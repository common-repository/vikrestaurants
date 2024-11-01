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
use E4J\VikRestaurants\TakeAway\Cart\Item;

/**
 * Class used to apply the "Free Item with Total" deal type.
 *
 * @since 1.8
 * @since 1.9  Renamed from DealRuleFreeTotal.
 */
class FreetotalDealRule extends DealRule
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'freetotal';
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKDEALTYPE4');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRTKDEALTYPEDESC4');
	}

	/**
	 * @inheritDoc
	 */
	public function serve($cart, $deal)
	{
		$dealCount = 0;

		/** @var object[] */
		$dealGifts = $deal->getGifts();

		$dealMaxQuantity = (int) $deal->get('max_quantity');

		if ($dealMaxQuantity <= 0)
		{
			// unlimited usages, use an unreachable amount
			$dealMaxQuantity = PHP_INT_MAX;
		}

		/**
		 * The mean of the "units" parameter of the gift products has changed. It is now used to track the maximum
		 * number of times that the same product can be gifted. This lookup will track the ID of the product/option
		 * against the number of usages.
		 * 
		 * @since 1.9
		 */
		$usagesLookup = [];

		// scan all the items in cart
		foreach ($cart->getItems() as $item)
		{
			// recalculate the total cost at every loop because the application of a new discount
			// might have reduced the total and it could be no more compliant with the deal
			// requirements
			$cartTotalCost = $cart->getTotalCost();

			$found = false;
			
			// reset deal from product, if registered
			if ($item->getDealID() == $deal->get('id'))
			{
				$item->setDealQuantity(0);
				$item->setPrice($item->getOriginalPrice());
				$item->setDealID(0);
			}
	
			for ($k = 0; $k < count($dealGifts) && !$found; $k++)
			{
				$prod = $dealGifts[$k];

				// make sure the current item is compatible with the deal gift
				$found = (
					$prod->id_product == $item->getItemID()
					&& ($prod->id_option <= 0 || $prod->id_option == $item->getOptionID())
					// && $prod->params->units <= $item->getQuantity()
				);

				if (!$found)
				{
					// gift not eligible, go to the next one
					continue;
				}

				// make sure the deal be actually gifted
				if ($dealMaxQuantity <= $dealCount || $cartTotalCost - $item->getPrice() < $deal->get('params')->amount)
				{
					// we already reached the maximum number of usages or the total cost is lower than
					// the threshold needed to trigger the deal
					continue;
				}

				if (!isset($usagesLookup[$k]))
				{
					$usagesLookup[$k] = 0;
				}

				$discountableUnits = $item->getQuantity();

				if ($cartTotalCost - $item->getOriginalPrice() * $discountableUnits < $deal->get('params')->amount)
				{
					// Not all the products can be gifted because the resulting total cost would
					// be lower than the threshold needed to trigger this offer.
					// We should calculate here the maximum amount of units that can be discounted.
					$discountableUnits = floor(($cartTotalCost - $deal->get('params')->amount) / $item->getOriginalPrice());
				}
				
				// calculate the remaining usages
				$freeSpace = min($dealMaxQuantity - $dealCount, $prod->params->units - $usagesLookup[$k]);

				if ($freeSpace > 0)
				{
					// apply discount to item
					$item->setDealID($deal->get('id'));
					$item->setDealQuantity(0);
					$item->setPrice(0.0);

					if ($discountableUnits <= $freeSpace)
					{
						// all the items in cart can be offered
						$dealCount += $discountableUnits;
						$item->setDealQuantity($discountableUnits);
					}
					else
					{
						// not all the items can be offered
						$dealCount += min($discountableUnits, $freeSpace);
						$item->setDealQuantity(min($discountableUnits, $freeSpace));
					}

					// increase the number of times this product has been gifted
					$usagesLookup[$k] += $freeSpace;
				}

				// end gift loop

			}

			// end cart item loop

		}
		
		// check whether we should auto-insert the free item
		if ($cart->getTotalCost() >= $deal->get('params')->amount && ($deal->get('params')->autoinsert ?? false) && $dealMaxQuantity > $dealCount)
		{
			foreach ($dealGifts as $gift)
			{
				$cartTotalCost = $cart->getTotalCost();

				// create the new item to insert
				$newItem = new Item([
					'id_menu'      => $gift->id_takeaway_menu,
					'id'           => $gift->id_product, 
					'id_option'    => $gift->id_option, 
					'name'         => $gift->product_name, 
					'option_name'  => $gift->option_name, 
					'price'        => (float) $gift->product_price + (float) $gift->option_price,
					'quantity'     => $gift->params->units,
					'ready'        => $gift->ready, 
				]);

				// check if we already have the same item within the cart
				$index = $unitsInCart = $cart->indexOf($newItem);

				// in case the item already exists, we need to subtract the items already in cart to avoid entering in a 
				// loop that keeps re-adding the gift when deleting other items.s
				if ($index !== false && $cartTotalCost - $newItem->getOriginalPrice() * $cart->getItemAt($index)->getQuantity() < $deal->get('params')->amount)
				{
					continue;
				}
					
				if ($dealMaxQuantity >= $dealCount + $newItem->getQuantity())
				{
					$newItem->setDealID($deal->get('id'));
					$newItem->setDealQuantity($newItem->getQuantity());
					$newItem->setPrice(0.0);
					$newItem->setRemovable(false);

					$dealCount += $newItem->getQuantity();
				
					$cart->addItem($newItem);
				}
			}
		}

		return (bool) $dealCount;
	}
}
