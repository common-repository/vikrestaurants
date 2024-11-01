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

use E4J\VikRestaurants\Collection\Filters\Helpers\NumberComparator;
use E4J\VikRestaurants\Collection\Item;

/**
 * Default class used to filter (as number) a specific property of an item.
 * 
 * @since 1.9
 */
class NumberFilter extends PropertyFilter
{
	use NumberComparator;

	/** @var string */
	protected $comparator;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $name        The name of the property to access.
	 * @param  mixed   $value       The value to compare.
	 * @param  string  $comparator  The comparison operator.
	 */
	public function __construct(string $name, $value, string $comparator)
	{
		if (!is_numeric($value))
		{
			throw new \InvalidArgumentException('Number expected, ' . gettype($value) . ' given');
		}

		$this->comparator = $comparator;

		parent::__construct($name, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function match(Item $item): bool
	{
		// use the comparison method defined by the NumberComparator trait
		return $this->compare(
			$item->get($this->name, 0),
			$this->value,
			$this->comparator
		);
	}
}
