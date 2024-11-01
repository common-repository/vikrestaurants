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
 * VikRestaurants special days test view.
 *
 * @since 1.8.2
 */
class VikRestaurantsViewspecialdaystest extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$args = array();

		$args['group'] = $app->getUserStateFromRequest('vre.specialdaystest.group', 'group', '', 'string');
		$args['date']  = $app->getUserStateFromRequest('vre.specialdaystest.date', 'date', '', 'string');

		// make sure the group is supported
		$args['group'] = JHtml::fetch('vrehtml.admin.getgroup', $args['group'], ['restaurant', 'takeaway']);

		// if not specified, use the current date
		if (!$args['date'])
		{
			$args['date'] = date(VREFactory::getConfig()->get('dateformat'), VikRestaurants::now());
		}

		$this->args = $args;
		
		// display the template (default.php)
		parent::display($tpl);
	}
}
