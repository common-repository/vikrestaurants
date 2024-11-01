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
 * VikRestaurants menus list view.
 * The menus of restaurant will be filtered by date
 * in case the Search Bar is turned on.
 *
 * @since 1.0
 */
class VikRestaurantsViewmenuslist extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();
		
		$this->filters = [];
		$this->filters['date']  = $app->input->get('date', '', 'string');
		$this->filters['shift'] = $app->input->get('shift', 0, 'uint');
		$this->filters['hour']  = $app->input->get('hour', null, 'uint');

		/**
		 * Filter the menus to display only the selected ones.
		 *
		 * @since 1.7.4
		 */
		$this->filters['ids'] = $app->input->get('id_menus', [], 'uint');
		
		if (E4J\VikRestaurants\Helpers\DateHelper::isNull($this->filters['date']))
		{
			$this->filters['date'] = date(VREFactory::getConfig()->get('dateformat'), VikRestaurants::now());
		}

		// check if the search bar should be displayed
		$this->showSearchForm = $app->getUserStateFromRequest('vre.menuslist.showsearchbar', 'show_search_bar', false, 'bool');
		$this->showSearchForm = $app->input->get('tmpl') != 'component' && $this->showSearchForm;
		
		$this->menus = [];

		$hour = $this->filters['hour'];

		if (is_null($hour) && $this->filters['shift'])
		{
			$time = JHtml::fetch('vikrestaurants.timeofshift', (int) $this->filters['shift']);

			if ($time)
			{
				// use from hour of selected working shift
				$hour = $time->fromhour;
			}
		}
			
		/**
		 * Do not use the date filter if search bar is turned off.
		 * This will retrieve all the menus also for closing days.
		 *
		 * Do not use time filter in case the hour/shift was not specified.
		 *
		 * @since  1.8
		 */
		$args = [
			'date'    => $this->showSearchForm ? $this->filters['date'] : null,
			'hourmin' => $this->showSearchForm && $hour ? $hour . ':0' : null,
		];
		
		$this->menus = VikRestaurants::getAllAvailableMenusOn($args);

		if ($this->filters['ids'])
		{
			$ids = $this->filters['ids'];

			// unset the menus that are not within the list
			$this->menus = array_filter($this->menus, function($menu) use ($ids)
			{
				return in_array($menu->id, $ids);
			});

			// do not preserve the keys
			$this->menus = array_values($this->menus);
		}

		// translate menus in case multi-lingual is supported
		VikRestaurants::translateMenus($this->menus);

		/**
		 * Set printable menus setting in user state for
		 * being re-used in other views.
		 *
		 * @since 1.8
		 */
		$app->getUserStateFromRequest('vre.menuslist.printable', 'printable_menus', false, 'bool');

		$this->itemid = $app->input->get('Itemid', null, 'uint');

		// prepare page content
		VikRestaurants::prepareContent($this);
		
		// display the template
		parent::display($tpl);
	}
}
