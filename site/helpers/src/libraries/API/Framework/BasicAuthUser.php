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

use E4J\VikRestaurants\API\Event;
use E4J\VikRestaurants\API\User;

/**
 * API BASIC AUTH user login implementor.
 * This class is used by the framework to connect the users and to authorise the events.
 *
 * @see User
 * @see Events
 *
 * @since 1.9
 */
class BasicAuthUser extends User
{
	/**
	 * @inheritDoc
	 */
	public function __construct(string $username = null, string $password = null, string $ip = null)
	{
		// tries to recover username and password through HTTP BASIC AUTH headers
		if (!$username && !$password)
		{
			// access server superglobal
			$server = \JFactory::getApplication()->input->server;

			// try to extract username and password from headers
			$username = $server->getString('PHP_AUTH_USER', '');
			$password = $server->getString('PHP_AUTH_PW', '');
		}

		// construct through parent
		parent::__construct((string) $username, (string) $password, $ip);
	}

	/**
	 * @inheritDoc
	 */
	public function authorise(Event $event)
	{
		// make sure the user is not connected and the event is provided
		if (!$this->id() || $event === null)
		{
			return false;
		}

		// check whether the event is always allowed (such as "connection ping")
		if ($event->alwaysAllowed())
		{
			return true;
		}

		/** @var \stdClass fetch the record of the logged in user */
		$loginItem = \JModelVRE::getInstance('apiuser')->getItem($this->id());

		// make sure the specified event is not included in the list of the denied plugins
		return !in_array($event->getName(), $loginItem->denied);
	} 

	/**
	 * @inheritDoc
	 * 
	 * The provided username is valid whether all the conditions below are verified:
	 * 
	 * - it can contain only letters, numbers, underscores or dots (no white spaces);
	 * - its length is between 3 and 128 characters.
	 */
	protected function isUsernameAccepted(string $username)
	{
		// [0-9A-Za-z._]	- accepted characters
		// {3,128}			- have to be 3-128 characters
		// 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz._
		return preg_match("/^[0-9A-Za-z._]{3,128}$/", $username);
	}

	/**
	 * @inheritDoc
	 * 
	 * The provided password is valid whether all the conditions below are verified:
	 * 
	 * - it can contain only letters, numbers or these !?@#$%{}[]()_-. symbols;
	 * - its length is between 8 and 128 characters;
	 * - it contains at least one number;
	 * - it contains at least one letter.
	 */
	protected function isPasswordAccepted(string $password)
	{
		// (?=.*\d) 					- at least one number
		// (?=.*[A-Za-z]) 				- at least one letter
		// [0-9A-Za-z!@#$%{}[]()_-.]	- accepted characters
		// {8,128}						- have to be 8-128 characters
		// 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!?@#$%{}[]()_-.
		return preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!?@#$%_.\-{\[()\]}]{8,128}$/', $password);
	}

	/**
	 * @inheritDoc
	 * 
	 * Do NOT apply any hashing because the password must be stored as it is.
	 */
	protected function hashMask(string $password)
	{
		// no hash mask applied
		return $password;
	}
}
