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
 * Flags a restaurant reservation as arrived at the local.
 *
 * @since 1.9
 */
class ArrivedCodeRule extends CodeRule
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
		// group arrived at the restaurant
		\JModelVRE::getInstance('reservation')->save([
			'id'      => $order->id,
			'arrived' => 1,
		]);
	}
}
