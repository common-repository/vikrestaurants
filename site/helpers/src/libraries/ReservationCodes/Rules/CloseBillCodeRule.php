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
 * Closes the bill of the specified restaurant reservation.
 *
 * @since 1.9
 */
class CloseBillCodeRule extends CodeRule
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
		// close the bill
		\JModelVRE::getInstance('reservation')->save([
			'id'          => $order->id,
			'bill_closed' => 1,
		]);
	}
}
