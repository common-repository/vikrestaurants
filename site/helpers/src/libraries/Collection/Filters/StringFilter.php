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
 * Default class used to filter (as case-sensitive string) a specific property of an item.
 * 
 * @since 1.9
 */
class StringFilter extends PropertyFilter
{
	/**
	 * Class constructor.
	 * 
	 * @param  string  $name   The name of the property to access.
	 * @param  string  $value  The string to compare.
	 */
	public function __construct(string $name, string $value)
	{
		parent::__construct($name, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		return strcmp($this->value, $item->get($this->name, '')) === 0;
	}
}
