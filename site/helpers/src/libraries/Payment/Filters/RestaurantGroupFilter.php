<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Payment\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Filters the payment methods to obtain only the ones belonging to the restaurant group.
 * 
 * @since 1.9
 */
class RestaurantGroupFilter implements CollectionFilter
{
	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		// take only the fields that do not belong to the take-away only
		return $item->get('group', null) != 2;
	}
}
