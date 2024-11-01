<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9_1;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * The update to the 1.9 version adjusted the X code for a status code that doesn't exist, "CANCELED" instead of "CANCELLED".
 * Here we should execute an additional rule aiming to fix the reservations and orders that still use the "CANCELLED" status.
 *
 * These changes have to be applied to the following tables:
 * - `#__vikrestaurants_reservation`
 * - `#__vikrestaurants_takeaway_reservation`
 *
 * @since 1.9.1
 */
class StatusCodesMapper extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$this->adaptRestaurantReservations();
		$this->adaptTakeAwayOrders();

		return true;
	}

	/**
	 * Adjusts the status codes for the restaurant reservations.
	 *
	 * @return 	void
	 */
	private function adaptRestaurantReservations()
	{
		$db = \JFactory::getDbo();

		// set CANCELLED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('X'))
			->where($db->qn('status') . ' = ' . $db->q('CANCELLED'));

		$db->setQuery($q);
		$db->execute();
	}

	/**
	 * Adjusts the status codes for the take-away orders.
	 *
	 * @return 	void
	 */
	private function adaptTakeAwayOrders()
	{
		$db = \JFactory::getDbo();

		// set CANCELLED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('X'))
			->where($db->qn('status') . ' = ' . $db->q('CANCELLED'));

		$db->setQuery($q);
		$db->execute();
	}
}
