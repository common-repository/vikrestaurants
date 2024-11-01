<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\TakeAway\Cart;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\TakeAway\Cart\Deals\Discount;

/**
 * Used to handle the take-away deals into the cart.
 * This class wraps a list of Discount objects
 *
 * @since 1.7
 * @since 1.9  Renamed from TakeAwayDeals.
 */
class Deals implements \IteratorAggregate
{	
	/**
	 * The list containing all the current discounts.
	 *
	 * @var Discount[]
	 */
	protected $discounts = [];
	
	/**
	 * Class constructor.
	 *
	 * @param  Discount[]  $discounts  The list of deals to push.
	 */
	public function __construct(array $discounts = [])
	{
		$this->setDiscounts($discounts);
	}
	
	/**
	 * Pushes a new discount in the list.
	 * If the discount already exists, increase its quantity.
	 *
	 * @param   Discount  $discount  The details of the deal to push.
	 *
	 * @return  self      This object to support chaining.
	 */
	public function insert(Discount $discount)
	{
		$index = $this->indexOf($discount);
		
		if ($index !== false && $index >= 0)
		{
			// discount already available, increase quantity
			$this->discounts[$index]->addQuantity($discount->getQuantity());
		}
		else
		{
			// register new discount
			$this->discounts[] = $discount;
		}

		return $this;
	}
	
	/**
	 * Sets or replaces the discount at the specified position.
	 * In case the specified position does not exist, the discount will
	 * be added at the end of the list.
	 *
	 * @param   Discount  $discount  The details of the deal to set.
	 * @param   int       $index     The index of the deal to replace.
	 *
	 * @return  self      This object to support chaining.
	 */
	public function set(Discount $discount, int $index)
	{
		if (isset($this->discounts[$index]))
		{
			// replace existing discount
			$this->discounts[$index] = $discount;
		}
		else
		{
			// add in a new position
			$this->insert($discount);
		}

		return $this;
	}

	/**
	 * Sets the disocunts list with the specified one.
	 *
	 * @param   Discount[]  $discounts  The discounts array. Each element must be an instance
	 *                                  of Discount, otherwise it will be ignored.
	 *
	 * @return  self        This object to support chaining.
	 */
	public function setDiscounts(array $discounts)
	{
		$this->clear();

		foreach ($discounts as $d)
		{
			if ($d instanceof Discount)
			{
				$this->insert($d);
			}
		}

		return $this;
	}
	
	/**
	 * Returns the discount at the specified index.
	 *
	 * @return  Discount  The discount at the specified index if exists, otherwise null.
	 */
	public function get(int $index)
	{
		return $this->discounts[$index] ?? null;
	}

	/**
	 * Returns the index of the specified discount.
	 *
	 * @param   Discount  $discount  The discount to search for.
	 *
	 * @return  int|bool  The index of the discount on success, otherwise false.
	 */
	public function indexOf(Discount $discount)
	{
		foreach ($this->discounts as $index => $d)
		{
			if ($discount->equalsTo($d))
			{
				return $index;
			}
		}

		return false;
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
		foreach ($this->discounts as $index => $d)
		{
			if ($discount instanceof Discount)
			{
				// compare type with the given discount
				if ($d->sameType($discount))
				{
					return $index;
				}
			}
			else
			{
				// string given, search by type
				if ($d->getType() == $discount)
				{
					return $index;
				}
			}
		}

		return false;
	}

	/**
	 * Removes the discount at the specified index.
	 *
	 * @param   int  $index  The index of the deal to remove.
	 *
	 * @return  Discount|null  The removed discount on success, null otherwise.
	 */
	public function removeAt(int $index)
	{
		if (!isset($this->discounts[$index]))
		{
			// index not found
			return null;
		}

		// remove and return the element at the specified index
		return array_splice($this->discounts, $index, 1);
	}

	/**
	 * Removes an existing discount from the list.
	 *
	 * @param   Discount  $discount  The deal to remove.
	 *
	 * @return  bool      True on success, otherwise false.
	 */
	public function remove(Discount $discount)
	{
		$index = $this->indexOf($discount);

		if ($index !== false && $index >= 0)
		{
			return $this->removeAt($index);
		}

		return false;
	}
	
	/**
	 * Returns the list containing all the valid discounts.
	 *
	 * @return  Discount[]  The list of deals.
	 */
	public function getDiscounts()
	{
		$list = [];

		// filter the discounts to obtain only the ones with valid quantity
		foreach ($this->discounts as $d)
		{
			if ($d->getQuantity() > 0)
			{
				$list[] = $d;
			}
		}
		
		return $list;
	}
	
	/**
	 * Resets the list by removing all the discounts.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function clear()
	{
		$this->discounts = [];

		return $this;
	}
	
	/**
	 * @inheritDoc
	 * 
	 * @see \IteratorAggregate
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator($this->getDiscounts());
	}	
}
