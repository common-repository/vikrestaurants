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
 * Used to handle the take-away item group into the cart.
 * This class wraps a list of toppings.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\TakeAway\Cart\Item\ToppingsGroup instead.
 */
class TakeAwayItemGroup extends E4J\VikRestaurants\TakeAway\Cart\Item\ToppingsGroup
{	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id_group 	The group ID.
	 * @param 	string 	 $title 	The group title.
	 * @param 	boolean  $multiple 	True if group is multiple, otherwise is single.
	 * @param 	boolean  $quantity  True if the toppings can be picked more than once.
	 */
	public function __construct($id_group, $title, $multiple, $quantity = false)
	{
		parent::__construct([
			'id'       => $id_group,
			'title'    => $title,
			'multiple' => $multiple,
			'quantity' => $quantity,
		]);
	}

	/**
	 * Returns the index of the specified topping.
	 *
	 * @param   Topping   $topping  The topping to search for.
	 *
	 * @return  int|bool  The index of the topping on success, otherwise false.
	 */
	public function indexOf(E4J\VikRestaurants\TakeAway\Cart\Item\Topping $topping)
	{
		$index = parent::indexOf($topping);
		return $index === false ? -1 : $index;
	}

	/**
	 * Pushes the specified topping into the list.
	 * It is possible to push a topping only if it is not yet contained in the list.
	 *
	 * @param   Topping  $topping  The topping to insert.
	 *
	 * @return  bool     True on success, otherwise false.
	 */
	public function addTopping(E4J\VikRestaurants\TakeAway\Cart\Item\Topping $topping)
	{
		if ($this->indexOf($topping) === -1)
		{
			$this->toppings[] = $topping;

			return true;
		}

		return false;
	}

	/**
	 * Reset the list by removing all the toppings.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function emptyToppings()
	{
		return $this->clear();
	}
	
	/**
	 * Get the list containing all the toppings.
	 *
	 * @return 	array 	The list of toppings.
	 */
	public function getToppingsList()
	{
		return $this->getToppings();
	}
	
	/**
	 * Magic toString method to debug the group contents.
	 *
	 * @return  string  The debug string of this object.
	 *
	 * @since   1.7
	 */
	public function __toString() 
	{
		return '<pre>' . print_r($this, true) . '</pre>';
	}
}
