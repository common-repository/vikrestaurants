<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class used to validate the delivery area coverage.
 * 
 * @since 1.9
 */
class DeliveryChecker
{
	/** @var AreasCollection */
	protected $areas;

	/**
	 * Class constructor.
	 * 
	 * @param  AreasCollection  $areas  A list of delivery areas.
	 */
	public function __construct(AreasCollection $areas)
	{
		$this->areas = $areas;
	}

	/**
	 * Checks whether the provided query can be covered by an area for deliveries.
	 * 
	 * @param   DeliveryQuery|array|object  The address information.
	 * 
	 * @return  Area|null  The matching delivery area in case the address is supported,
	 *                     null otherwise.
	 */
	public function search($query)
	{
		if (!$query instanceof DeliveryQuery)
		{
			// create query from provided array/object
			$query = new DeliveryQuery($query);
		}

		// iterate all the supported delivery areas
		foreach ($this->areas as $area)
		{
			// check whether this area can accept the provided query
			if ($area->canAccept($query))
			{
				// delivery supported on this area
				return $area;
			}
		}

		return null;
	}
}
