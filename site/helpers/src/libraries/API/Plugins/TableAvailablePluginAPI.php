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
 * Event used to check the availability of the tables.
 *
 * @since 1.7
 * @since 1.9  Renamed from TableAvailable.
 */
class TableAvailablePluginAPI extends Event
{
	/**
	 * @inheritDoc
	 * 
	 * @todo Consider to refactor the method by using an apposite site model
	 *       to validate the integrity of the request.
	 */
	protected function execute(array $args, Response $response)
	{
		$input = \JFactory::getApplication()->input;

		if (!$args)
		{
			// get booking arguments from request
			$args = [];
			$args['date']     = $input->getString('date');
			$args['hourmin']  = $input->getString('hourmin');
			$args['people']   = $input->getUint('people');
			$args['id_table'] = $input->getInt('id_table', 0);
		}
		else
		{
			// do not init a default value for the other arguments because
			// VikRestaurants::isRequestReservationValid() will check the
			// request integrity for us
			$args['id_table'] = isset($args['id_table']) ? (int) $args['id_table'] : 0;
		}

		// validate request
		$code = \VikRestaurants::isRequestReservationValid($args);

		if ($code !== 0)
		{
			// fetch error message from code
			$error = \VikRestaurants::getResponseFromReservationRequest($code);
			// register response
			$response->setContent(\JText::translate($error));
			
			// bad request, throw exception
			throw new \Exception($response->getContent(), 400);
		}

		// from now on the result should be ok even if there are no available tables
		$response->setStatus(1);

		// prepare response object for client
		$obj = new \stdClass;
		$obj->status = 0;
		$obj->specialday = false;

		// extract hour and minutes
		list($args['hour'], $args['min']) = explode(':', $args['hourmin']);
		
		// get checkin timestamp
		$checkin_ts = \VikRestaurants::createTimestamp($args['date'], $args['hour'], $args['min']);

		// make sure the reservations are allowed for the selected date time
		if (!\VikRestaurants::isReservationsAllowedOn($checkin_ts))
		{
			// reservation blocked for today
			$obj->message = \JText::translate('VRNOMORERESTODAY');
			// register response
			$response->setContent($obj->message);

			/**
			 * Let the application framework safely output the response.
			 *
			 * @since 1.8.4
			 */
			return $obj;
		}

		// init special days manager
		$sdManager = new \VRESpecialDaysManager('restaurant');
		// set checkin date
		$sdManager->setStartDate($args['date']);
		// set checkin time
		$sdManager->setCheckinTime($args['hourmin']);

		// get first available special day
		$specialDay = $sdManager->getFirst();

		if ($specialDay)
		{
			/**
			 * Include in the response if we are checking the availability for
			 * an important special day.
			 * 
			 * @since 1.9.1
			 */
			$obj->specialday = $specialDay->markoncal && $specialDay->ignoreClosingDays;

			// make sure we haven't reached the threshold of allowed people
			if ($specialDay->canHostPeople($args) == false)
			{
				// unable to host the requested party size
				$obj->message = \JText::translate('VRRESNOSINGTABLEFOUND');
				// register response
				$response->setContent($obj->message);

				/**
				 * Let the application framework safely output the response.
				 *
				 * @since 1.8.4
				 */
				return $obj;
			}

			// check if we should ignore closing days
			$ignore_cd = $specialDay->ignoreClosingDays;
		}
		else
		{
			// never ignore closing days
			$ignore_cd = false;
		}
		
		// check if we have a closing day for the selected checkin date
		if (!$ignore_cd && \VikRestaurants::isClosingDay($args))
		{
			// the selected day is closed
			$obj->message = \JText::translate('VRSEARCHDAYCLOSED');
			// register response
			$response->setContent($obj->message);

			/**
			 * Let the application framework safely output the response.
			 *
			 * @since 1.8.4
			 */
			return $obj;
		}

		// create availability search object
		$search = new \VREAvailabilitySearch($args['date'], $args['hourmin'], $args['people']);

		if ($args['id_table'])
		{
			// check availability for the selected table
			if ($search->isTableAvailable($args['id_table']))
			{
				$obj->status = 1;
				$obj->table  = $args['id_table'];

				/**
				 * Return the details of the table.
				 *
				 * @since 1.8
				 */
				$obj->details = $search->getTable($args['id_table']);

				/**
				 * Construct the URL to reach to complete the booking process.
				 * 
				 * @since 1.9.1
				 */
				$obj->url = $this->createBookingUrl($args);
			}
			else
			{
				// the selected table is not available
				$obj->message = \JText::translate('VRTNOTAVAILABLE');

				/**
				 * Pass the suggested times to the caller.
				 * 
				 * @since 1.9.1
				 */
				$obj->hints = $this->createHints($search);
			}
		}
		else
		{
			// get all available tables
			$tables = $search->getAvailableTables();

			if (count($tables))
			{
				// register first table found
				$obj->status = 1;
				$obj->table  = $tables[0]->id;

				/**
				 * Return a list containing all the available tables.
				 *
				 * @since 1.8
				 */
				$obj->list = $tables;

				/**
				 * Construct the URL to reach to complete the booking process.
				 * 
				 * @since 1.9.1
				 */
				$obj->url = $this->createBookingUrl($args);
			}
			else
			{
				// no tables available
				$obj->message = \JText::translate('VRRESNOSINGTABLEFOUND');

				/**
				 * Pass the suggested times to the caller.
				 * 
				 * @since 1.9.1
				 */
				$obj->hints = $this->createHints($search);
			}
		}

		/**
		 * Let the application framework safely output the response.
		 *
		 * @since 1.8.4
		 */
		return $obj;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		/**
		 * @todo translate
		 */
		return 'Table Availability';
	}

	/**
	 * @inheritDoc
	 */
	public function getShortDescription()
	{
		/**
		 * @todo translate
		 */
		return 'Check whether a table is available for a certain date, time and number of people.';
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
		return \JLayoutHelper::render('apis.plugins.table_available', ['plugin' => $this]);
	}

	/**
	 * Constructs the URL to reach to complete the booking process.
	 * 
	 * @param   array   $args  The search arguments provided by the client.
	 * 
	 * @return  string  The URL for direct booking.
	 * 
	 * @since   1.9.1
	 */
	protected function createBookingUrl(array $args)
	{
		// In case the system requires the selection of a menu, do not skip the
		// search process, as the users are required to select the dishes that
		// they are going to eat.
		$view = ($specialDay->chooseMenu ?? false) ? 'search' : 'confirmres';

		$queryString = [
			'date'    => $args['date'],
			'hourmin' => $args['hourmin'],
			'people'  => $args['people'],
		];

		if ($args['id_table'])
		{
			// preserve the selected table
			$queryString['table'] = $args['id_table'];
		}
		else if (\VREFactory::getConfig()->getUint('reservationreq') != 2)
		{
			// table selection mandatory, go to the search page
			$view = 'search';
		}

		// route the URL for external usage
		return \VREFactory::getPlatform()->getUri()->route("index.php?option=com_vikrestaurants&view={$view}&" . http_build_query($queryString), $xhtml = false);
	}

	/**
	 * Elaborates the time hints.
	 * The method will return the first N available times before
	 * the selected check-in time and the next 2. Such as:
	 * 12:00 | 12:30 | CURRENT | 13:30 | 14:30
	 * 
	 * @param   VREAvailabilitySearch  $search
	 * @param   int  $n
	 * 
	 * @return  array
	 * 
	 * @since   1.9.1
	 */
	protected function createHints($search, $n = 2)
	{
		$hints = $search->getSuggestedTimes($n);

		// get rid of the empty times
		$hints = array_values(array_filter($hints));

		foreach ($hints as $hint)
		{
			$hint->date = \JFactory::getDate(date('Y-m-d H:i', $hint->ts), \JFactory::getApplication()->get('offset', 'UTC'))->toISO8601(true);
			unset($hint->ts);
		}

		return $hints;
	}
}
