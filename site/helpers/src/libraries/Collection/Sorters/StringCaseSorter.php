<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Sorters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionSorter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Generic class to sort 2 items according to the case-insensitive comparison of the specified property (as string).
 * 
 * @since 1.9
 */
class StringSorter extends PropertySorter
{
	/**
	 * @inheritDoc
	 */
	protected function doCompare(Item $a, Item $b): int
	{
		return strcasecmp($a->get($this->name), $b->get($this->name));
	}
}
