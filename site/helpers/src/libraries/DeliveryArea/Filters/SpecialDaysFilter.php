<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\DeliveryArea\Area;

/**
 * Filters the delivery areas to obtain only the ones that have been assigned to the
 * special days that match the check-in date and time.
 * 
 * @since 1.9
 */
class SpecialDaysFilter implements CollectionFilter
{
	/** @var int[] */
	protected $acceptedZones = [];

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		// get cart instance
		$cart = \E4J\VikRestaurants\TakeAway\Cart::getInstance();

		// get check-in date
		$date = $cart->getCheckinTimestamp();
		// get check-in time
		$time = $cart->getCheckinTime($first = true);

		// init special days manager
		$sdManager = new \VRESpecialDaysManager('takeaway');
		// set checkin date
		$sdManager->setStartDate($date);

		if ($time)
		{
			// explode hour and minute
			list($hour, $min) = explode(':', $time);
			// set checkin time
			$sdManager->setCheckinTime($hour, $min);
		}

		// get special days
		$sdList = $sdManager->getList();

		// iterate all special days found
		foreach ($sdList as $sd)
		{
			// add accepted areas
			$this->acceptedZones = array_merge($this->acceptedZones, $sd->deliveryAreas);
		}

		// get rid of duplicates
		$this->acceptedZones = array_values(array_filter($this->acceptedZones));
	}

	/**
	 * @inheritDoc
	 * 
	 * @throws  \InvalidArgumentException  Only Area instances are accepted.
	 */
	public function match(Item $item)
	{
		if (!$item instanceof Area)
		{
			// can handle only objects that inherit the Area class
			throw new \InvalidArgumentException('Area item expected, ' . get_class($item) . ' given');
		}

		if (!$this->acceptedZones)
		{
			// no filter applied to the delivery areas
			return true;
		}

		// make sure this delivery area is accepted
		return in_array($item->get('id'), $this->acceptedZones);
	}
}
