<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * With the implementation of the status codes, it is now possible to choose for which statuses, the system
 * should send a notification e-mail. For this reason, during the update to the new 1.9 version, after
 * installing the default statuses, we should update the configuration to support the new statuses.
 *
 * Similar changes have to be applied also to the following tables:
 * - `#__vikrestaurants_reservation`
 * - `#__vikrestaurants_takeaway_reservation`
 * - `#__vikrestaurants_configuration` (default status)
 *
 * @since 1.9
 */
class StatusCodesMapper extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$this->adaptConfiguration();
		$this->adaptRestaurantReservations();
		$this->adaptTakeAwayOrders();

		return true;
	}

	/**
	 * Adjusts the status codes from the configuration.
	 *
	 * @return  void
	 */
	private function adaptConfiguration()
	{
		$config = \VREFactory::getConfig();

		// update notification statuses
		foreach (['mailcustwhen', 'mailoperwhen', 'mailadminwhen', 'tkmailcustwhen', 'tkmailoperwhen', 'tkmailadminwhen'] as $key)
		{
			switch ($config->getUint($key))
			{
				case 1:
					// only confirmed
					$config->set($key, ['C', 'P']);
					break;

				case 2:
					// confirmed and pending
					$config->set($key, ['C', 'P', 'W']);
					break;

				default:
					// never
					$config->set($key, []);
			}
		}

		// update default status
		foreach (['defstatus', 'tkdefstatus'] as $key)
		{
			if ($config->get($key) == 'CONFIRMED')
			{
				$config->set($key, 'C');
			}
			else
			{
				$config->set($key, 'W');
			}
		}
	}

	/**
	 * Adjusts the status codes for the restaurant reservations.
	 *
	 * @return 	void
	 */
	private function adaptRestaurantReservations()
	{
		$db = \JFactory::getDbo();

		// set PAID status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('P'))
			->where($db->qn('status') . ' = ' . $db->q('CONFIRMED'))
			->where($db->qn('tot_paid') . ' > 0');

		$db->setQuery($q);
		$db->execute();

		// set CONFIRMED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('C'))
			->where($db->qn('status') . ' = ' . $db->q('CONFIRMED'));

		$db->setQuery($q);
		$db->execute();

		// set PENDING status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('W'))
			->where($db->qn('status') . ' = ' . $db->q('PENDING'));

		$db->setQuery($q);
		$db->execute();

		// set REMOVED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('E'))
			->where($db->qn('status') . ' = ' . $db->q('REMOVED'));

		$db->setQuery($q);
		$db->execute();

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

		// set PAID status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('P'))
			->where($db->qn('status') . ' = ' . $db->q('CONFIRMED'))
			->where($db->qn('tot_paid') . ' = ' . $db->qn('total_to_pay'));

		$db->setQuery($q);
		$db->execute();

		// set CONFIRMED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('C'))
			->where($db->qn('status') . ' = ' . $db->q('CONFIRMED'));

		$db->setQuery($q);
		$db->execute();

		// set PENDING status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('W'))
			->where($db->qn('status') . ' = ' . $db->q('PENDING'));

		$db->setQuery($q);
		$db->execute();

		// set REMOVED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('E'))
			->where($db->qn('status') . ' = ' . $db->q('REMOVED'));

		$db->setQuery($q);
		$db->execute();

		// set CANCELLED status
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('status') . ' = ' . $db->q('X'))
			->where($db->qn('status') . ' = ' . $db->q('CANCELLED'));

		$db->setQuery($q);
		$db->execute();
	}
}
