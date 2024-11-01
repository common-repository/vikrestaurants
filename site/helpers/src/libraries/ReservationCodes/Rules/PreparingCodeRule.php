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
 * Flags a take-away order as under preparation.
 * This is useful to attach the order to the "current" dashboard widget.
 *
 * @since 1.9
 */
class PreparingCodeRule extends CodeRule
{
	/**
	 * @inheritDoc
	 * 
	 * Available only for take-away orders.
	 */
	public function isSupported(string $group)
	{
		return !strcasecmp($group, 'takeaway');
	}

	/**
	 * @inheritDoc
	 */
	public function execute($order)
	{
		// order under preparation
		\JModelVRE::getInstance('tkreservation')->save([
			'id'      => $order->id,
			'current' => 1,
		]);
	}
}
