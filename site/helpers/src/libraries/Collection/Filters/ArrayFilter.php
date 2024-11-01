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
 * Default class used to check whether a specific property of an item is contained
 * within the provided array.
 * 
 * @since 1.9
 */
class ArrayFilter extends PropertyFilter
{
	/**
	 * Class constructor.
	 * 
	 * @param  string  $name   The name of the property to access.
	 * @param  array   $value  A list of supported values.
	 */
	public function __construct(string $name, array $value)
	{
		parent::__construct($name, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		return in_array($item->get($this->name, ''), $this->value);
	}
}
