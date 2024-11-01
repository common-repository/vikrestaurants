<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Event;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Wraps the results of a dispatched event.
 *
 * @since 1.9
 */
class EventResponse implements \IteratorAggregate, \ArrayAccess
{
	/** @var array */
	protected $results;

	/**
	 * Class constructor.
	 * 
	 * @param  array  $results  The results received by a dispatched event.
	 */
	public function __construct(array $results)
	{
		// get rid of nulls
		$this->results = array_values(array_filter($results, function($value)
		{
			return !is_null($value);
		}));
	}

	/**
	 * Returns the default array with the results.
	 * 
	 * @return  array
	 */
	public function toArray()
	{
		return $this->results;
	}

	/**
	 * Checks whether a plugin returned at least a value.
	 * 
	 * @return  bool
	 */
	public function has()
	{
		return (bool) $this->results;
	}

	/**
	 * Returns the first available value.
	 * Nulls, falses, zeros and empty strings are always ignored.
	 * 
	 * @return  mixed
	 */
	public function first()
	{
		// get the first positive (bool) value
		foreach ($this->results as $value)
		{
			if ($value)
			{
				return $value;
			}
		}

		// No positive values, the array is probably empty or
		// contains empty values (null, false, empty string or 0).
		// Get the first one in case the array has length, otherwise
		// null will be returned.
		return array_shift($this->results);
	}

	/**
	 * Checks whether the list contains at least a TRUE value.
	 * 
	 * @return  bool
	 */
	public function isTrue()
	{
		return in_array(true, $this->results, true);
	}

	/**
	 * Checks whether the list contains at least a FALSE value.
	 * 
	 * @return  bool
	 */
	public function isFalse()
	{
		return in_array(false, $this->results, true);
	}

	/**
	 * Returns only the values that match a number.
	 * 
	 * @return  array
	 */
	public function numbers()
	{
		// filter the returned values and take only integers and floats
		return array_filter($this->results, function($return)
		{
			return is_int($return) || is_float($return);
		});
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists($offset)
	{
		return isset($this->results[$offset]);
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->results[$offset];
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		// prevent manual property setter
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		// prevent manual property unset
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \IteratorAggregate
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		// return an iterator for the registered results
		return new \ArrayIterator($this->toArray());
	}
}
