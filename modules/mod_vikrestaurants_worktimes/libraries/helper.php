<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_worktimes
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class for VikRestaurants Worktimes module.
 *
 * @since 1.1.2
 */
class VikRestaurantsWorktimesHelper
{
	/**
	 * Use methods defined by modules trait for a better reusability.
	 *
	 * @see E4J\VikRestaurants\Module\ModuleHelper
	 */
	use E4J\VikRestaurants\Module\ModuleHelper;

	/**
	 * Get the working times of the current week, starting from today.
	 *
	 * @param   JRegistry  $params  The module configuration.
	 *
	 * @return  array      The week worktimes.
	 */
	public static function getDaysWorkTimes($params)
	{
		$group = $params->get('group', 1);

		$days = [];

		$date = getdate();

		for ($i = 0; $i < 7; $i++)
		{
			$days[] = self::getDetailsOnDay($date[0], $group);

			$date = getdate(mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']));
		}

		return $days;
	}

	/**
	 * Finds the specified working shift within the array.
	 *
	 * @param   array  $arr  The list of shifts.
	 * @param   int    $id   The shift ID to search.
	 *
	 * @return  mixed  The working shift on success, otherwise false. 
	 */
	public static function findWorkingShiftInArray($arr, $id)
	{
		foreach ($arr as $w)
		{
			if ($w['id'] == $id)
			{
				return $w;
			}
		}

		return false;
	}

	/**
	 * Recover the working details of the specified day.
	 *
	 * @param   int    $ts     The timestamp of the day.
	 * @param   int    $group  The section to look for (1: restaurant, 2: take-away).
	 *
	 * @return  array  The working details.
	 */
	public static function getDetailsOnDay($ts, $group = 1)
	{
		// take care of closing days
		$args = [
			'closure' => true,
		];

		/**
		 * Use native helper class to recover daily opening times.
		 * Use strict mode to recover a fictitious working shift
		 * in case of continuous opening times.
		 *
		 * @since 1.2
		 */
		$shifts = JHtml::fetch('vikrestaurants.shifts', $group, $ts, $strict = true, $args);

		// case to array for backward compatibility
		$shifts = array_map(function($sh)
		{
			return (array) $sh;
		}, $shifts);

		return [
			'timestamp' => $ts,
			'status' 	=> count($shifts),
			'shifts' 	=> $shifts,
		];
	}
}
