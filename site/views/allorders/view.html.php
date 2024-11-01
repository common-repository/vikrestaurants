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
 * VikRestaurants global profile view.
 * Here the customers can log in to see the history
 * of reservations/orders made.
 *
 * @since 1.5
 */
class VikRestaurantsViewallorders extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();

		$this->user = JFactory::getUser();

		if (!$this->user->guest)
		{	
			$this->isRestaurantEnabled = VikRestaurants::isRestaurantEnabled();
			$this->isTakeAwayEnabled   = VikRestaurants::isTakeAwayEnabled();

			$this->activeTab = $app->input->cookie->getString('vre_allorders_activetab', 'restaurant');

			/**
			 * The value stored within the cookie to detect the active tab has been changed from
			 * integer (1, 2) to string ("restaurant", "takeaway").
			 * 
			 * In case the cookie still have a number assigned, convert it to the matching string.
			 * 
			 * @since 1.9
			 */
			if (is_numeric($this->activeTab))
			{
				$this->activeTab = $this->activeTab == 1 ? 'restaurant' : 'takeaway';
			}

			if (!$this->isRestaurantEnabled)
			{
				// auto select take-away in case restaurant is disabled
				$this->activeTab = 'takeaway';
			}
			else if (!$this->isTakeAwayEnabled)
			{
				// auto select restaurant in case take-away is disabled
				$this->activeTab = 'restaurant';
			}

			/**
			 * Load reservations/orders through the view model.
			 *
			 * @since 1.9
			 */
			$model = JModelVRE::getInstance('allorders');

			$options = [];
			$options['start'] = $app->getUserStateFromRequest($this->getPoolName() . '.restaurant.limitstart', 'restaurantlimitstart', 0, 'uint');
			$options['limit'] = 5;

			// load latest restaurant reservations
			$this->reservations = $model->getItems('restaurant', $options);

			if ($this->reservations)
			{
				// get pagination HTML
				$this->restaurantNavbut = $model->getPagination('restaurant')->getPagesLinks();
			}
			else
			{
				$this->restaurantNavbut = '';
			}

			$options = [];
			$options['start'] = $app->getUserStateFromRequest($this->getPoolName() . '.takeaway.limitstart', 'takeawaylimitstart', 0, 'uint');
			$options['limit'] = 5;

			// load latest take-away oders
			$this->orders = $model->getItems('takeaway', $options);

			if ($this->orders)
			{
				// get pagination HTML
				$this->takeawayNavbut = $model->getPagination('takeaway')->getPagesLinks();
			}
			else
			{
				$this->takeawayNavbut = '';
			}
		}
		else
		{
			// user not logged in, use the login/registration layout
			$this->setLayout('login');
		}

		$this->itemid = $app->input->getUint('Itemid', 0);

		// prepare page content
		VikRestaurants::prepareContent($this);
		
		// display the template
		parent::display($tpl);
	}
}
