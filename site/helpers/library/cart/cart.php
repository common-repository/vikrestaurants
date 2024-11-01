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
 * Used to handle the take-away cart of the program.
 * This class cannot be instantiated manually as we can have only one instance per session.
 *
 * Usage:
 * $cart = TakeAwayCart::getInstance();
 *
 * or:
 * $items 	= array();
 * $config 	= array('max_size' => 20);
 *
 * $cart = TakeAwayCart::getInstance($items, $config);
 *
 * @see TakeAwayItem   To handle take-away item objects.
 * @see TakeAwayDeals  To handle a list of deals.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\TakeAway\Cart instead.
 */
class TakeAwayCart extends E4J\VikRestaurants\TakeAway\Cart
{	
	/**
	 * Get the instance of the TakeAwayCart object.
	 * If the instance is not yet available, create a new one.
	 * 
	 * @param 	array 	$cart 	The array containing all the items to push.
	 * @param 	array 	$params The settings array.
	 *
	 * @return 	self 	The instance of the TakeAwayCart.
	 *
	 * @since 	1.7
	 */
	public static function getInstance(array $cart = [], array $params = [])
	{
		if (static::$instance === null)
		{
			// get cart from session
			$session_cart = JFactory::getSession()->get(self::CART_SESSION_KEY, '', 'vikrestaurants');

			if (empty($session_cart))
			{
				$cart = new static($cart, $params);
			}
			else
			{
				$cart = unserialize($session_cart);
				// params should have been stored too
				//$cart->setParams($params);
			}

			static::$instance = $cart;
		}

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
		parent::__construct($cart, $params);

		$this->deals = new TakeAwayDeals;
	}
	
	/**
	 * Set the max size of the cart.
	 * If you don't need a maximum size, just specify -1.
	 *
	 * @param 	integer  $max_size 	The maximum number of items, 
	 *								otherwise -1 for unlimited size.
	 *
	 * @return 	self 	 This object to support chaining.
	 */
	public function setMaxSize($max_size)
	{
		$this->setParams([
			self::MAX_SIZE => $max_size,
		]);

		return $this;
	}

	/**
	 * Returns the index of the specified item.
	 *
	 * @param   Item      $item  The item to find.
	 *
	 * @return  int|bool  The index found on success, otherwise false.
	 */
	public function indexOf(E4J\VikRestaurants\TakeAway\Cart\Item $item)
	{
		$index = parent::indexOf($item);
		return $index === false ? -1 : $index;
	}

	/**
	 * Removes the specified item found.
	 *
	 * @param   Item  $item	  The item to remove
	 * @param   int   $units  The units of the item to remove.
	 *
	 * @return 	bool  True on success, otherwise false.
	 */
	public function removeItem(E4J\VikRestaurants\TakeAway\Cart\Item $item, int $units = 1)
	{
		if (($index = $this->indexOf($item)) !== -1)
		{
			return $this->removeItemAt($index, $units);
		}

		return false;
	}
	
	/**
	 * Empty the items and the deals stored in the cart.
	 *
	 * @return 	self  This object to support chaining.
	 *
	 * @uses 	emptyDiscount()
	 */
	public function emptyCart()
	{
		return $this->clear();
	}
	
	/**
	 * Get the current size of the cart, including the item without quantity.
	 * @protected This method should be used only for internal purposes.
	 *
	 * @return 	integer  The size of the cart.
	 */
	protected function getCartLength()
	{
		return count($this);
	}
	
	/**
	 * Get the real size of the cart.
	 * Consider only the items with quantity equals or higher than 1.
	 *
	 * @return 	integer  The real size of the cart.
	 */
	public function getCartRealLength()
	{
		return count($this->getItems());
	}
	
	/**
	 * Get the list of all the valid items in cart.
	 *
	 * @return 	array 	The list of the items.
	 */
	public function getItemsList()
	{
		return $this->getItems();
	}

	/**
	 * Get the total taxes of the cart.
	 *
	 * @return 	float  The total taxes.
	 *
	 * @since   1.7
	 */
	public function getTaxes()
	{
		return $this->getTotals()->tax;
	}

	/**
	 * Get the real total net of the cart, by substracting the taxes from the grand total.
	 *
	 * @return 	float 	 The real total net.
	 *
	 * @since   1.7
	 */
	public function getRealTotalNet()
	{
		return $this->getTotals()->net;
	}
	
	/**
	 * Get the real grand total of the cart, by summing the real total net and the real total taxes.
	 * In case there is a discount, taxes need to be recalculated proportionally.
	 *
	 * @return 	float 	 The real grand total.
	 *
	 * @since   1.7
	 */
	public function getRealTotalCost($use_taxes = false)
	{
		return $this->getTotals()->gross;
	}

	/**
	 * Get the real total taxes of the cart.
	 * 
	 * @param 	boolean  $use_taxes  True if taxes are excluded, otherwise false.
	 *
	 * @return 	float 	 The total taxes.
	 *
	 * @uses 	getTotalCost()
	 * @uses 	getTotalDiscount()
	 * @uses 	getTaxes()
	 * @uses 	getRealTotalNet()
	 * @uses 	getRealTotalCost()
	 *
	 * @since 	1.7
	 */
	public function getRealTotalTaxes($use_taxes = false)
	{
		return $this->getTaxes();
	}
	
	/**
	 * Magic toString method to debug the cart contents.
	 *
	 * @return  string  The debug string of the cart.
	 *
	 * @since   1.7
	 */
	public function __toString()
	{
		return '<pre>' . print_r($this, true) . '</pre><br />Total Cost = ' . $this->getTotalCost() . '<br />';
	}
}
