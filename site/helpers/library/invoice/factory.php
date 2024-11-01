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
 * Invoices factory class.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\Invoice\Factory instead.
 */
class VREInvoiceFactory
{
	/**
	 * Returns a new instance of this object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param 	array 	$order 	The order details.
	 * @param 	string 	$group 	The invoices group.
	 *
	 * @return 	self 	A new instance of this object.
	 */
	public static function getInstance($order = null, $group = null)
	{
		return E4J\VikRestaurants\Invoice\Factory::getInvoice($order, $group);
	}
}
