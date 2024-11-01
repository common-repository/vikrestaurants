<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants restaurant room map model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMap extends JModelVRE
{
	/** @var object[] */
	protected static $rooms = null;

	/**
	 * Returns a list of rooms with all the assigned tables.
	 * The records are statically cached. Therefore, calling this
	 * method more than once won't lead to duplicate queries.
	 * 
	 * @return  object[]
	 */
	public function getRooms()
	{
		if (is_null(static::$rooms))
		{
			static::$rooms = [];

			$db = JFactory::getDbo();

			$q = $db->getQuery(true)
				->select('t.*')
				->select($db->qn('r.name', 'room_name'))
				->select($db->qn('r.graphics_properties'))
				->from($db->qn('#__vikrestaurants_room', 'r'))
				->join('INNER', $db->qn('#__vikrestaurants_table', 't') . ' ON ' . $db->qn('r.id') . ' = ' . $db->qn('t.id_room'))
				->order($db->qn('r.ordering') . ' ASC')
				->order($db->qn('t.name') . ' ASC');
			
			$db->setQuery($q);
			
			foreach ($db->loadObjectList() as $r)
			{
				if (!isset(static::$rooms[$r->id_room]))
				{
					$room = new stdClass;
					$room->id   = $r->id_room;
					$room->name = $r->room_name;

					// decode room design data
					$room->graphics_properties = $r->graphics_properties ? json_decode($r->graphics_properties) : new stdClass;

					$room->tables = [];

					// cache room details
					static::$rooms[$r->id_room] = $room;
				}

				// decode table design data
				$r->design_data = json_decode($r->design_data);

				// inject table within the registered room
				static::$rooms[$r->id_room]->tables[] = $r;
			}
		}

		// clone the room to allow the manipulation of the cached objects
		return array_map(function($room)
		{
			return clone $room;
		}, static::$rooms);
	}

	/**
	 * Obtain the details of the selected room.
	 * In case of specified filters (date & hourmin), the system will check
	 * whether the room is closed or not for the provided check-in.
	 * 
	 * @param   int    $roomId   The ID of the room.
	 * @param   array  $filters  An array of check-in filters.
	 * 
	 * @return  object
	 * 
	 * @throws  Exception
	 */
	public function getRoom(int $roomId, array $filters = [])
	{
		/** @var object[] */
		$rooms = $this->getRooms();

		if (!$rooms)
		{
			// no rooms found
			throw new RuntimeException(JText::translate('VRROOMMISSINGERROR'), 400);
		}

		if ($roomId <= 0)
		{
			// always use the first room available
			$roomId = (int) reset($rooms);
		}
		else if (!isset($rooms[$roomId]))
		{
			// the provided room does not exist
			throw new UnexpectedValueException('Room [' . $roomId . '] not found', 404);
		}

		if ($filters)
		{
			if ((!isset($filters['hour']) || !isset($filters['min'])) && !empty($filters['hourmin']))
			{
				list($filters['hour'], $filters['min']) = explode(':', $filters['hourmin']);
			}

			// calculate check-in timestamp
			$res_ts = VikRestaurants::createTimestamp($filters['date'], $filters['hour'], $filters['min']);

			$db = JFactory::getDbo();

			// check whether the specified room is closed or not
			$query = $db->getQuery(true)
				->select(1)
				->from($db->qn('#__vikrestaurants_room_closure', 'rc'))
				->where([
					$db->qn('rc.id_room') . ' = ' . $roomId,
					$db->qn('rc.start_ts') . ' <= ' . $res_ts,
					$db->qn('rc.end_ts') . ' > ' . $res_ts,
				]);

			$db->setQuery($query);
			$rooms[$roomId]->isClosed = (bool) $db->loadResult();
		}

		return $rooms[$roomId];
	}

	/**
	 * Creates a map renderer.
	 * 
	 * @param   int    $roomId   The ID of the room.
	 * @param   array  $filters  An array of check-in filters.
	 * @param   array  $options  The map configuration.
	 * 
	 * @return  VREMapFactory
	 */
	public function createMapRenderer(int $roomId, array $filters, array $options = [])
	{
		/** @var object */
		$room = $this->getRoom($roomId, $filters);

		// Create availability search.
		// Do not use ADMIN permissions to properly display the tables as unavailable
		// when they are unpublished or in case they belong to a closed room.
		// Even if the tables are unavailable, they are still bookable through the MAPS view.
		$search = new VREAvailabilitySearch($filters['date'], $filters['hourmin'], $filters['people'], $admin = false);

		/**
		 * Force usage of the specified time of stay.
		 *
		 * @since 1.8.2
		 */
		if (isset($filters['staytime']))
		{
			$search->setStayTime($filters['staytime']);
		}

		// get all available tables (exclude current reservation ID, if any)
		$availableTables = $search->getAvailableTables($filters['id_res'] ?? null);
		
		// calculates tables occurrence
		$allSharedTablesOccurrency = $search->getTablesOccurrence();

		// get current reservations
		$currentReservations = $search->getReservations();

		// use local time
		$now = VikRestaurants::now();

		$config = VREFactory::getConfig();

		// fetch current reservations
		foreach ($currentReservations as $res)
		{
			if (empty($res->stay_time))
			{
				$res->stay_time = $config->getUint('averagetimestay');
			}

			$res->checkin_date = date($config->get('dateformat'), $res->checkin_ts);
			$res->checkin_time = date($config->get('timeformat'), $res->checkin_ts);
			$res->checkin      = $res->checkin_date . ' ' . $res->checkin_time;

			if ($res->checkin_ts <= $now && $now < $res->checkin_ts + (int) $res->stay_time * 60)
			{
				$res->time_left = $res->checkin_ts + $res->stay_time * 60 - $now;
			}

			// strip HTML from notes
			$res->notes = strip_tags((string) $res->notes);
		}

		// configure table availability and assigned reservations
		foreach ($room->tables as $table)
		{
			$found = false;

			for ($j = 0, $m = count($availableTables); $j < $m && !$found; $j++)
			{
				$found = $availableTables[$j]->id == $table->id;
			}
			
			$table->available = $found || (!empty($options['ignore_availability']) && $options['ignore_availability'] !== 'false');

			if (isset($allSharedTablesOccurrency[$table->id]))
			{
				$table->occurrency = $allSharedTablesOccurrency[$table->id];
			}
			else
			{
				$table->occurrency = 0;
			}

			// reset reservations
			$table->reservations = [];
			
			$found = false;

			for ($j = 0; $j < count($currentReservations) && !$found; $j++)
			{
				if ($table->id == $currentReservations[$j]->id_table)
				{
					$table->reservations[] = $currentReservations[$j];
					
					if ($table->multi_res == 0)
					{
						// stop only in case of non shared table
						$found = true;

						// update occupancy
						$table->occurrency = (int) $currentReservations[$j]->people;
					}
				}
			}
		}

		// inject filters within the factory options
		$options['filters'] = $filters;

		VRELoader::import('library.map.factory');

		// set up map factory
		return VREMapFactory::getInstance($options)->setRoom($room);
	}
}
