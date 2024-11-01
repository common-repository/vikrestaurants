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
 * Generic collection item implementation.
 *
 * @since 1.9
 */
class Item extends \JObject implements \ArrayAccess
{
	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists($offset)
	{
		return $this->get($offset, null) !== null;
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * @inheritDoc
	 *
	 * @see \ArrayAccess
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		$this->set($offset, null);
	}
}
