<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Class used to wrap a collection of items.
 * 
 * @see \IteratorAggregate
 * 
 * @since 1.9
 */
class ObjectsCollection implements \IteratorAggregate, \ArrayAccess, \Countable, \JsonSerializable
{
	/** @var Item[] */
	protected $items = [];

	/**
	 * Class constructor.
	 * 
	 * @param   array  $items  An array of items to register.
	 */
	public function __construct(array $items = [])
	{
		foreach ($items as $item)
		{
			// use the apposite method so that any concrete implementor can apply
			// further restrictions while adding the items
			$this->add($item);
		}
	}

	/**
	 * Registers the specified item within the collection.
	 * 
	 * @param   Item  $item  The item to add.
	 * 
	 * @return  self  This object to support chaining.
	 */
	public function add(Item $item)
	{
		$this->items[] = $item;

		return $this;
	}

	/**
	 * Filters the items in the collection according to the provided search query.
	 * 
	 * @param   CollectionFilter   $filter  The search query.
	 * 
	 * @return  ObjectsCollection  A new collection with the matching items.
	 */
	public function filter(CollectionFilter $filter)
	{
		// create a new collection with the same class as the current one
		$collection = clone $this;

		// reset filtered items
		$collection->items = [];

		foreach ($this->items as $item)
		{
			if ($filter->match($item))
			{
				// matching item, register it within the new collection
				$collection->add($item);
			}
		}

		return $collection;
	}

	/**
	 * Sorts the items in the collection according to the provided methods.
	 * 
	 * @param   mixed  $sort  Either an array of methods or a CollectionSorter instance.
	 * 
	 * @return  self   This object to support chaining.
	 */
	public function sort($sort)
	{
		if (!is_array($sort))
		{
			$sort = [$sort];
		}

		usort($this->items, function($a, $b) use ($sort)
		{
			// scan all the specified sort methods
			foreach ($sort as $method)
			{
				if (!$method instanceof CollectionSorter)
				{
					throw new \InvalidArgumentException('The specified collection sorter is not a valid instance');
				}

				$factor = $method->compare($a, $b);

				if ($factor !== 0)
				{
					// we have a difference between the 2 elements
					return $factor;
				}

				// no differences between the 2 elements, proceed to the next sorting method
			}

			// no differences
			return 0;
		});

		return $this;
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Countable
	 */
	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->items);
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \IteratorAggregate
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator($this->items);
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \JsonSerializable
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return $this->items;
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists($offset)
	{
		return isset($this->items[$offset]);
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->items[$offset];
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException('You cannot alter the items of a collection');
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('You cannot unset the items from a collection');
	}
}
