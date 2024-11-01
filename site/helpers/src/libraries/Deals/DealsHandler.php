<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DI\Container;
use E4J\VikRestaurants\DI\ContainerDecorator;
use E4J\VikRestaurants\TakeAway\Cart;

/**
 * Used to serve the available deals.
 *
 * @since 1.6
 * @since 1.7  Renamed from VRDealsHandler.
 * @since 1.9  Renamed from DealsHandler (without namespace).
 */
class DealsHandler
{
	/** @var \E4J\VikRestaurants\TakeAway\Cart */
	protected $cart;

	/** @var DealsFactory */
	protected $factory;

	/**
	 * Class constructor.
	 * 
	 * @param  Cart          $cart     The cart holding the purchased products.
	 * @param  DealsFactory  $factory  The factory used to fetch the available deal rules.
	 */
	public function __construct(Cart $cart = null, DealsFactory $factory = null)
	{
		if ($cart)
		{
			$this->cart = $cart;
		}
		else
		{
			$this->cart = Cart::getInstance();
		}

		if ($factory)
		{
			$this->factory = $factory;
		}
		else
		{
			$this->factory = DealsFactory::getInstance();
		}
	}

	/**
	 * Preflight checks before looking for some deals.
	 * 
	 * @param   array  $options  An associative array containing the deals configuration.
	 *
	 * @return  void
	 */
	public function setup(array $options = [])
	{
		// get all supported drivers
		$rules = $this->factory->getSupportedDeals($options);

		foreach ($rules as $rule)
		{
			// execute rule preflight
			$rule->prepare($this->cart);
		}
	}

	/**
	 * Applies the specified deal to the cart.
	 *
	 * @param   Deal   $deal     The deal to apply.
	 * @param   array  $options  An associative array containing the deals configuration.
	 *
	 * @return  bool   True if applied, false otherwise.
	 */
	public function serve(Deal $deal, array $options = [])
	{
		// make sure the deal is valid
		if (!$deal->type)
		{
			// missing deal type
			return false;
		}

		try
		{
			// create deal rule instance
			$rule = $this->factory->getDeal($deal->type, $options);
		}
		catch (\Exception $e)
		{
			// unable to create the provided rule
			return false;
		}

		// try to serve the deal to the customer
		return $rule->serve($this->cart, $deal);
	}

	/**
	 * Returns a collection of available deals according to the cart configuration.
	 * 
	 * @return  Deal[]  A collection of deals.
	 */
	public function getAvailableDeals()
	{
		// deals not provided, detect the default ones
		return DealsCollection::getInstance()
			// take only the published deals
			->filter(new Filters\PublishedFilter)
			// filter by publishing dates, based on the selected check-in
			->filter(new Filters\DateFilter($this->cart->getCheckinTimestamp()))
			// filter by day of the week, based on the selected check-in
			->filter(new Filters\WeekdayFilter($this->cart->getCheckinTimestamp()))
			// filter by time, based on the selected check-in
			->filter(new Filters\ShiftFilter($this->cart->getCheckinTime(true)))
			// filter by delivery or pickup, based on the selected service
			->filter(new Filters\ServiceFilter($this->cart->getService()));
	}

	/**
	 * Checks whether the provided product should use a discounted price.
	 * 
	 * @param   array  $product  An array holding the product information.
	 *                           - id          int       The product ID.
	 *                           - id_option   int|null  The variation ID.
	 *                           - price       float     The original price.
	 * @param   mixed  $deals    An iterable collection of deals. Leave empty
	 *                           to retrieve the default available deals.
	 * 
	 * @return  float  The discounted price.
	 */
	public function discountItem(array $product, $deals = null)
	{
		if (empty($product['id']))
		{
			throw new \InvalidArgumentException('The product must specify an ID.');
		}

		if ($deals === null)
		{
			// deals not provided, detect the default ones and filter them by "discountitem" rule
			$deals = $this->getAvailableDeals()->filter(new Filters\RuleFilter('discountitem'));
		}

		// create "discountitem" deal rule instance
		$discountItemRule = $this->factory->getDeal('discountitem');

		// scan all the active deals and look for the ones that support the provided product
		foreach ($deals as $deal)
		{
			// Fetch new price. In case the deal is not compatible, the price won't be altered.
			$product['price'] = $discountItemRule->getDiscountedPrice($product, $deal);
		}

		// return discounted price
		return $product['price'];
	}
}
