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
 * Used to handle the take-away deals into the cart.
 * This class wraps a list of TakeAwayDiscount objects
 *
 * @see TakeAwayDiscount  The details of the deal.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\TakeAway\Cart\Deals instead.
 */
class TakeAwayDeals extends E4J\VikRestaurants\TakeAway\Cart\Deals
{
	/**
	 * Returns the index of the specified discount.
	 *
	 * @param   Discount  $discount  The discount to search for.
	 *
	 * @return  int|bool  The index of the discount on success, otherwise false.
	 */
	public function indexOf(E4J\VikRestaurants\TakeAway\Cart\Deals\Discount $discount)
	{
		$index = parent::indexOf($discount);
		return $index === false ? -1 : $index;
	}

	/**
	 * Returns the index of the first discount that as same type of the specified one.
	 *
	 * @param   Discount|string  $discount 	Either the discount instance or the discount
	 *                                      type to search for.
	 *
	 * @return 	int|bool  The index of the discount on success, otherwise false.
	 */
	public function indexOfType($discount)
	{
		$index = parent::indexOfType($discount);
		return $index === false ? -1 : $index;
	}

	/**
	 * Removes an existing discount from the list.
	 *
	 * @param   Discount  $discount  The deal to remove.
	 *
	 * @return  bool      True on success, otherwise false.
	 */
	public function remove(E4J\VikRestaurants\TakeAway\Cart\Deals\Discount $discount)
	{
		return $this->removeAt($this->indexOf($discount));
	}

	/**
	 * Get the list containing all the valid discounts.
	 *
	 * @return 	array 	The list of deals.
	 */
	public function getDiscountsList()
	{
		return $this->getDiscounts();
	}

	/**
	 * Get the total count of discounts in the list.
	 *
	 * @return 	integer  The size of the list.
	 */
	public function getSize()
	{
		return count($this->getDiscounts());
	}
	
	/**
	 * Reset the list by removing all the discounts.
	 *
	 * @return 	self 	This object to support chaining.
	 */
	public function emptyDiscounts()
	{
		return $this->clear();
	}
	
	/**
	 * Magic toString method to debug the deals contents.
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
