<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Item;

/**
 * Default class used to filter (as case-insensitive string) a specific property of an item.
 * 
 * @since 1.9
 */
class StringCaseFilter extends StringFilter
{
	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		return strcasecmp($this->value, $item->get($this->name, '')) === 0;
	}
}
