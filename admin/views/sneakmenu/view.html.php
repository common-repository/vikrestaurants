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
 * VikRestaurants menus preview page.
 *
 * @since 1.5
 */
class VikRestaurantsViewsneakmenu extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$dbo   = JFactory::getDbo();
		
		// force tmpl=component
		$input->set('tmpl', 'component');

		$id = $input->get('id', 0, 'uint');
		
		/** @var \stdClass */
		$this->sections = JModelVRE::getInstance('menu')->getSections($id);

		// display the template
		parent::display($tpl);
	}
}
