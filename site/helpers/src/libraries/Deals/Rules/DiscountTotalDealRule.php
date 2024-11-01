<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Deals\DealRule;
use E4J\VikRestaurants\TakeAway\Cart\Deals\Discount;

/**
 * Class used to apply the "Discount with Total Cost" deal type.
 *
 * @since 1.8
 * @since 1.9  Renamed from DealRuleDiscountTotal.
 */
class DiscountTotalDealRule extends DealRule
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'discounttotal';
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKDEALTYPE6');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRTKDEALTYPEDESC6');
	}

	/**
	 * @inheritDoc
	 */
	public function prepare($cart)
	{
		// check if this deal is already applied
		while (($deal = $cart->deals()->indexOfType($this->getID())) !== false)
		{
			// remove deal from cart
			$cart->deals()->removeAt($deal);

			// safety break for backward compatibility
			if ($deal < 0)
			{
				return;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function serve($cart, $deal)
	{
		// calculate the total cost of the cart (without applied discounts)
		$cartTotalCost = $cart->getTotalCost();

		// make sure the cart total is equals or higher than the specified threshold
		if ($cartTotalCost < $deal->params->totalcost)
		{
			return false;
		}

		// create new discount
		$discount = new Discount([
			'id'      => $deal->get('id'),
			'amount'  => $deal->params->amount,
			'percent' => $deal->params->percentot == 1,
			'type'    => $deal->get('type'),
		]);

		// check whether the total discount has been already applied
		if (($index = $cart->deals()->indexOfType($discount)) !== false)
		{
			if ($discount->isPercent())
			{
				// calculate percentage amount of the new discount
				$newOffer = $cartTotalCost * $discount->getAmount() / 100;
			}
			else
			{
				// use fixed amount of the new discount
				$newOffer = $discount->getAmount();
			}

			// obtain the discount already applied
			$existingDiscount = $cart->deals()->get($index);

			if ($existingDiscount->isPercent())
			{
				// calculate percentage amount of the existing discount
				$existingOffer = $cartTotalCost * $existingDiscount->getAmount() / 100;
			}
			else
			{
				// use fixed amount of the existing discount
				$existingOffer = $existingDiscount->getAmount();
			}

			// replace the existing offer with the new one in case the discounted amount is higher
			if ($newOffer > $existingOffer)
			{
				$cart->deals()->set($discount, $index);
			}
		}
		else
		{
			// no active discount of this type, register a new one
			$cart->deals()->insert($discount);
		}

		return true;
	}
}
