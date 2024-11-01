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
 * VikRestaurants maps view.
 *
 * @since 1.0
 */
class VikRestaurantsViewmaps extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		/**
		 * Loads dedicated CSS file.
		 *
		 * @since 1.7.4
		 */
		VREApplication::getInstance()->addStyleSheet(VREASSETS_URI . 'css/oversight.css');

		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		$dbo 	= JFactory::getDbo();
		$config = VREFactory::getConfig();
		
		// set the toolbar
		$this->addToolBar();

		$id_room = $app->getUserStateFromRequest('vre.maps.selectedroom', 'selectedroom', 0, 'uint');

		$_df = $app->getUserStateFromRequest('vre.maps.datefilter', 'datefilter', '', 'string');
		$_hm = $app->getUserStateFromRequest('vre.maps.hourmin', 'hourmin', '', 'string');
		$_pl = $app->getUserStateFromRequest('vre.maps.people', 'people', 1, 'uint');
		
		$_df_ts = VikRestaurants::createTimestamp($_df, 0, 0);

		if (strlen($_df) == 0 || $_df_ts == -1)
		{
			$_df_ts = VikRestaurants::now();
		}

		$_df = date($config->get('dateformat'), $_df_ts);
		
		$_hm_exp = explode(':', $_hm);

		$args = array(
			'date'    => $_df,
			'hourmin' => $_hm,
		);

		if (count($_hm_exp) != 2 || !VikRestaurants::isHourBetweenShifts($args, 1))
		{
			$_hm = VikRestaurants::getFirstAvailableHour();
			$_hm_exp = explode(':', $_hm);
		}
		
		if ($config->getUint('minimumpeople') > $_pl || $config->getUint('maximumpeople') < $_pl)
		{
			$_pl = max(2, $config->getUint('minimumpeople')); // 2 or higher
		}
		
		$filters = array(
			'date'    => $_df,
			'hourmin' => $_hm,
			'people'  => $_pl,
			'hour'    => $_hm_exp[0],
			'min'     => $_hm_exp[1],
		);

		/**
		 * Find closest time for current day.
		 * Only if the time wasn't submitted through the form.
		 *
		 * @since 1.7.4
		 */
		if (JFactory::getDate()->format($config->get('dateformat')) == $filters['date']
			&& VikRestaurants::isTimePast($filters)
			&& !$input->getBool('formsubmitted'))
		{
			// same day, try to fetch the closest time
			$tmp = VikRestaurants::getClosestTime();

			if ($tmp)
			{
				// new hours and minutes, update $filters
				$_hm_exp = explode(':', $tmp);

				$filters['hourmin'] = $tmp;
				$filters['hour'] 	= $_hm_exp[0];
				$filters['min']  	= $_hm_exp[1];
			}
		}
		
		$rooms = [];
		
		$res_ts = VikRestaurants::createTimestamp($filters['date'], $filters['hour'], $filters['min']);
		
		$q = "SELECT `rm`.*, (
			SELECT COUNT(1)
			FROM `#__vikrestaurants_room_closure` AS `rc`
		 	WHERE `rc`.`id_room`=`rm`.`id` AND `rc`.`start_ts` <= $res_ts AND $res_ts < `rc`.`end_ts` LIMIT 1
		) AS `isClosed` 
		FROM `#__vikrestaurants_room` AS `rm`
		ORDER BY `rm`.`ordering`";

		$dbo->setQuery($q);

		foreach ($dbo->loadObjectList() as $room)
		{
			$room->graphics_properties = $room->graphics_properties ? json_decode($room->graphics_properties) : new stdClass;

			$rooms[] = $room;
		}

		/**
		 * Always use the first room available.
		 *
		 * @since 1.7.4
		 */
		if ($id_room <= 0 && $rooms)
		{
			$id_room = $rooms[0]->id;
		}
		
		$allRoomTables = [];
		
		if ($id_room > 0)
		{
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikrestaurants_table'))
				->where($dbo->qn('id_room') . ' = ' . $id_room);

			$dbo->setQuery($q);

			foreach ($dbo->loadObjectList() as $table)
			{
				$table->design_data = json_decode($table->design_data);

				$allRoomTables[] = $table;
			}
		} 

		// Create availability search.
		// Do not use ADMIN permissions to properly display the tables as unavailable
		// when they are unpublished or in case they belong to a closed room.
		// Even if the tables are unavailable, they are still bookable through the MAPS view.
		$search = new VREAvailabilitySearch($filters['date'], $filters['hourmin'], $filters['people'], $admin = false);

		// get all available tables
		$rows = $search->getAvailableTables();
		
		// calculates tables occurrence
		$allSharedTablesOccurrency = $search->getTablesOccurrence();

		// get current reservations
		$current_res = $search->getReservations();

		// use local time
		$now = VikRestaurants::now();

		foreach ($current_res as $res)
		{
			if (empty($res->stay_time))
			{
				$res->stay_time = $config->getUint('averagetimestay');
			}

			$res->checkin_date = date($config->get('dateformat'), $res->checkin_ts);
			$res->checkin_time = date($config->get('timeformat'), $res->checkin_ts);
			$res->checkin 	   = $res->checkin_date . ' ' . $res->checkin_time;

			if ($res->checkin_ts <= $now && $now < $res->checkin_ts + (int) $res->stay_time * 60)
			{
				$res->time_left = $res->checkin_ts + $res->stay_time * 60 - $now;
			}

			// strip HTML from notes
			$res->notes = strip_tags((string) $res->notes);
		}
		
		$this->rooms                     = $rooms;
		$this->tables                    = $allRoomTables;
		$this->selectedRoomId            = $id_room;
		$this->filters                   = $filters;
		$this->reservationTableOnDate    = $rows;
		$this->allSharedTablesOccurrency = $allSharedTablesOccurrency;
		$this->currentReservations       = $current_res;

		// display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	private function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWMAPS'), 'vikrestaurants');
		
		if (JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::custom('map.edit', 'edit', 'edit', JText::translate('VREDIT'), false);
		}
	}
}
