<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API\Plugins;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\Event;
use E4J\VikRestaurants\API\Response;

/**
 * Event used to book a table for the searched date, time and number of people.
 *
 * @since 1.7
 * @since 1.9  Renamed from BookTable.
 */
class BookTablePluginAPI extends Event
{
	/**
	 * @inheritDoc
	 * 
	 * @todo Consider to refactor the method by using an apposite site model
	 *       to validate the integrity of the request.
	 */
	protected function execute(array $args, Response $response)
	{
		if (!$args)
		{
			$input = \JFactory::getApplication()->input;
			
			// no payload found, recover arguments from request
			$args = [];
			$args['date']     = $input->getString('date');
			$args['hourmin']  = $input->getString('hourmin');
			$args['people']   = $input->getUint('people');
			$args['id_table'] = $input->getInt('id_table', 0);
		}
		
		try
		{
			/**
			 * Check the table availability through a different API plugin.
			 *
			 * @see TableAvailablePluginAPI
			 */
			$json = \VREFactory::getAPI()->dispatch('tableavailable', $args);
		}
		catch (\Exception $e)
		{
			// register response here
			$response->setContent($e->getMessage());
			// propagate exception
			throw $e;
		}

		// decode response
		$res = json_decode($json);

		if (!isset($res->status) || !$res->status || isset($res->errcode))
		{
			/*
			we got a json like:
			{
				status: 0
			}

			or:
			{
				errcode: 500,
				error: "something wrong"
			}
			*/

			if (isset($res->status))
			{
				// table not available, register response
				$response->setStatus(1)->setContent($res->message);
			}
			else
			{
				// register error
				$response->setContent($res->error);
			}

			// propagate the same message we got
			return $json;
		}

		// table is available
		$response->setStatus(1);

		// extract hour and minutes
		list($args['hour'], $args['min']) = explode(':', $args['hourmin']);
			
		$args['id_table'] = $res->table;

		$model = \JModelVRE::getInstance('reservation');

		$data = [];
		// fetch check-in timestamp
		$data['checkin_ts'] = \VikRestaurants::createTimestamp($args['date'], $args['hour'], $args['min']);
		// set number of people
		$data['people'] = $args['people'];
		// assign table
		$data['id_table'] = $args['id_table'];
		// use current language tag
		$data['langtag'] = \JFactory::getLanguage()->getTag();

		// fill customer details
		$this->assignCustomer($data, $args);

		// fetch the correct status to use
		$this->assignStatusRole($data, $args);

		// register notes, if any
		$this->assignNotes($data, $args);

		/**
		 * When explicitly specified by the requestor, send a notification e-mail to the customer.
		 * 
		 * @since 1.9.1
		 */
		if (!empty($args['notify']))
		{
			$data['notifycust'] = true;
		}

		$obj = new \stdClass;

		// save reservation
		$id = $model->save($data);

		if ($id)
		{
			$obj->status = 1;
			$obj->oid    = $id;
			$obj->date   = $args['date'];
			$obj->time   = $args['hourmin'];
			$obj->people = $args['people'];
			$obj->table  = $args['id_table'];

			/**
			 * Register the details of the saved reservation.
			 *
			 * @since 1.8
			 */
			$obj->details = $model->getData();

			/**
			 * Include the role of the status currently set.
			 * 
			 * @since 1.9.1
			 */
			$obj->statusrole = \JHtml::fetch('vrehtml.status.role', 'restaurant', $obj->details['status']);

			if ($obj->statusrole === 'PENDING')
			{
				$obj->selfconfirm = \VREFactory::getConfig()->getBool('selfconfirm');
			}

			/**
			 * Include the URL of the reservation within the response.
			 * 
			 * @since 1.9.1
			 */
			$obj->url = $this->createReservationUrl($obj->details);
		}
		else
		{
			// get string error
			$error = $model->getError(null, true);

			$obj->status  = 0;
			$obj->message = $error ?: \JText::translate('VRNEWQUICKRESNOTCREATED');
		}

		/**
		 * Let the application framework safely outputs the response.
		 *
		 * @since 1.8.4
		 */
		return $obj;
	}

	/**
	 * Recovers the customer data from the request.
	 *
	 * @param   array   &$data  The reservation details.
	 * @param   array   $args   The event arguments.
	 *
	 * @return  void
	 */
	private function assignCustomer(array &$data, array $args)
	{
		if (isset($args['purchaser']))
		{
			$purchaser = $args['purchaser'];
		}
		else
		{
			$input = \JFactory::getApplication()->input;

			$purchaser = $input->get('purchaser', [], 'array');
		}

		$customerData = [];

		if (isset($purchaser['name']))
		{
			$customerData['purchaser_nominative'] = $purchaser['name'];
		}
		else
		{
			$customerData['purchaser_nominative'] = '';
		}

		if (isset($purchaser['mail']))
		{
			$customerData['purchaser_mail'] = $purchaser['mail'];
		}
		else
		{
			$customerData['purchaser_mail'] = '';
		}

		if (isset($purchaser['phone']))
		{
			$customerData['purchaser_phone'] = preg_replace("/[^0-9\s+]+/", '', $purchaser['phone']);
		}
		else
		{
			$customerData['purchaser_phone'] = '';
		}

		if (isset($purchaser['country']))
		{
			$customerData['purchaser_country'] = $purchaser['country'];
		}
		else
		{
			$customerData['purchaser_country'] = \E4J\VikRestaurants\CustomFields\Helpers\FieldsHelper::getDefaultCountryCode();
		}

		if (isset($purchaser['prefix']))
		{
			$customerData['purchaser_prefix'] = $purchaser['prefix'];
		}
		else
		{
			// get selected country
			$country = \JHtml::fetch('vrehtml.countries.withcode', $customerData['purchaser_country']);

			if ($country)
			{
				$customerData['purchaser_prefix'] = $country->dial;
			}
			else
			{
				$customerData['purchaser_prefix'] = '';
			}
		}

		/**
		 * Assign user according to the specified e-mail.
		 *
		 * @since 1.8
		 */
		if ($customerData['purchaser_mail'])
		{
			$customerModel = \JModelVRE::getInstance('customer');

			/** @var \stdClass|null */
			$customer = $customerModel->getItem(['billing_mail' => $customerData['purchaser_mail']]);

			if ($customer)
			{
				$data['id_user'] = $customer->id;	
			}
			else
			{
				/**
				 * Create the customer right now.
				 * 
				 * @since 1.9.1
				 */
				$data['id_user'] = $customerModel->save($customerData);
			}
		}

		// add customer info to the reservation details
		$data = array_merge($data, $customerData);
	}

	/**
	 * Recovers the status from the request.
	 * If not provided, the default one configured by the system will be used.
	 *
	 * @param   array   &$data  The reservation details.
	 * @param   array   $args   The event arguments.
	 *
	 * @return  void
	 * 
	 * @since   1.9.1
	 */
	private function assignStatusRole(array &$data, array $args)
	{
		if (isset($args['status']))
		{
			$status = (string) $args['status'];
		}
		else
		{
			$status = \JFactory::getApplication()->input->get('status', '');
		}

		switch (strtoupper($status))
		{
			case 'PENDING':
				$status = \JHtml::fetch('vrehtml.status.pending', 'restaurant', 'code');
				break;

			case 'APPROVED':
				$status = \JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code');
				break;

			default:
				$status = \VREFactory::getConfig()->getString('defstatus');
		}

		$data['status'] = $status;
	}

	/**
	 * Recovers the notes from the request.
	 *
	 * @param   array   &$data  The reservation details.
	 * @param   array   $args   The event arguments.
	 *
	 * @return  void
	 * 
	 * @since   1.9.1
	 */
	private function assignNotes(array &$data, array $args)
	{
		if (isset($args['notes']))
		{
			$data['notes'] = (string) $args['notes'];
		}
		else
		{
			$data['notes'] = \JFactory::getApplication()->input->getString('notes', '');
		}
	}

	/**
	 * Constructs the URL to access the summary page of the reservation.
	 * 
	 * @param   array   $data  The reservation details.
	 * 
	 * @return  string  The URL of the reservation.
	 * 
	 * @since   1.9.1
	 */
	protected function createReservationUrl(array $data)
	{
		// route the URL for external usage
		return \VREFactory::getPlatform()->getUri()->route(
			"index.php?option=com_vikrestaurants&view=reservation&ordnum={$data['id']}&ordkey={$data['sid']}",
			$xhtml = false
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		/**
		 * @todo translate
		 */
		return 'Book a Table';
	}

	/**
	 * @inheritDoc
	 */
	public function getShortDescription()
	{
		/**
		 * @todo translate
		 */
		return 'Book a table for a certain date, time and number of people.';
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		/**
		 * Read the description HTML from a layout.
		 *
		 * @since 1.8
		 */
		return \JLayoutHelper::render('apis.plugins.book_table', ['plugin' => $this]);
	}
}
