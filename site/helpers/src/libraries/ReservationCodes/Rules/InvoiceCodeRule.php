<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\ReservationCodes\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\ReservationCodes\CodeRule;

/**
 * Issues an invoice for the selected reservations/orders.
 *
 * @since 1.9
 */
class InvoiceCodeRule extends CodeRule
{
	/**
	 * @inheritDoc
	 * 
	 * Applies to restaurant reservations and take-away orders.
	 */
	public function isSupported(string $group)
	{
		return !strcasecmp($group, 'restaurant')
			|| !strcasecmp($group, 'takeaway');
	}

	/**
	 * @inheritDoc
	 */
	public function execute($order)
	{
		// get total amount
		if ($order instanceof \VREOrderRestaurant)
		{
			$total = $order->bill_value;
		}
		else
		{
			$total = $order->total_to_pay;
		}

		// generate invoice only in case the total amount is higher than 0
		if ($total > 0)
		{
			// try to generate the invoice
			\JModelVRE::getInstance('invoice')->save([
				'id'         => 0,
				'id_order'   => $order->id,
				'group'      => $order instanceof \VREOrderRestaurant ? 0 : 1,
				'notifycust' => $order->purchaser_mail ? 1 : 0,
			]);
		}

		// close the bill in case of restaurant reservation
		if ($order instanceof \VREOrderRestaurant)
		{
			// update changes
			\JModelVRE::getInstance('reservation')->save([
				'id'          => $order->id,
				'bill_closed' => 1,
			]);
		}
	}
}
