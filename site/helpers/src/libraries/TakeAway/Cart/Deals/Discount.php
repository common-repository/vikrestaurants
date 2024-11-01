<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\TakeAway\Cart\Deals;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Used to handle a take-away discount into the cart.
 *
 * @since 1.7
 * @since 1.9  Renamed from TakeAwayDiscount.
 */
class Discount
{	
	/**
	 * The ID of the deal.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The amount of the discount.
	 *
	 * @var float
	 */
	protected $amount;

	/**
	 * True whether the discount should be calculated in percentage.
	 * 
	 * @var boolean
	 */
	protected $percent;

	/**
	 * The quantity of this type of deal.
	 *
	 * @var int
	 */
	protected $quantity;

	/**
	 * The type of the deal.
	 * Null in case the type does not exist or it is not relevant.
	 * 
	 * @var string|null
	 */
	protected $type = null;

	/**
	 * The event dispatcher.
	 * 
	 * @var DispatcherInterface
	 */
	protected $dispatcher;

	/**
	 * A registry of configuration options.
	 * 
	 * @var \JRegistry
	 */
	protected $options;
	
	/**
	 * Class constructor.
	 *
	 * @param  array|object         $data        The discount data.
	 * @param  DispatcherInterface  $dispatcher  The events dispatcher instance.
	 */
	public function __construct($data, DispatcherInterface $dispatcher = null)
	{
		$data = (array) $data;

		$this->id       = (string) ($data['id'] ?? 0);
		$this->amount   = (float) ($data['amount'] ?? 0);
		$this->percent  = (bool) ($data['percent'] ?? false);
		$this->quantity = max(1, (int) ($data['quantity'] ?? 1));
		$this->type     = $data['type'] ?? null;

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

		$this->options = new \JRegistry;
	}

	/**
	 * Returns a human-readable title for the current discount.
	 * 
	 * @return  string
	 */
	public function getTitle()
	{
		$id = $this->getDealID();

		if ($id && is_numeric($id))
		{
			// fetch deal details
			$deal = \JModelVRE::getInstance('tkdeal')->getItem((int) $id);

			if ($deal->name)
			{
				// return the name configured for the redeemed deal
				return $deal->name;
			}
		}

		// deal not found, try to translate by type
		$langKey = 'VRE_DEALS_DISCOUNT_' . strtoupper((string) $this->type);
		$tx = \JText::translate($langKey);

		if ($langKey !== $tx)
		{
			// translation found
			return $tx;
		}

		// create title from type
		return ucfirst((string) ($this->type ?: $this->id));
	}
	
	/**
	 * Returns the ID of the deal.
	 *
	 * @return  string  The deal ID.
	 */
	public function getDealID()
	{
		return $this->id;
	}

	/**
	 * Returns the amount of the deal.
	 *
	 * @return  float  The deal amount.
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * Checks whether the amount type of the deal is percentage.
	 *
	 * @return  bool  True if percentage, otherwise false.
	 */
	public function isPercent()
	{
		return $this->percent;
	}

	/**
	 * Checks whether the amount type of the deal is total.
	 *
	 * @return  bool  True if total, otherwise false.
	 */
	public function isTotal()
	{
		return $this->isPercent() === false;
	}
	
	/**
	 * Returns the quantity of the deal.
	 *
	 * @return  int  The deal quantity.
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}
	
	/**
	 * Sets the quantity of the deal.
	 *
	 * @param   int   $quantity  The quantity of the deal.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setQuantity(int $quantity)
	{
		$this->quantity = max(0, $quantity);

		return $this;
	}
	
	/**
	 * Add the specified units to the existing quantity of the deal.
	 *
	 * @param   int   $units  The units of the deal to add.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function addQuantity(int $units = 1)
	{
		$this->quantity += abs($units);

		return $this;
	}
	
	/**
	 * Removes the specified units from the existing quantity of the deal.
	 *
	 * @param   int   $units  The units of the deal to remove.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function removeQuantity(int $units = 1)
	{
		$this->quantity -= abs($units);

		if ($this->quantity < 0)
		{
			$this->quantity = 0;
		}

		return $this;
	}

	/**
	 * Sets the type of the deal.
	 *
	 * @param   string|null  $type  The deal type. Specify null if not relevant.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Returns the type of the deal.
	 *
	 * @return  string|null  The deal type, null if not relevant.
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * Checks whether this object is equal to the specified discount.
	 * Two deals are equal if they have the same ID and type.
	 *
	 * @param   Discount  $discount  The discount to check.
	 *
	 * @return  bool      True if the 2 objects are equal, otherwise false.
	 */
	public function equalsTo(Discount $discount)
	{
		return $this->getDealID() == $discount->getDealID() && $this->getType() == $discount->getType();
	} 

	/**
	 * Checks whether this object has same type of the specified discount.
	 *
	 * @param   Discount  $discount  The discount to check.
	 *
	 * @return 	bool      True if the 2 objects have same type, otherwise false.
	 */
	public function sameType(Discount $discount)
	{
		return $this->type !== null && $this->getType() == $discount->getType();
	}

	/**
	 * Prepares the discount before calling the "apply" method.
	 * 
	 * @param   array  $data  A configuration array.
	 * 
	 * @return  void
	 */
	public function prepare(array $data = [])
	{
		// reset internal index
		$this->options->set('count', 0);
		// reset internal total discount
		$this->options->set('disctot', 0);
		// set total number of items with cost
		$this->options->set('length', (int) ($data['length'] ?? 0));
		// register the total cost of the order
		$this->options->set('total', (float) ($data['total'] ?? 0));
	}

	/**
	 * Applies a discount to the specified amount.
	 *
	 * @param   float  $amount  The amount to discount.
	 * @param   float  $base    The initial price of the item.
	 * @param   mixed  $item    The item/option to discount.
	 *
	 * @return  float  The resulting amount.
	 */
	public function apply(float $amount, float $base, $item)
	{
		// get internal count
		$count = (int) $this->options->get('count', 0);

		// immediately increase internal count by one because external plugins
		// might prevent the application of the discount
		$this->options->set('count', ++$count);

		/**
		 * Trigger event to let external plugins prevent the application of the
		 * discount at runtime. Useful in example to ignore the discount for
		 * certain items and options.
		 *
		 * @param   self   $discount  The current discount instance.
		 * @param   float  $amount    The current amount to discount.
		 * @param   mixed  $item      The item to discount.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$result = $this->dispatcher->filter('onBeforeApplyCartDiscount', [$this, $amount, $item]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		if ($result->isFalse())
		{
			// do not apply the discount
			return $amount;
		}

		// get total discount already applied
		$applied = (float) $this->options->get('disctot', 0);

		if ($this->isPercent())
		{
			// calculate percentage discount
			$disc_val = ($amount * $this->amount / 100.0) * $this->quantity;
		}
		else
		{
			// get total number of itema
			$length = (int) $this->options->get('length', 0);

			if ($count < $length)
			{
				// Fixed discount, apply proportionally according to
				// the total cost of the items. Since the discounts
				// might be applied on cascade, we need to calculate
				// the proportion on the base cost of the item.
				$percentage = $base * 100 / (float) $this->options->get('total', 0);
				$disc_val   = ($this->amount * $percentage / 100) * $this->quantity;
			}
			else
			{
				// We are fetching the last element of the list, instead of calculating the
				// proportional discount, we should subtract the total discount from the coupon
				// value, in order to avoid rounding issues. Let's take as example a coupon of
				// EUR 10 applied on 3 items. The final result would be 3.33 + 3.33 + 3.33,
				// which won't match the initial discount value of the coupon. With this
				// alternative way, the result would be: 10 - 3.33 - 3.33 = 3.34.
				$disc_val = ($this->amount * $this->quantity) - $applied;
			}
		}

		// always round discount to 2 decimals
		$disc_val = round($disc_val, 2);

		/**
		 * Trigger event to let external plugins alter the discount to apply at runtime.
		 *
		 * @param   float  &$value    The calculated discount value.
		 * @param   self   $discount  The current discount instance.
		 * @param   float  $amount    The current amount to discount.
		 * @param   mixed  $item      The item to discount.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$this->dispatcher->trigger('onAfterApplyCartDiscount', [&$disc_val, $this, $amount, $item]);

		// discount value cannot be lower than 0 and cannot be higher than the total amount to discount
		$disc_val = max($disc_val, 0);
		$disc_val = min($disc_val, $amount);

		// subtract discount from amount
		$amount -= $disc_val;

		// update internal total discount
		$this->options->set('disctot', $applied + $disc_val);

		return $amount;
	}
}
