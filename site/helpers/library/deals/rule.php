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
 * Deal rule encapsulation.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\Deals\DealRule instead.
 */
abstract class DealRule extends E4J\VikRestaurants\Deals\DealRule
{
	/**
	 * @inheritDoc
	 * 
	 * Proxy needed for compliance with the deprecated framework, which
	 * passes the $cart argument by reference.
	 */
	public function prepare($cart)
	{
		$this->preflight($cart);
	}

	/**
	 * Executes the rule before start checking whether this rule can be applied or not.
	 *
	 * @param   TakeAwayCart  $cart  The cart with the items.
	 *
	 * @return  void
	 */
	public function preflight(&$cart)
	{
		// do nothing here
	}

	/**
	 * @inheritDoc
	 * 
	 * Proxy needed for compliance with the deprecated framework, which
	 * passes the $cart argument by reference.
	 */
	public function serve($cart, $deal)
	{
		return $this->apply($cart, $deal);
	}

	/**
	 * Applies the deal to the cart instance, if needed.
	 *
	 * @param   TakeAwayCart  $cart  The cart with the items.
	 * @param   array         $deal  The deal to apply.
	 *
	 * @return  bool          True if applied, false otherwise.
	 */
	abstract public function apply(&$cart, $deal);
}
