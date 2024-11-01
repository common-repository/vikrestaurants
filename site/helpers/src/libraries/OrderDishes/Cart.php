<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\OrderDishes;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Used to handle the dishes cart of the program.
 * This class cannot be instantiated manually as we can have only one instance per session.
 *
 * Usage:
 * $cart = Cart::getInstance($reservationId);
 *
 * @since 1.8
 * @since 1.9  Renamed from VREDishesCart.
 */
class Cart
{	
	/**
	 * The list containing the cart items.
	 *
	 * @var Item[]
	 */
	private $cart = [];

	/**
	 * The reservation ID.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * A pool of cart instances for each visited reservation.
	 *
	 * @var array
	 */
	private static $instances = [];
	
	/**
	 * Class constructor.
	 *
	 * @param  int    $id     The reservation ID.
	 * @param  array  $items  The items list.
	 */
	protected function __construct(int $id, array $items = [])
	{
		// this method can be accessed only internally.
		$this->id = $id;

		// set up cart items
		$this->setCart($items);
	}

	/**
	 * Class cloner.
	 */
	protected function __clone()
	{
		// this method is not accessible
	}

	/**
	 * Get the instance of the cart object.
	 * If the instance is not yet available, create a new one.
	 * 
	 * @param   int   $id  The reservation ID.
	 *
	 * @return  self  The instance of the VREDishesCart.
	 */
	public static function getInstance(int $id)
	{
		if (!isset(static::$instances[$id]))
		{
			// get cart from session
			$cartSession = \JFactory::getSession()->get('vre.dishes.cart.' . $id, null, 'vikrestaurants');

			$items = [];

			$dbo = \JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('id'))
				->from($dbo->qn('#__vikrestaurants_res_prod_assoc'))
				->where($dbo->qn('id_reservation') . ' = ' . $id)
				->order($dbo->qn('id') . ' ASC');

			$dbo->setQuery($q);
			
			// iterate all records
			foreach ($dbo->loadColumn() as $record_id)
			{
				try
				{
					// init record and push it within the list
					$items[] = new RecordItem($record_id);
				}
				catch (\Exception $e)
				{
					// go ahead in case of errors
				}
			}

			if (empty($cartSession))
			{
				// create new cart instance
				$cart = new static($id, $items);
			}
			else
			{
				// unserialize from session
				$cart = unserialize($cartSession);

				// prepend items at the beginning of the cart
				$cart->unshiftItem($items);
			}

			// cache cart instance
			static::$instances[$id] = $cart;
		}

		return static::$instances[$id];
	}

	/**
	 * Stores all the pending items within the database for
	 * being accessed by the kitchen too.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function transmit()
	{
		// get reservation-product model
		$model = \JModelVRE::getInstance('resprod');

		$totals = new \stdClass;
		$totals->net   = 0;
		$totals->tax   = 0;
		$totals->gross = 0;

		// iterate all items
		foreach ($this->cart as $item)
		{
			// check if we have a volatile dish
			if ($item->getRecordID() == 0)
			{
				$itemTotals = $item->getTotals();

				// prepare data to save
				$data = [
					'id'                => 0,
					'id_reservation'    => $this->id,
					'id_product'        => $item->id,
					'id_product_option' => $item->id_option,
					'name'              => $item->getFullName(),
					'quantity'          => $item->getQuantity(),
					'price'             => $item->getPrice(),
					'net'               => $itemTotals->net,
					'tax'               => $itemTotals->tax,
					'gross'             => $itemTotals->gross,
					'tax_breakdown'     => $itemTotals->breakdown,
					'notes'             => $item->getAdditionalNotes(),
					'servingnumber'     => $item->getServingNumber(),
				];

				// save data
				$id = $model->save($data);

				if ($id)
				{
					// Register record ID. When storing the cart this item won't be kept anymore within the
					// session as it owns an ID higher than 0. Then, at the next request the item will be
					// loaded from the database and it will belong to the VREDishesRecord class.
					$item->setRecordID($id);

					// increase totals
					$totals->net   += $itemTotals->net;
					$totals->tax   += $itemTotals->tax;
					$totals->gross += $itemTotals->gross;

					// unset modified state
					$item->modified(false);
				}
			}
		}

		if ($totals->gross != 0)
		{
			// update reservation bill
			$this->updateReservationTotals($totals);
		}

		return $this;
	}

	/**
	 * Store this instance into the PHP session.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function store()
	{
		// get reservation-product model
		$model = \JModelVRE::getInstance('resprod');

		$totals = new \stdClass;
		$totals->net   = 0;
		$totals->tax   = 0;
		$totals->gross = 0;

		foreach ($this->cart as $item)
		{
			if ($item->isModified() && $item->getRecordID())
			{
				$itemTotals = $item->getTotals();

				// prepare data to save
				$data = [
					'id'                => $item->getRecordID(),
					'name'              => $item->getFullName(),
					'quantity'          => $item->getQuantity(),
					'price'             => $item->getPrice(),
					'net'               => $itemTotals->net,
					'tax'               => $itemTotals->tax,
					'gross'             => $itemTotals->gross,
					'tax_breakdown'     => $itemTotals->breakdown,
					'notes'             => $item->getAdditionalNotes(),
					'id_product_option' => $item->id_option,
				];

				// save data
				if ($model->save($data))
				{
					// obtain the previous totals to avoid adding the item cost more than once
					$itemPrevTotals = $item->getCurrentTotals();

					// increase totals
					$totals->net   += $itemTotals->net - $itemPrevTotals->net;
					$totals->tax   += $itemTotals->tax - $itemPrevTotals->tax;
					$totals->gross += $itemTotals->gross - $itemPrevTotals->gross;

					// unset modified state
					$item->modified(false);
				}
			}
		}

		if ($totals->gross != 0)
		{
			// update reservation bill
			$this->updateReservationTotals($totals);
		}

		// save cart in session
		\JFactory::getSession()->set('vre.dishes.cart.' . $this->id, serialize($this), 'vikrestaurants');

		return $this;
	}

	/**
	 * Set the items into the array.
	 *
	 * @param   Item[]  $items  The items array.
	 *                          Each element must be an instance of Item,
	 *                          otherwise it will be ignored.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setCart(array $items)
	{
		$this->emptyCart();

		$this->addItem($items);

		return $this;
	}
	
	/**
	 * Empty the items and the deals stored in the cart.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function emptyCart()
	{
		$this->cart = [];

		return $this;
	}
	
	/**
	 * Check if the cart doesn't contain elements.
	 *
	 * @return 	bool  True if has no element, otherwise false.
	 */
	public function isEmpty()
	{
		if (count($this->cart) == 0)
		{
			return true;
		}
		
		foreach ($this->cart as $i)
		{
			if ($i->getQuantity() > 0)
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Prepends a new item at the beginning of the cart.
	 * 
	 * @param   mixed  $item  Either the item to prepend or an array.
	 *
	 * @return  self   This object to support chaining. 
	 */
	public function unshiftItem($item)
	{	
		if (!is_array($item))
		{
			// always treat as array
			$item = [$item];
		}
		else
		{
			// reverse item to prepend them following
			// the current ordering
			$item = array_reverse($item);
		}

		foreach ($item as $i)
		{
			if ($i instanceof Item)
			{
				// add item to cart
				array_unshift($this->cart, $i);
			}
		}

		return $this;
	}
	
	/**
	 * Adds a new item into the cart. 
	 * 
	 * @param   mixed  $item  Either the item to push or an array.
	 *
	 * @return  self   This object to support chaining. 
	 */
	public function addItem($item)
	{
		if (!is_array($item))
		{
			// always treat as array
			$item = [$item];
		}

		foreach ($item as $i)
		{
			if ($i instanceof Item)
			{
				// add item to cart
				$this->cart[] = $i;
			}
		}

		return $this;
	}
	
	/**
	 * Gets the item at the specified position.
	 *
	 * @param   int   $index  The index of the item.
	 *
	 * @return  Item  The item found on success, otherwise null.
	 */
	public function getItemAt(int $index)
	{
		if (isset($this->cart[$index]))
		{
			return $this->cart[$index];
		}
		
		return null;
	}

	/**
	 * Removes the item at the specified index.
	 *
	 * @param   int   $index  The index of the item to remove.
	 * @param   int   $units  The units of the item to remove.
	 *
	 * @return  bool  True on success, otherwise false.
	 */
	public function removeItemAt(int $index, int $units = 1)
	{
		// check if the item exists
		if (($item = $this->getItemAt($index)) !== null && $units > 0)
		{
			// keep totals before removing the units
			$totals = $item->getTotals();

			// decrease the item quantity by the specified number of units
			$remaining = $item->remove($units);

			if ($remaining == 0)
			{
				// no more units left, permanently remove from cart
				unset($this->cart[$index]);

				if ($totals->gross > 0 && $item->getRecordID())
				{
					// update reservation bill
					$this->updateReservationTotals($totals, -1);
				}
			}

			return true;
		}
		
		return false;
	}
	
	/**
	 * Gets the index of the specified item.
	 *
	 * @param   Item  $item  The item to find.
	 *
	 * @return  int   The index found on success, otherwise -1.
	 */
	public function indexOf(Item $item)
	{
		foreach ($this->cart as $k => $i)
		{
			if ($i->equalsTo($item))
			{
				return $k;
			}
		}

		return -1;
	}

	/**
	 * Removes the specified item found.
	 *
	 * @param   Item  $item   The item to remove
	 * @param   int   $units  The units of the item to remove.
	 *
	 * @return  bool  True on success, otherwise false.
	 */
	public function removeItem(Item $item, int $units = 1)
	{
		// check if the item exists
		if (($index = $this->indexOf($item)) != -1)
		{
			// remove item units
			return $this->removeItemAt($index, $units);
		}

		return false;
	}
	
	/**
	 * Returns the total count of items within the cart.
	 *
	 * @return  int  The size of the cart.
	 */
	public function getLength()
	{
		return count($this->cart);
	}
	
	/**
	 * Returns the list of all the items within cart.
	 *
	 * @return  array  The list of the items.
	 */
	public function getItemsList()
	{	
		return $this->cart;
	}

	/**
	 * Returns the base total cost of the cart by summing
	 * the base cost of each item.
	 *
	 * @return  float  The base total cost.
	 */
	public function getTotalCost()
	{
		$total = 0;

		foreach ($this->cart as $item)
		{
			$total += $item->getTotalCost();
		}
		
		return $total;
	}

	/**
	 * Returns the total costs (net, tax, gross) of the item.
	 *
	 * @return  object
	 * 
	 * @since  1.9
	 */
	public function getTotals()
	{
		$totals = new \stdClass;
		$totals->net   = 0;
		$totals->tax   = 0;
		$totals->gross = 0;

		foreach ($this->cart as $item)
		{
			$tmp = $item->getTotals();

			$totals->net   += $tmp->net;
			$totals->tax   += $tmp->tax;
			$totals->gross += $tmp->gross;
		}
		
		return $totals;
	}

	/**
	 * Updates the reservation totals.
	 * 
	 * @param   object  $totals  An object holding the net, tax and gross amounts.
	 * @param   int     $factor  Whether the totals should be added (1) or removed (-1).
	 * 
	 * @throws  \Exception
	 * 
	 * @since   1.9
	 */
	protected function updateReservationTotals(object $totals, int $factor = 1)
	{
		// get reservation model
		$reservationModel = \JModelVRE::getInstance('reservation');

		// fetch reservation details
		$reservation = $reservationModel->getItem($this->id);

		if (!$reservation)
		{
			// reservation not found
			throw new \Exception('Reservation [' . $this->id . '] not found.', 404);
		}

		// update reservation bill too
		$reservationModel->save([
			'id'         => $this->id,
			'total_net'  => $reservation->total_net  + $totals->net   * $factor,
			'total_tax'  => $reservation->total_tax  + $totals->tax   * $factor,
			'bill_value' => $reservation->bill_value + $totals->gross * $factor,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function __serialize()
	{
		$vars = get_object_vars($this);

		// strip all the items stored in the database
		$vars['cart'] = array_values(array_filter($vars['cart'], function($item)
		{
			return $item->getRecordID() == 0;
		}));

		// re-balance cart keys
		$this->cart = array_values($this->cart);

		return $vars;
	}

	/**
	 * @inheritDoc
	 */
	public function __unserialize($vars)
	{
		// construct the object
		foreach ($vars as $k => $v)
		{
			$this->{$k} = $v;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function __toString()
	{
		return '<pre>' . print_r($this, true) . '</pre><br />Total Cost = ' . $this->getTotalCost() . '<br />';
	}
}
