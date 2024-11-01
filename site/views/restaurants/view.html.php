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
 * VikRestaurants restaurant reservation form view.
 * Within this view is displayed the form to start
 * the table booking process.
 *
 * @since 1.0
 */
class VikRestaurantsViewrestaurants extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$config = VREFactory::getConfig();

		$args = [];
		$args['date']    = $app->input->get('date', '', 'string');
		$args['hourmin'] = $app->input->get('hourmin', '', 'string');
		$args['people']  = $app->input->get('people', 0, 'uint');

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

		if (is_integer($args['date']))
		{
			// convert date timestamp to string
			$args['date'] = date($config->get('dateformat'), $args['date']);
		}
		
		/**
		 * An associative array containing the check-in details,
		 * such as: date, hourmin and people.
		 * 
		 * @var array
		 */
		$this->args = $args;

		/**
		 * Flag used to check whether the customer already agreed
		 * that all the guests belong to the same family.
		 * @see COVID-19
		 *
		 * @var bool
		 * @since 1.8
		 */
		$this->family = $app->getUserState('vre.search.family', false);

		/**
		 * The current menu item ID.
		 * 
		 * @var int|null
		 * @since 1.9
		 */
		$this->itemid = $app->input->get('Itemid', null, 'uint');

		// prepare page content
		VikRestaurants::prepareContent($this);
		
		// display the template
		parent::display($tpl);
	}
}
