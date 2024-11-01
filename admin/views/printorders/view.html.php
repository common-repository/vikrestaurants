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
 * VikRestaurants print orders view.
 *
 * @since 1.0
 */
class VikRestaurantsViewprintorders extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$input = JFactory::getApplication()->input;
		$input->set('tmpl', 'component');

		// Retrieve print header and footer from request.
		// If not specified, the default ones will be used.
		$print_orders_attr = $input->get('printorders', VikRestaurants::getPrintOrdersText(), 'array');

		if (!empty($print_orders_attr['update']))
		{
			// update header and footer texts
			VREFactory::getConfig()->set('printorderstext', $print_orders_attr);
		}
		
		$type = $input->get('type', 'restaurant', 'string');
		$ids  = $input->get('cid', [], 'uint');

		$tag = JFactory::getLanguage()->getTag();

		/**
		 * Loads the site language file according to the current langtag.
		 *
		 * @since 1.8
		 */
		VikRestaurants::loadLanguage($tag);
		
		$this->rows = [];

		foreach ($ids as $id)
		{	
			if ($type == 'takeaway')
			{
				// get take-away order
				$order = VREOrderFactory::getOrder($id, $tag);
			}
			else
			{
				// get restaurant reservation
				$order = VREOrderFactory::getReservation($id, $tag);	
			}

			if ($order)
			{
				$this->rows[] = $order;
			}	
		}
		
		$this->type = $type;
		$this->text = $print_orders_attr;

		// display the template
		parent::display($tpl);
	}
}
