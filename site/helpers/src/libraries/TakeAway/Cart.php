<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\TakeAway;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\TakeAway\Cart\Item;
use E4J\VikRestaurants\TakeAway\Cart\Deals;

/**
 * Used to handle the take-away cart of the program.
 *
 * @since 1.7
 * @since 1.9  Renamed from TakeAwayCart.
 */
class Cart implements \IteratorAggregate, \Countable
{	
	/**
	 * The list containing the TakeAwayItem objects.
	 *
	 * @var Item[]
	 */
	protected $cart = [];

	/**
	 * The object to handle all the deals found.
	 *
	 * @var Deals
	 */
	protected $deals;
	
	/**
	 * The check-in date timestamp of the order.
	 *
	 * @var int|null
	 */
	protected $checkin = null;

	/**
	 * The check-in time of the order.
	 *
	 * @var string|null
	 * @since 1.8
	 */
	protected $time = null;

	/**
	 * The selected service.
	 *
	 * @var string|null
	 * @since 1.8
	 */
	protected $service = null;
	
	/**
	 * The array containing all the settings of the cart.
	 * 
	 * - maxsize  int  The max number of items, or -1 for unlimited size.
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * The instance of the Cart.
	 * There should be only one cart instance for the whole session.
	 *
	 * @var Cart
	 * @since 1.7
	 */
	protected static $instance = null;

	/**
	 * Returns the instance of the cart object, only creating it
	 * if doesn't exist yet.
	 * 
	 * @param   array  $params  The settings array.
	 *
	 * @return  self   The instance of the cart.
	 */
	public static function getInstance(array $params = [])
	{
		if (static::$instance === null)
		{
			// get cart from session
			$session_cart = \JFactory::getSession()->get(self::CART_SESSION_KEY, '', 'vikrestaurants');

			if (empty($session_cart))
			{
				$cart = new static();
			}
			else
			{
				$cart = unserialize($session_cart);
			}

			static::$instance = $cart;
		}

		// always overwrite existing params
		static::$instance->setParams($params);

		return static::$instance;
	}
	
	/**
	 * Class constructor.
	 *
	 * @param  Item[]  $cart    The array containing all the items to push.
	 * @param  array   $params  The settings array.
	 */
	public function __construct(array $cart = [], array $params = [])
	{	
		$this->deals = new Deals;

		$this->setCart($cart);
		$this->setParams($params);
	}

	/**
	 * Store this instance into the PHP session.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function store()
	{
		\JFactory::getSession()->set(self::CART_SESSION_KEY, serialize($this), 'vikrestaurants');

		return $this;
	}
	
	/**
	 * Set the configuration params of the object.
	 *
	 * @param   array  $params  The settings array.
	 *                          This array accepts only [maxsize] key.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setParams(array $params)
	{
		// add/update parameters one by one
		foreach ($params as $k => $v)
		{
			$this->params[$k] = $v;
		}

		if (!isset($this->params[self::MAX_SIZE]))
		{
			// force unlimited size if not specified
			$this->params[self::MAX_SIZE] = \VREFactory::getConfig()->getUint('tkmaxitems');
		}

		return $this;
	}

	/**
	 * Sets the items into the array.
	 *
	 * @param   Item[]  $items  The items array. Each element must be an instance
	 *                          of Item, otherwise it will be ignored.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setCart(array $items)
	{
		$this->clear();

		foreach ($items as $item)
		{
			if ($item instanceof Item)
			{
				$this->addItem($item);
			}
		}

		return $this;
	}

	/**
	 * Returns the maximum size of the cart.
	 *
	 * @return  int  The maximum number of items, otherwise -1 for unlimited size.
	 */
	public function getMaxSize()
	{
		return $this->params[self::MAX_SIZE];
	}
	
	/**
	 * Sets the checkin timestamp.
	 *
	 * @param   int   $ts  The checkin timestamp.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setCheckinTimestamp(int $ts)
	{
		$this->checkin = $ts;

		return $this;
	}

	/**
	 * Returns the check-in date timestamp.
	 * In case the check-in date timestamp is not set, the first
	 * available date for bookings will be returned.
	 *
	 * @return  int  The check-in date timestamp.
	 */
	public function getCheckinTimestamp()
	{
		if (\E4J\VikRestaurants\Helpers\DateHelper::isNull($this->checkin))
		{
			/**
			 * Get minimum number of days required to book in advace.
			 *
			 * @since 1.8
			 */
			$days = \VREFactory::getConfig()->getUint('tkmindate', 0);

			// get current date
			$date = getdate(\VikRestaurants::now());

			// obtain the timestamp of the first available day
			return mktime(0, 0, 0, $date['mon'], $date['mday'] + $days, $date['year']);
		}

		return $this->checkin;
	}

	/**
	 * Sets the check-in time.
	 *
	 * @param   string|null  $time  The check-in time string in H:i format.
	 *
	 * @return  self         This object to support chaining.
	 */
	public function setCheckinTime(string $time = null)
	{
		$this->time = $time;

		return $this;
	}

	/**
	 * Returns the check-in time.
	 *
	 * @param   bool    $first  True to return the first available check-in time
	 *                          in case it is empty.
	 *
	 * @return  string  The check-in time string.
	 */
	public function getCheckinTime(bool $first = false)
	{
		if ($this->time === null && $first)
		{
			// get a valid check-in date
			$date = $this->getCheckinTimestamp();

			// fetch closest time
			$this->time = \VikRestaurants::getClosestTimeTakeAway($date, $next = true);

			// I think we should always re-assign $date to the cart check-in, as the
			// first available time might refer to the next day (see code below).
			$this->checkin = $date;
		}

		return (string) $this->time;
	}

	/**
	 * Sets the delivery service.
	 *
	 * @param   string  $service  The type of service applied.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setService(string $service)
	{
		// init special days manager
		$sdManager = new \VRESpecialDaysManager('takeaway');
		
		// filter special days by check-in time in order
		// to figure out what's the delivery service to
		// use for the selected time
		$sdManager->setCheckinTime($this->getCheckinTime(true));

		// set checkin date
		$sdManager->setStartDate($this->getCheckinTimestamp());

		// get special days
		$sd = $sdManager->getFirst();

		if ($sd)
		{
			// set up delivery/pickup service
			$delivery = $sd->delivery;
			$pickup   = $sd->pickup;
		}
		else
		{
			$delivery = $pickup = null;
		}

		// get delivery service flag from configuration
		$avail = \VREFactory::getConfig()->getUint('deliveryservice');

		if (is_null($delivery))
		{
			// unable to fetch delivery service from special days,
			// rely on default configuration
			$delivery = $avail == 1 || $avail == 2;
		}

		if (is_null($pickup))
		{
			// unable to fetch pickup service from special days,
			// rely on default configuration
			$pickup = $avail == 0 || $avail == 2;
		}

		if (!in_array($service, ['delivery', 'pickup']))
		{
			// invalid service, use the default one
			$service = \VREFactory::getConfig()->get('tkdefaultservice');
		}

		// validate selected service
		if ($service == 'delivery' && !$delivery)
		{
			// use pickup because delivery is disabled
			$service = 'pickup';
		}

		if ($service == 'pickup' && !$pickup)
		{
			// use delivery because pickup is disabled
			$service = 'delivery';
		}

		$this->service = $service;

		return $this;
	}

	/**
	 * Returns the selected delivery service.
	 * If not set the default one will be used.
	 *
	 * @return  string  The type of service.
	 */
	public function getService()
	{
		if ($this->service === null)
		{
			// set the default configuration service
			$this->setService('');
		}

		return $this->service;
	}
	
	/**
	 * Empty the items and the deals stored in the cart.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function clear()
	{
		$this->cart = [];

		$this->deals->clear();

		return $this;
	}
	
	/**
	 * Checks whether the cart doesn't contain any element.
	 *
	 * @return  bool  True if has no element, otherwise false.
	 */
	public function isEmpty()
	{
		if (count($this->cart) == 0)
		{
			// no items in cart
			return true;
		}
		
		// look for an item that has at least a unit available
		foreach ($this->cart as $item)
		{
			if ($item->getQuantity() > 0)
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Checks whether there's enough space to collect the specified item.
	 * An item can be added according to the maximum number of available spaces.
	 * Items that do not require a preparation are always excluded by this count.
	 * 
	 * @param   Item  $item  The item that should be added.
	 * 
	 * @return  bool  True in case it can be added, false otherwise.
	 */
	public function canAddItem(Item $item)
	{
		if ($item->isReady())
		{
			// the item does not need a preparation, bypass restriction
			return true;
		}

		$maxSize = $this->getMaxSize();

		if ($maxSize === self::UNLIMITED)
		{
			// no restriction applied
			return true;
		}

		// the current number of items that need a preparation, added to the units of the item that
		// should be added, must be equals or lower than the maximum allowed size
		return $this->getPreparationItemsQuantity() + $item->getQuantity() <= $maxSize;
	}
	
	/**
	 * Attempt to add a new item into the cart.
	 * An item is not pushed when the size of the cart is full.
	 * 
	 * @param   Item      $item  The item to push.
	 *
	 * @return  int|bool  The index of the array on success, otherwise false.
	 */
	public function addItem(Item $item)
	{	
		if ($this->canAddItem($item))
		{
			$this->cart[] = $item;

			return count($this->cart) - 1;
		}
		
		return false;
	}
	
	/**
	 * Returns the item at the specified position.
	 *
	 * @param   int        $index  The index of the item.
	 *
	 * @return 	Item|null  The item found on success, otherwise null.
	 */
	public function getItemAt(int $index)
	{
		if (!isset($this->cart[$index]))
		{
			// item not set
			return null;
		}

		if ($this->cart[$index]->getQuantity() < 1)
		{
			// item no more available
			return null;
		}

		return $this->cart[$index];
	}

	/**
	 * Removes the item at the specified index.
	 *
	 * @param   int   $index  The index of the item to remove.
	 * @param   int   $units  The units of the item to remove.
	 *
	 * @return 	bool  True on success, otherwise false.
	 */
	public function removeItemAt(int $index, int $units = 1)
	{
		if ($units <= 0)
		{
			// invalid number of units
			throw new \InvalidArgumentException('Cannot decrease by 0 units');
		}

		// get item at the specified position
		$item = $this->getItemAt($index);

		if (!$item)
		{
			// item not found
			return false;
		}

		// remove the specified units
		$item->remove($units);

		if ($this->isEmpty())
		{
			// no more items in cart, reset list
			$this->clear();
		}

		return true;
	}
	
	/**
	 * Returns the index of the specified item.
	 *
	 * @param   Item      $item  The item to find.
	 *
	 * @return  int|bool  The index found on success, otherwise false.
	 */
	public function indexOf(Item $item)
	{
		foreach ($this->cart as $k => $i)
		{
			if ($i->getQuantity() > 0 && $i->equalsTo($item))
			{
				return $k;
			}
		}

		return false;
	}

	/**
	 * Removes the specified item found.
	 *
	 * @param   Item  $item	  The item to remove
	 * @param   int   $units  The units of the item to remove.
	 *
	 * @return 	bool  True on success, otherwise false.
	 */
	public function removeItem(Item $item, int $units = 1)
	{
		if (($index = $this->indexOf($item)) !== false && $index >= 0)
		{
			return $this->removeItemAt($index, $units);
		}

		return false;
	}
	
	/**
	 * Get the list of all the valid items in cart.
	 *
	 * @return 	array 	The list of the items.
	 */
	public function getItems()
	{
		// Take only the items with valid quantity.
		// NOTE: we must preserve the keys as they are because
		// they could be used to track the index of the items.
		return array_filter($this->cart, function($item)
		{
			return $item->getQuantity() > 0;
		});
	}

	/**
	 * Returns the number of items that need a preparation.
	 *
	 * @return  int  The preparation items count.
	 */
	public function getPreparationItemsQuantity()
	{
		$count = 0;

		foreach ($this->cart as $k => $i)
		{
			if ($i->getQuantity() > 0 && !$i->isReady())
			{
				$count += $i->getQuantity();
			}
		}

		return $count;
	}

	/**
	 * Returns the total quantity of the specified item and variation in cart.
	 *
	 * @param   int  $idItem    The ID of the item.
	 * @param   int  $idOption  The ID of the variation.
	 *
	 * @return  int  The total quantity.
	 */
	public function getQuantityItems(int $idItem, int $idOption)
	{
		$count = 0;
		
		foreach ($this->cart as $k => $i)
		{
			if ($i->getItemID() == $idItem && ($idOption <= 0 || $i->getOptionID() == $idOption))
			{
				$count += $i->getQuantity();
			}
		}
		
		return $count;
	}
	
	/**
	 * Returns the list of current deals.
	 *
	 * @return  Deals  The deals list.
	 */
	public function deals()
	{
		/**
		 * @todo Is this really needed? Because the deals are always initialized by the constructor and
		 *       there are no setters that could alter them from the outside. Maybe this security measure
		 *       was introduced to prevent errors in case the unserialization was unable to properly
		 *       prepare the deals property. Don't know, just my assumption.
		 *       
		 */
		if ($this->deals === null)
		{
			$this->deals = new Deals;
		}

		return $this->deals;
	}

	/**
	 * Returns the base total cost of the cart by summing the base cost of each item.
	 *
	 * @return  float  The base total cost.
	 */
	public function getTotalCost()
	{
		$total = 0;

		foreach ($this->getItems() as $item)
		{
			$total += $item->getTotalCost();
		}

		return $total;
	}

	/**
	 * Configures the discount objects before being used.
	 *
	 * @return  self
	 */
	protected function prepareDiscounts()
	{
		$count = 0;

		// counts the total number of items that have a cost
		foreach ($this->getItems() as $item)
		{
			if ($item->getPrice() > 0)
			{
				// item with cost, increase counter
				$count++;
			}
		}

		foreach ($this->deals as $discount)
		{
			$discount->prepare([
				// reset internal index
				'count' => 0,
				// reset internal total discount
				'disctot' => 0,
				// set total number of items with cost
				'length' => $count,
				// register the total cost of the order
				'total' => $this->getTotalCost(),
			]);
		}

		return $this;
	}

	/**
	 * Returns the totals (net, tax, gross) of the cart.
	 *
	 * @return  object
	 */
	public function getTotals()
	{
		$this->prepareDiscounts();

		$totals = new \stdClass;
		$totals->net   = 0;
		$totals->tax   = 0;
		$totals->gross = 0;

		foreach ($this->getItems() as $item)
		{
			// calculate item totals
			$tmp = $item->getTotals($this);

			// increate totals
			$totals->net   += $tmp->net;
			$totals->tax   += $tmp->tax;
			$totals->gross += $tmp->gross;
		}
		
		return $totals;
	}

	/**
	 * Returns the total discount.
	 *
	 * @param   array  &$lookup  A lookup used to track the applied discounts.
	 *
	 * @return  float
	 */
	public function getTotalDiscount(&$lookup = [])
	{
		$this->prepareDiscounts();

		$total = 0;

		foreach ($this->getItems() as $item)
		{
			// calculate the difference between the item full price
			// and the discounted price, if any
			$total += $item->getTotalCost() - $item->getDiscountedTotal($this, $lookup);
		}
		
		return round($total, 2);
	}

	/**
	 * Returns the totals per each registered item and option.
	 *
	 * @return  array  An array of discounts, matching the index
	 *                 of the related item.
	 */
	public function getTotalsPerItem()
	{
		$this->prepareDiscounts();

		$items = [];

		foreach ($this->getItems() as $i => $item)
		{
			$itemTotals = new \stdClass;
			// calculate original price
			$itemTotals->priceBeforeDiscount = $item->getTotalCost();
			// calculate final price per item and related discount
			$itemTotals->price    = $item->getDiscountedTotal($this);
			$itemTotals->discount = $itemTotals->priceBeforeDiscount - $itemTotals->price;

			// re-calculate totals of discounted item
			$tmp = \E4J\VikRestaurants\Taxing\TaxesFactory::calculate($item->getItemID(), $itemTotals->price, [
				'subject' => 'takeaway.item',
			]);

			// register new totals inside the object
			foreach ($tmp as $k => $v)
			{
				$itemTotals->{$k} = $v;
			}

			// Register item within the return list.
			// NOTE: we must preserve the index of the array in order
			// to properly recognize the matching item.
			$items[$i] = $itemTotals;
		}
		
		return $items;
	}

	/**
	 * Returns the total discount per each registered offer.
	 *
	 * @return  array  A lookup of discounts, where the key is the
	 *                 title/ID and the value is the discount.
	 */
	public function getTotalDiscountPerOffer()
	{
		// pass a junk variable to the method used to calculate the
		// total discount per each offer
		$this->getTotalDiscount($lookup);

		$map = [];
		
		// iterate all registered discounts
		foreach ($this->deals as $discount)
		{
			// fetch discount identifier
			$id = $discount->getDealID() ?: $discount->getType();

			if (!isset($lookup[$id]))
			{
				// discount not set, go ahead
				continue;
			}

			// try to check whether the discount supports a readable title
			$k = $discount->getTitle();

			// register discount total
			$map[$k] = round($lookup[$id], 2);
		}

		return $map;
	}
	
	/**
	 * @inheritDoc
	 * 
	 * @see \Countable
	 */
	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->getItems());
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \IteratorAggregate
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator($this->getItems());
	}

	/**
	 * MAX_SIZE setting identifier.
	 *
	 * @var string
	 */
	const MAX_SIZE = 'maxsize';
	
	/**
	 * UNLIMITED cart size identifier.
	 *
	 * @var int
	 */
	const UNLIMITED = -1;

	/**
	 * CART_SESSION_KEY identifier for session key.
	 *
	 * @var string
	 * @since 1.7
	 * @since 1.9 Renamed from "vrecartdev".
	 */
	const CART_SESSION_KEY = 'takeaway.cart';	
}
