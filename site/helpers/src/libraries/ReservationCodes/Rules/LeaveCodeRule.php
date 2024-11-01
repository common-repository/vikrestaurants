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
 * Closes the bill and flags the reservation as paid when the group leaves the table.
 *
 * @since 1.9
 */
class LeaveCodeRule extends CodeRule
{
	/**
	 * @inheritDoc
	 * 
	 * Available only for restaurant reservations.
	 */
	public function isSupported(string $group)
	{
		return !strcasecmp($group, 'restaurant');
	}

	/**
	 * @inheritDoc
	 */
	public function execute($order)
	{
		$now = \VikRestaurants::now();

		// prepare save data
		$data = [
			'id'          => $order->id,
			'bill_closed' => 1,
			'tot_paid'    => $order->bill_value,
		];

		// get paid status
		$paid = \JHtml::fetch('vrehtml.status.paid', 'restaurant', 'code');

		if ($paid)
		{
			// use paid status
			$data['status'] = $paid;
		}

		// make sure the default checkout of the reservation is higher than the current time,
		// because we can only shorten the stay time of a reservation
		if ($order->checkin_ts < $now && $now < $order->checkout)
		{
			// calculate seconds difference between current time and check-in
			$diff = $now - $order->checkin_ts;
			// convert seconds in minutes
			$diff = round($diff / 60);

			$data['stay_time'] = $diff;
		}

		// apply changes
		\JModelVRE::getInstance('reservation')->save($data);
	}
}
