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
 * Used to handle the take-away item into the cart.
 *
 * @see TakeAwayItemGroup  The details of the topping group.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\TakeAway\Cart\Item instead.
 */
class TakeAwayItem extends E4J\VikRestaurants\TakeAway\Cart\Item
{	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id_menu 	 The item menu ID.
	 * @param 	integer  $id_item 	 The item ID
	 * @param 	integer  $id_var 	 The item variation ID.
	 * @param 	string 	 $name_item  The item name.
	 * @param 	string 	 $name_var 	 The item variation name.
	 * @param 	float 	 $price 	 The item price.
	 * @param 	integer  $quantity 	 The item quantity.
	 * @param 	boolean  $ready 	 True if doesn't require a preparation, otherwise false.
	 * @param 	int 	 $id_tax 	 The tax ID.
	 * @param 	$string  $notes 	 The item additional notes.
	 */
	public function __construct($id_menu, $id_item, $id_var, $name_item, $name_var, $price, $quantity, $ready, $id_tax, $notes)
	{
		parent::__construct([
			'id'          => $id_item,
			'id_option'   => $id_var,
			'id_menu'     => $id_menu,
			'name'        => $name_item,
			'option_name' => $name_var,
			'price'       => $price,
			'quantity'    => $quantity,
			'ready'       => $ready,
			'notes'       => $notes,
		]);
	}
	
	/**
	 * Get the ID of the item variation.
	 *
	 * @return 	integer  The item variation ID.
	 */
	public function getVariationID()
	{
		return $this->getOptionID();
	}
	
	/**
	 * Get the name of the item variation.
	 *
	 * @return 	string 	The item variation name.
	 */
	public function getVariationName()
	{
		return $this->getOptionName();
	}

	/**
	 * Get the full name of the item.
	 * Concatenate the item name and the variation name, separated by the given string.
	 *
	 * @param 	string 	$separator 	The separator string between the names.
	 *
	 * @return 	string 	The item full name.
	 */
	public function getFullName($separator = null)
	{
		return $this->getName($separator);
	}
	
	/**
	 * Increase the quantity of the item by the specified units.
	 *
	 * @param 	integer  $units  The units to add.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function push($units = 1)
	{
		return $this->add($units);
	}
	
	/**
	 * Reset the quantity of the item.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function emptyItem()
	{
		return $this->clear();
	}
	
	/**
	 * Get the total cost of the item without discounts.
	 * Calculated by summing the original price with the sum of the toppings total cost.
	 *
	 * @return 	float 	The item total cost with no discount.
	 *
	 * @uses 	getOriginalPrice()
	 * @uses 	getQuantity()
	 */
	public function getTotalCostNoDiscount()
	{
		return $this->getTotalCostBeforeDiscount();
	}

	/**
	 * Get the taxes amount of the item.
	 *
	 * @param 	boolean  $use_taxes  True if taxes are escluded, otherwise false.
	 * 
	 * @return 	float 	 The item taxes amount.
	 */
	public function getTaxes($use_taxes = false)
	{
		$totals = $this->getTotals(E4J\VikRestaurants\TakeAway\Cart\Cart::getInstance());

		/**
		 * Plugins attached to this event can change the calculated taxes
		 * at runtime.
		 *
		 * Note. Calling $item->getTaxes() in this event will result in recursion.
		 *
		 * @param 	float 	&$taxes  The default amount of taxes to use.
		 * @param 	self 	$item 	 The item instance.
		 *
		 * @return 	void
		 *
		 * @since 	1.8
		 * @deprecated 1.10 Without replacement.
		 */
		VREFactory::getEventDispatcher()->trigger('onCalculateItemTaxes', [&$totals->tax, $this]);

		return $totals->tax;
	}

	/**
	 * Get the taxes ratio of the item.
	 *
	 * @return 	float 	The item taxes ratio.
	 */
	public function getTaxesRatio()
	{
		return 0;
	}

	/**
	 * Returns the index of the specified topping group.
	 *
	 * @param   ToppingsGroup  $group  The group to search for.
	 *
	 * @return  int|bool       The index of the group on success, otherwise false. 
	 */
	public function indexOf(E4J\VikRestaurants\TakeAway\Cart\Item\ToppingsGroup $group)
	{
		$index = parent::indexOf($group);
		return $index === false ? -1 : $index;
	}

	/**
	 * Pushes the specified topping group into the list.
	 * It is possible to push a topping group only if it is not yet contained in the list.
	 *
	 * @param   ToppingsGroup  $group  The group to insert.
	 *
	 * @return  bool  True on success, otherwise false.
	 */
	public function addToppingsGroup(E4J\VikRestaurants\TakeAway\Cart\Item\ToppingsGroup $group)
	{
		if ($this->indexOf($group) === -1)
		{
			$this->toppingsGroups[] = $group;

			return true;
		}

		return false;
	}
	
	/**
	 * Empty the topping groups of the item.
	 * 
	 * @return 	self 	This object to support chaining.
	 */
	public function emptyGroups()
	{
		return $this->clearGroups();
	}
	
	/**
	 * Get the list containing all the topping groups.
	 *
	 * @return 	array 	The list of topping groups.
	 */
	public function getToppingsGroupsList()
	{
		return $this->getToppingsGroups();
	}
	
	/**
	 * Magic toString method to debug the item contents.
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
