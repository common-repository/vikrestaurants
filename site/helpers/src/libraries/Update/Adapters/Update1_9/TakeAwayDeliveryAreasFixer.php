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
 * The type used to identify the delivery area handler has been changed from INT to STRING to 
 * improve the extendibility of the framework.
 *
 * @since 1.9
 */
class TakeAwayDeliveryAreasFixer extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$db = \JFactory::getDbo();

		// fetch all the existing areas
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__vikrestaurants_takeaway_delivery_area'))
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $area)
		{
			// adjust the take-away delivery area 
			$this->mapAreaType($area);

			// commit changes
			$db->updateObject('#__vikrestaurants_takeaway_delivery_area', $area, 'id');
		}

		return true;
	}

	/**
	 * Adjusts the delivery area type from INT to STRING.
	 * 
	 * @param   object  $area  The delivery area to update.
	 * 
	 * @return  void
	 */
	private function mapAreaType(object $area)
	{
		// define lookup to convert the type of the take-away delivery area
		$lookup = [
			1 => 'polygon',
			2 => 'circle',
			3 => 'zipcodes',
			4 => 'cities',
		];

		if (isset($lookup[$area->__type]))
		{
			$area->type = $lookup[$area->__type];
		}
	}
}
