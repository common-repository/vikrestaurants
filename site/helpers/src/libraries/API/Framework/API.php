<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API\Framework;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\API as AbstractAPI;
use E4J\VikRestaurants\API\BlockEngine;
use E4J\VikRestaurants\API\Event;
use E4J\VikRestaurants\API\Response;
use E4J\VikRestaurants\API\User;
use E4J\VikRestaurants\API\Exception\BlockedUserException;
use E4J\VikRestaurants\API\Exception\UserAuthenticationException;
use E4J\VikRestaurants\DI\Container;

/**
 * API framework implementor.
 * This class is used to run all the registered plugins.
 *
 * All the events are runnable only if the user is correctly authenticated.
 *
 * @since 1.9
 */
class API extends AbstractAPI
{
	/**
	 * @inheritDoc
	 */
	public function __construct(Container $container, BlockEngine $blockEngine, array $config = [])
	{
		// invoke parent first
		parent::__construct($container, $blockEngine, $config);

		// make sure the API framework is enabled
		if (!\VREFactory::getConfig()->getBool('apifw'))
		{
			// disable API
			$this->disable();
		}
	}

	/**
	 * @inheritDoc
	 * 
	 * The credentials of the user are stored in the database.
	 * This method can raise the following internal errors:
	 * 
	 * - 103 = The username and password do not match
	 * - 104 = This account is blocked
	 * - 105 = The source IP is not authorised
	 * 
	 * @throws UserAuthenticationException
	 * @throws BlockedUserException
	 */
	protected function doConnection(User $user)
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true);

		// get login that matches with the credentials provided
		$query->select('*')
			->from($db->qn('#__vikrestaurants_api_login'))
			->where($db->qn('username') . ' = ' . $db->q($user->getUsername()))
			->where('BINARY ' . $db->qn('password') . ' = ' . $db->q($user->getPassword()));

		$db->setQuery($query, 0, 1);
		
		// load login
		$login = $db->loadObject();

		if (!$login)
		{
			// login doesn't match
			throw new UserAuthenticationException($user, 'Authentication Error! The username and password do not match.', 103);
		}

		// check if login account is still active
		if (!$login->active)
		{
			// login blocked
			throw new BlockedUserException($user);
		}

		// Check if user IP address is in the list of the allowed IPs.
		// If there are no IPs specified, all addresses are allowed.
		if ($login->ips)
		{
			// decode IPs from JSON string
			$allowedList = (array) json_decode($login->ips, true);

			if ($allowedList && !in_array($user->getSourceIp(), $allowedList))
			{
				// login doesn't match
				throw new UserAuthenticationException($user, 'Authentication Error! The IP [' . $user->getSourceIp() . '] is not authorised.', 105);
			}
		}

		// login successful
		$user->assign($login->id);

		return true;
	}

	/**
	 * @inheritDoc
	 * 
	 * This log is registered in the database and it is visible only from the administrator.
	 */
	protected function registerEvent(Event $event = null, Response $response = null)
	{
		$log     = '';
		$status  = 2;
		$id_user = $this->isConnected() ? $this->getUser()->id() : 0;
		$ip      = $this->isConnected() ? $this->getUser()->getSourceIp() : null;

		// if the event is not empty : register it
		if ($event !== null)
		{
			$log .= 'Event: ' . $event->getName() . "\n";
		}

		// if the response is not empty : register it and evaluate the status
		if ($response !== null)
		{
			$log .= $response->getContent();

			$status = $response->isVerified() ? 1 : 0;
		}

		if (empty($log))
		{
			// if the evaluated log is still empty
			if ($id_user > 0)
			{
				// try to register the details of the user
				$log = 'User [' . $this->getUser()->getUsername() . '] login @ ' . \JHtml::fetch('date', 'now', 'Y-m-d H:i:s', \JFactory::getApplication()->get('offset', 'UTC'));
			}
			else
			{
				// otherwise register a "unrecognised" response
				$log = 'Unable to recognize the response';
			}
		}

		// prepare log data
		$data = [
			'id'       => 0,
			'id_login' => $id_user,
			'status'   => $status,
			'content'  => $log,
			'payload'  => $response->getPayload(),
		];

		// save log through model
		return (bool) \JModelVRE::getInstance('apilog')->save($data);
	}

	/**
	 * @inheritDoc
	 */
	protected function updateUserManifest()
	{
		if ($this->getUser() === null)
		{
			return false;
		}

		// prepare user data
		$data = [
			'id'         => $this->getUser()->id(),
			'last_login' => \VikRestaurants::now(),
		];

		// save manifest through model
		return \JModelVRE::getInstance('apiuser')->save($data);
	}

	/**
	 * @inheritDoc
	 */
	public function output($data, $type = 'application/json')
	{
		if (!is_null($data))
		{
			$app = \JFactory::getApplication();

			// check whether the output requires a specific content type
			// and make sure the headers haven't been already sent
			if ($type && $this->sendHeaders)
			{
				// set content type and send the headers
				$app->setHeader('Content-Type', $type);
				$app->sendHeaders();

				// lock headers sending
				$this->sendHeaders = false;
			}
		
			// try to stringify an object in case of JSON content type
			if (!is_string($data) && preg_match("/json/i", $type))
			{
				$data = json_encode($data);
			}

			echo $data;
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function loadEventConfig(string $eventName, User $user = null)
	{
		$options = [];

		if (!$user)
		{
			// make sure we have a logged-in user
			if (!$this->isConnected())
			{
				// nope, return an empty array...
				return $options;
			}

			// use currently connected user
			$user = $this->getUser();
		}

		/** @var \JModelLegacy */
		$model = \JModelVRE::getInstance('apiuseroptions');

		// load options related to the specified ID and event
		return $model->getOptions($user->id(), $eventName);
	}

	/**
	 * @inheritDoc
	 */
	protected function saveEventConfig(Event $event, User $user = null)
	{
		$options = $event->getOptions();

		if (!$options)
		{
			// empty configuration, do not need to go ahead
			return true;
		}

		if (!$user)
		{
			// make sure we have a logged-in user
			if (!$this->isConnected())
			{
				// nope, saving failed
				return false;
			}

			// use currently connected user
			$user = $this->getUser();
		}

		/** @var \JModelLegacy */
		$model = \JModelVRE::getInstance('apiuseroptions');

		// set up data to bind
		$data = [
			'id_login' => $user->id(),
			'id_event' => $event->getName(),
			'options'  => $event->getOptions(),
		];

		// store options
		return $model->save($data);
	}
}
