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

use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;
use E4J\VikRestaurants\TakeAway\Cart;
use E4J\VikRestaurants\TakeAway\Cart\Item\ToppingsGroup;

/**
 * Used to handle the take-away item into the cart.
 *
 * @since 1.7
 * @since 1.9  Renamed from TakeAwayItem.
 */
class Item
{	
	/**
	 * The ID of the item.
	 *
	 * @var int
	 */
	protected $id;
	
	/**
	 * The ID of the variation.
	 * Specify 0 or -1 if the item has no variations.
	 *
	 * @var int.
	 */
	protected $idOption;

	/**
	 * The ID of the menu to which the item belongs.
	 *
	 * @var int
	 */
	protected $idMenu;

	/**
	 * The name of the item.
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 * The name of the variation.
	 * Empty if the item has no variations.
	 *
	 * @var string
	 */
	protected $optionName;
	
	/**
	 * The sum of the item and variation prices without discounts.
	 *
	 * @var float
	 */
	protected $originalPrice;
	
	/**
	 * The original price with eventual discounts.
	 *
	 * @var float
	 */
	protected $price;
	
	/**
	 * The quantity of the item.
	 *
	 * @var int
	 */
	protected $quantity;
	
	/**
	 * If the item requires a preparation or not.
	 *
	 * @var bool
	 */
	protected $ready;
	
	/**
	 * The ID of the taxes that this item should use.
	 *
	 * @var int
	 */
	protected $idTax;

	/**
	 * The additional notes and requirements for this item.
	 *
	 * @var string
	 */
	protected $additionalNotes;	
	
	/**
	 * The list containing the toppings for this item.
	 *
	 * @var ToppingsGroup[]
	 */
	protected $toppingsGroups = [];
	
	/**
	 * The number of times the deal has been redeemed.
	 *
	 * @var int
	 */
	protected $dealQuantity = 0;

	/**
	 * The ID of the deal redeemed.
	 *
	 * @var int
	 */
	protected $idDeal = -1;

	/**
	 * Whether the item can be removed from the cart or not.
	 * For example, an item cannot be removed in case it has been added with a deal.
	 *
	 * @var bool
	 */
	protected $removable = true;

	/**
	 * The event dispatcher.
	 * 
	 * @var DispatcherInterface
	 */
	protected $dispatcher;
	
	/**
	 * Class constructor.
	 *
	 * @param  array|object         $data        The item data.
	 * @param  DispatcherInterface  $dispatcher  The events dispatcher instance.
	 */
	public function __construct($data, DispatcherInterface $dispatcher = null)
	{
		$this->id       = (int) ($data['id'] ?? 0);
		$this->idOption = (int) ($data['id_option'] ?? 0);
		$this->idMenu   = (int) ($data['id_menu'] ?? 0);

		$this->name       = (string) ($data['name'] ?? '');
		$this->optionName = (string) ($data['option_name'] ?? '');

		$this->price         = abs((float) ($data['price'] ?? 0));
		$this->originalPrice = $this->price;

		$this->quantity        = max(1, (int) ($data['quantity'] ?? 1));
		$this->ready           = (bool) ($data['ready'] ?? false);
		$this->additionalNotes = (string) ($data['notes'] ?? '');

		if ($dispatcher)
		{
			// use provided dispatcher
			$this->dispatcher = $dispatcher;
		}
		else
		{
			// use default platform dispatcher
			$this->dispatcher = \VREFactory::getPlatform()->getDispatcher();
		}
	}
	
	/**
	 * Returns the ID of the item menu.
	 *
	 * @return  int  The item menu ID.
	 */
	public function getMenuID()
	{
		return $this->idMenu;
	}
	
	/**
	 * Returns the ID of the item.
	 *
	 * @return  int  The item ID.
	 */
	public function getItemID()
	{
		return $this->id;
	}
	
	/**
	 * Returns the ID of the item variation.
	 *
	 * @return  int  The item variation ID.
	 */
	public function getOptionID()
	{
		return $this->idOption;
	}
	
	/**
	 * Returns the name of the item.
	 *
	 * @return  string  The item name.
	 */
	public function getItemName()
	{
		return $this->name;
	}
	
	/**
	 * Returns the name of the item variation.
	 *
	 * @return  string  The item variation name.
	 */
	public function getOptionName()
	{
		return $this->optionName;
	}

	/**
	 * Returns the full name of the item.
	 * Concatenates the item name and the variation name, separated by the given string.
	 *
	 * @param   string  $separator  The separator string between the names.
	 *
	 * @return  string  The item full name.
	 */
	public function getName(string $separator = null)
	{
		if (empty($separator))
		{
			$separator = ' - ';
		}

		// join item name with variation name
		return implode($separator, array_filter([
			$this->getItemName(),
			$this->getOptionName(),
		]));
	}
	
	/**
	 * Returns the real price of the item.
	 *
	 * @return  float  The item real price.
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
	/**
	 * Returns the original price of the item.
	 *
	 * @return  float  The item original price.
	 */
	public function getOriginalPrice()
	{
		return $this->originalPrice;
	}
	
	/**
	 * Forces a new price of the item.
	 *
	 * @param   float  $price  The item price.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setPrice(float $price)
	{
		$this->price = max(0, $price);

		return $this;
	}
	
	/**
	 * Sets the number of time a deal is used.
	 *
	 * @param   int   $qty  The deal quantity.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setDealQuantity(int $qty)
	{
		$this->dealQuantity = max(0, $qty);

		return $this;
	}
	
	/**
	 * Returns the number of time a deal is used.
	 *
	 * @return  int  The deal quantity.
	 */
	public function getDealQuantity()
	{
		return $this->dealQuantity;
	}
	
	/**
	 * Returns the quantity of the item.
	 *
	 * @return  int  The item quantity.
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}
	
	/**
	 * Sets the quantity of the item.
	 *
	 * @param   int   $units  The item quantity.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setQuantity(int $units)
	{
		$this->quantity = max(0, $units);

		return $this;
	}
	
	/**
	 * Increases the quantity of the item by the specified units.
	 *
	 * @param   int   $units  The units to add.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function add(int $units = 1)
	{
		return $this->setQuantity($this->quantity + $units);
	}
	
	/**
	 * Decreases the quantity of the item by the specified units.
	 *
	 * @param   int   $units  The units to remove.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function remove(int $units = 1)
	{
		return $this->setQuantity($this->quantity - $units);
	}
	
	/**
	 * Resets the quantity of the item.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function clear()
	{
		return $this->setQuantity(0);
	}
	
	/**
	 * Checks whether the item doesn't need a preparation.
	 *
	 * @return  bool  True if the item is ready, otherwise false.
	 */
	public function isReady()
	{
		return $this->ready;
	}

	/**
	 * Checks whether the item need a preparation.
	 *
	 * @return  bool  True if the item needs a preparation, otherwise false.
	 */
	public function needPreparation()
	{
		return $this->isReady() === false;
	}

	/**
	 * Returns the tax ID of the item.
	 *
	 * @return  int  The item tax ID.
	 */
	public function getTaxID()
	{
		if ($this->idTax === null)
		{
			// find tax ID of given subject
			$this->idTax = \E4J\VikRestaurants\Taxing\Helpers\TaxesHelper::getTaxOf($this->getItemID(), 'takeaway.item');
		}

		return $this->idTax;
	}
	
	/**
	 * Returns the additional notes of the item.
	 *
	 * @return  string  The item additional notes.
	 */
	public function getAdditionalNotes()
	{
		return $this->additionalNotes;
	}
	
	/**
	 * Sets the additional notes of the item.
	 *
	 * @param   string  The item additional notes.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setAdditionalNotes(string $notes)
	{
		$this->additionalNotes = $notes;

		return $this;
	}
	
	/**
	 * Sets the deal ID.
	 *
	 * @param   int   $idDeal  The deal ID.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setDealID(int $idDeal)
	{
		$this->idDeal = $idDeal;

		return $this;
	}
	
	/**
	 * Returns the deal ID.
	 *
	 * @return  int  The deal ID.
	 */
	public function getDealID()
	{
		return $this->idDeal;
	}

	/**
	 * Checks whether the item can be removed.
	 *
	 * @return  bool  True if the item can be removed, otherwise false.
	 */
	public function canBeRemoved()
	{
		return $this->removable;
	}
	
	/**
	 * Flags the item as removable or not.
	 *
	 * @param   bool  $removable  True if the item can be removed, otherwise false.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setRemovable(bool $removable)
	{
		$this->removable = $removable;

		return $this;
	}

	/**
	 * Calculates the total cost of the item without discounts.
	 * Calculated by summing the original price with the sum of the toppings total cost.
	 *
	 * @return  float  The item total cost with no discount.
	 */
	public function getTotalCostBeforeDiscount()
	{
		// get original price without any discount
		$totalCost = $this->getOriginalPrice();

		foreach ($this->toppingsGroups as $group)
		{
			// increase by the toppings total
			$totalCost += $group->getTotalCost();
		}

		// multiply all by the number of selected units
		return $totalCost * $this->getQuantity();
	}

	/**
	 * Calculates the total cost of the item considering internal discounts.
	 * Calculated by summing the original price with the sum of the toppings total cost.
	 * Then subtract the discounts in case there is at least a deal.
	 * 
	 * Discounts applied globally (such as a coupon) are not applied here.
	 *
	 * @return  float  The real item total cost.
	 */
	public function getTotalCost()
	{
		$totalCost = 0;

		if ($this->dealQuantity == 0)
		{
			// no deal, multiply the price by the number of selected units
			$totalCost = $this->getPrice() * $this->getQuantity();
		}
		else
		{
			// deal found, subtract the number of items affected by a deal from the total quantity
			// in order to obtain the units not covered by a discount
			$diff = max(0, $this->getQuantity() - $this->dealQuantity);

			// apply the discount price to the number of affected item and the original price
			// to the remaining ones
			$totalCost = $this->getPrice() * $this->dealQuantity + $this->getOriginalPrice() * $diff;
		}

		// iterate toppings
		foreach ($this->getToppingsGroups() as $group)
		{
			// multiply total cost by the selected item quantity
			$totalCost += $group->getTotalCost() * $this->getQuantity();
		}

		/**
		 * Plugins attached to this event can change the calculated total at runtime.
		 *
		 * Note. Calling $item->getTotalCost() in this event will result in recursion.
		 *
		 * @param   float  &$total  The item total cost.
		 * @param   Item   $item    The item instance.
		 *
		 * @return  void
		 *
		 * @since   1.8.2
		 */
		$this->dispatcher->trigger('onCalculateItemTotal', [&$totalCost, $this]);

		return $totalCost;
	}

	/**
	 * Returns the resulting total after applying the discounts.
	 *
	 * @param   Cart|null  $cart     When specified, the system will try to apply
	 *                               the discounts to the base price.
	 * @param   array      &$lookup  A lookup used to track the applied discounts.
	 *
	 * @return  float
	 */
	public function getDiscountedTotal(Cart $cart = null, &$lookup = [])
	{
		$price = $this->getTotalCost();

		if (!$price)
		{
			// the item has no price
			return 0;
		}

		// in case the cart was specified, check whether there are some discounts to apply
		if (!$cart)
		{
			// nope, use default price
			return $price;
		}

		// iterate all the available discounts
		foreach ($cart->deals() as $discount)
		{
			$old = $price;

			// apply discount on cascade
			$price = $discount->apply($price, $price, $this);

			// fetch discount ID
			$discountId = $discount->getDealID() ?: $discount->getType();

			if (!isset($lookup[$discountId]))
			{
				// create discount repository
				$lookup[$discountId] = 0;
			}

			// increase repo by subtracting the price after the discount
			// from the price before the discount
			$lookup[$discountId] += $old - $price;
		}

		// make sure the price is not lower than 0
		return max(0, $price);
	}

	/**
	 * Returns the total costs (net, tax, gross) of the item.
	 * 
	 * @param   Cart|null  $cart  When specified, the system will try to apply
	 *                            the discounts to the base price.
	 *
	 * @return  object
	 */
	public function getTotals(Cart $cart = null)
	{
		// get discounted total
		$price = $this->getDiscountedTotal($cart);

		// calculate totals
		return \E4J\VikRestaurants\Taxing\TaxesFactory::calculate($this->getItemID(), $price, [
			'subject' => 'takeaway.item',
		]);
	}
	
	/**
	 * Empties the topping groups of the item.
	 * 
	 * @return  self  This object to support chaining.
	 */
	public function clearGroups()
	{
		$this->toppingsGroups = [];

		return $this;
	}

	/**
	 * Returns the index of the specified topping group.
	 *
	 * @param   ToppingsGroup  $group  The group to search for.
	 *
	 * @return  int|bool       The index of the group on success, otherwise false. 
	 */
	public function indexOf(ToppingsGroup $group)
	{
		foreach ($this->toppingsGroups as $index => $g)
		{
			if ($g->equalsTo($group))
			{
				return $index;
			}
		}

		return false;
	}

	/**
	 * Pushes the specified topping group into the list.
	 * It is possible to push a topping group only if it is not yet contained in the list.
	 *
	 * @param   ToppingsGroup  $group  The group to insert.
	 *
	 * @return  bool  True on success, otherwise false.
	 */
	public function addToppingsGroup(ToppingsGroup $group)
	{
		if ($this->indexOf($group) === false)
		{
			$this->toppingsGroups[] = $group;

			return true;
		}

		return false;
	}
	
	/**
	 * Returns the list containing all the toppings groups.
	 *
	 * @return  ToppingsGroup[]  The list of topping groups.
	 */
	public function getToppingsGroups()
	{
		return $this->toppingsGroups;
	}
	
	/**
	 * Checks whether this object is equal to the specified item.
	 * Two items are equal if they have the same ID, the same variation ID,
	 * the same additional notes and the groups contained in both the lists
	 * are the same.
	 *
	 * @param   Item  $item  The item to check.
	 *
	 * @return  bool  True if the 2 objects are equal, otherwise false.
	 */
	public function equalsTo(Item $item)
	{
		if ($this->getItemID() != $item->getItemID() 
			|| $this->getOptionID() != $item->getOptionID() 
			|| $this->getAdditionalNotes() != $item->getAdditionalNotes())
		{
			// different item details
			return false;
		}
			
		$l1 = $this->getToppingsGroups();
		$l2 = $item->getToppingsGroups();

		if (count($l1) != count($l2))
		{
			// not the same number of toppings groups
			return false;
		}

		// repeat until the count is reached or there is a different group
		for ($i = 0; $i < count($l1); $i++)
		{
			$found = false;

			// repeat until the group is found.
			for ($j = 0; $j < count($l2) && !$found; $j++)
			{
				// if true, break the statement
				$found = $l1[$i]->equalsTo($l2[$j]);
			}

			if (!$found)
			{
				// topping group not found
				return false;
			}
		}

		return true;
	}
}
