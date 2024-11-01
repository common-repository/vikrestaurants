<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Used to handle the dishes cart of the program.
 * This class cannot be instantiated manually as we can have only one instance per session.
 *
 * Usage:
 * $cart = VREDishesCart::getInstance();
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\OrderDishes\Cart instead.
 */
class VREDishesCart extends E4J\VikRestaurants\OrderDishes\Cart implements Serializable
{	
	/**
	 * @inheritDoc
	 */
	public function serialize()
	{
		return serialize($this->__serialize());
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize($serialized)
	{
		$this->__unserialize(unserialize($serialized));
	}
}
