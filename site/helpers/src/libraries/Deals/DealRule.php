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

/**
 * Deal rule encapsulation.
 *
 * @since 1.8
 * @since 1.9 Renamed from DealRule (without namespace).
 */
abstract class DealRule
{
	/**
	 * A configuration array.
	 *
	 * @var \JRegistry
	 */
	private $options;

	/**
	 * Class constructor.
	 *
	 * @param  mixed  $options  A configuration registry.
	 */
	public function __construct($options = [])
	{
		// set-up configuration
		$this->options = new \JRegistry($options);
	}

	/**
	 * Returns the deal code identifier.
	 *
	 * @return  string
	 * 
	 * @since   1.9  The return type has been changed from int to string.
	 */
	abstract public function getID();

	/**
	 * Returns a deal readable name.
	 *
	 * @return  string
	 */
	abstract public function getName();

	/**
	 * Returns the description of the deal.
	 *
	 * @return  string
	 */
	abstract public function getDescription();

	/**
	 * Executes the rule before start checking whether this rule can be applied or not.
	 * @todo starting from the 1.10 version, type hint $cart.
	 *
	 * @param   Cart  $cart  The cart with the items.
	 *
	 * @return  void
	 */
	public function prepare($cart)
	{
		// do nothing here
	}

	/**
	 * Applies the deal to the cart instance, if needed.
	 * @todo starting from the 1.10 version, type hint $cart and $deal.
	 *
	 * @param   Cart  $cart  The cart with the items.
	 * @param   Deal  $deal  The deal to apply.
	 *
	 * @return  bool  True if applied, false otherwise.
	 */
	abstract public function serve($cart, $deal);
}
