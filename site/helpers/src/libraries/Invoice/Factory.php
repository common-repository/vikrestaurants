<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Invoice;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Invoices factory class.
 *
 * @since 1.9
 */
class Factory
{
	/**
	 * Returns a new instance of the requested invoice.
	 *
	 * @param   array 	$order 	 The order details.
	 * @param   string 	$group 	 The invoices group.
	 *
	 * @return  InvoiceTemplate  A new instance of the invoice object.
	 */
	public static function getInvoice($order, $group)
	{
		if (is_numeric($group))
		{
			$group = $group == 0 ? 'restaurant' : 'takeaway';
		}

		// prepare invoice template class name
		$classname = 'E4J\\VikRestaurants\\Invoice\\Templates\\' . ucfirst($group) . 'InvoiceTemplate';

		if (!class_exists($classname))
		{
			throw new \RuntimeException('Invoice template [' . $group . '] not found', 404);
		}

		// instantiate new invoice object
		$obj = new $classname($order);

		if (!$obj instanceof InvoiceTemplate)
		{
			throw new \RuntimeException('The invoice template [' . $classname . '] is not a valid instance', 500);
		}

		return $obj;
	}
}
