<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_search
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class for VikRestaurants Search module.
 *
 * @since 1.4.1
 */
class VikRestaurantsSearchHelper
{
	/**
	 * Use methods defined by modules trait for a better reusability.
	 *
	 * @see E4J\VikRestaurants\Module\ModuleHelper
	 */
	use E4J\VikRestaurants\Module\ModuleHelper;

	/**
	 * Returns the values in the query string.
	 *
	 * @return  array  The values.
	 */
	public static function getViewHtmlReferences()
	{
		$app = JFactory::getApplication();
		
		$args = [];

		$args['date'] = $app->input->getString('date');

		// check if we have arguments set in request
		if ($args['date'])
		{
			if (empty($args['hourmin']))
			{
				/**
				 * Find first available time for the given date.
				 *
				 * @since 1.7.4
				 */
				$args['hourmin'] = VikRestaurants::getClosestTime($args['date'], $next = true);
			}
		}
		else
		{
			/**
			 * Find first available time.
			 * The $date argument is passed by reference and it will
			 * be modified by the method, if needed.
			 *
			 * @since 1.7.4
			 */
			$args['date']    = null;
			$args['hourmin'] = VikRestaurants::getClosestTime($args['date'], $next = true);
		}

		/**
		 * In case date is an integer, convert the timestamp to a date string.
		 *
		 * @since 1.4.1
		 */
		if (is_numeric($args['date']))
		{
			$args['date'] = date(VREFactory::getConfig()->get('dateformat'), $args['date']);
		}

		$args['people'] = $app->input->getUint('people', 2);
		
		@list($args['hour'], $args['min']) = explode(':', $args['hourmin']);

		/**
		 * Flag used to check whether the customer already agreed
		 * that all the guests belong to the same family.
		 *
		 * @var   boolean
		 * @since 1.5
		 *
		 * @see   COVID-19
		 */
		$args['family'] = $app->getUserState('vre.search.family', false);
		
		return $args;
	}
}
