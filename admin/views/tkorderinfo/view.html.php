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
 * VikRestaurants take-away order summary view.
 *
 * @since 1.0
 */
class VikRestaurantsViewtkorderinfo extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$input = JFactory::getApplication()->input;

		// force blank component layout
		$input->set('tmpl', 'component');
		
		$id = $input->get('id', 0, 'uint');

		// check the status of the reservation first
		VikRestaurants::removeTakeAwayOrdersOutOfTime($id);
		
		// get order details
		$this->order = VREOrderFactory::getOrder($id, JFactory::getLanguage()->getTag());

		if (!$this->order)
		{
			throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
		}

		/**
		 * Retrieve custom fields for the takeaway section by using the related helper.
		 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
		 *
		 * @since 1.9
		 */
		$this->customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter)
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

		// display the template
		parent::display($tpl);
	}
}
