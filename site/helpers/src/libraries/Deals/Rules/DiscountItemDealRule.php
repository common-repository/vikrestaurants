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

use E4J\VikRestaurants\Deals\Deal;
use E4J\VikRestaurants\Deals\DealRule;

/**
 * Class used to apply the "Discount Item" deal type.
 *
 * @since 1.8
 * @since 1.9  Renamed from DealRuleDiscountItem.
 */
class DiscountItemDealRule extends DealRule
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'discountitem';
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKDEALTYPE2');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRTKDEALTYPEDESC2');
	}

	/**
	 * @inheritDoc
	 */
	public function serve($cart, $deal)
	{
		$deal_curr_quantity = 0;

		$applied = false;

		/** @var object[] */
		$dealProducts = $deal->getProducts();

		// iterate all the cart items
		foreach ($cart->getItems() as $item)
		{
			$found = false;
			
			// reset deal in case it was already applied to this item
			if ($item->getDealID() == $deal->get('id'))
			{
				$item->setDealQuantity(0);
				$item->setPrice($item->getOriginalPrice());
				$item->setDealID(-1);
			}
			
			// scan all the products eligible for this deal
			for ($k = 0; $k < count($dealProducts) && !$found; $k++)
			{	
				$prod = $dealProducts[$k];

				// make sure the current item is compatible with the deal seek
				$found = (
					$prod->id_product == $item->getItemID() &&
					($prod->id_option <= 0 || $prod->id_option == $item->getOptionID()) && 
					$prod->params->units <= $item->getQuantity()
				);
				
				if ($found)
				{
					// calculate number of deals to apply to the product
					$deal_quantity = intval($item->getQuantity() / $prod->params->units);

					/**
					 * Make sure the number of applies doesn't exceed the maximum threshold.
					 *
					 * @since 1.8
					 */
					if ($deal->get('max_quantity') > 0 && $deal_curr_quantity + $deal_quantity > $deal->get('max_quantity'))
					{
						// recalculate quantity to avoid exceeding the threshold
						$deal_quantity = $deal->get('max_quantity') - $deal_curr_quantity;
					}

					// apply discount to item
					$item->setDealQuantity($deal_quantity);

					if ($prod->params->percentot == 1)
					{
						// offer a percentage discount calculated on the price of the single item
						$item->setPrice($item->getPrice() - $item->getPrice() * $prod->params->amount / 100.0);
					}
					else
					{
						// offer a fixed discount
						$item->setPrice($item->getPrice() - $prod->params->amount);
					}

					// apply deal to the item
					$item->setDealID($deal->get('id'));
					
					// increase number of applies by the redeem quantity
					$deal_curr_quantity += $deal_quantity;

					$applied = true;
				}
				
			}

			if ($deal->get('max_quantity') > 0 && $deal_curr_quantity >= $deal->get('max_quantity'))
			{
				// no more redeemable deals
				break;
			}
		}

		return $applied;
	}

	/**
	 * Calculates the discounted price for the given deal.
	 * 
	 * @param   array  $product  An array holding the product information.
	 *                           - id          int       The product ID.
	 *                           - id_option   int|null  The variation ID.
	 *                           - price       float     The original price.
	 * @param   Deal   $deal     The deal instance.
	 * 
	 * @return  float  The discounted price.
	 */
	public function getDiscountedPrice(array $product, Deal $deal)
	{
		// define default variables
		$id        = (int)   ($product['id']        ?? 0);
		$optionId  = (int)   ($product['id_option'] ?? 0);
		$price     = (float) ($product['price']     ?? 0);

		// fetch product details
		$dealProd = $deal->getProduct($id, $optionId);

		if (!$dealProd)
		{
			// product not found, do not alter the price
			return $price;
		}

		// make sure the discount is not applied for 2 or more units
		if (($dealProd->params->units ?? 1) > 1)
		{
			// more than a unit required, do not alter the price
			return $price;
		}

		if (($dealProd->params->percentot ?? 1) == 1)
		{
			// percentage discount
			$price -= $price * ($dealProd->params->amount ?? 0) / 100;
		}
		else
		{
			// fixed discount
			$price -= ($dealProd->params->amount ?? 0);
		}

		return max(0, $price);
	}
}
