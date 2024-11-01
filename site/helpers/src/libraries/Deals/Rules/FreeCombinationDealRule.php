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
 * Class used to apply the "Free Item with Combination" deal type.
 *
 * @since 1.8
 * @since 1.9  Renamed from DealRuleFreeCombination.
 */
class FreeCombinationDealRule extends DealRule
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'freecombination';
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKDEALTYPE3');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRTKDEALTYPEDESC3');
	}

	/**
	 * @inheritDoc
	 */
	public function serve($cart, $deal)
	{
		$required_min_occurrence = -1;
		$atleast_count = 0;

		// flag used to check whether there's at least an optional target food
		$has_at_least = false;

		/** @var E4J\VikRestaurants\TakeAway\Cart\Item[] */
		$cartItems = $cart->getItems();

		/** @var object[] */
		$dealProducts = $deal->getProducts();

		foreach ($dealProducts as $prod)
		{
			$occurrence = 0;

			foreach ($cartItems as $item)
			{
				$opt_id = $item->getOptionID();
				
				if (($item->getPrice() > 0 || $item->getDealID() > 0)
					&& $item->getItemID() == $prod->id_product
					&& ($opt_id == $prod->id_option || $opt_id <= 0 || $prod->id_option <= 0)
					&& $item->getQuantity() >= $prod->params->units)
				{
					$occurrence += floor($item->getQuantity() / $prod->params->units);
				}
			}
			
			if ($prod->params->required == 1)
			{
				if ($required_min_occurrence == -1 || $occurrence < $required_min_occurrence)
				{
					$required_min_occurrence = $occurrence;
				}
			}
			else
			{
				$atleast_count += $occurrence;

				// at least an optional food
				$has_at_least = true;
			}
		}

		/**
		 * Do not apply discount in case the deal expects at least 
		 * an optional food and the cart doesn't contain them.
		 *
		 * @since 1.9
		 */
		if ($atleast_count == 0 && $has_at_least)
		{
			return false;
		}
		
		if ($required_min_occurrence == -1)
		{
			$required_min_occurrence = $atleast_count;
		} 
		else if ($required_min_occurrence > 0 && $atleast_count == 0)
		{
			/**
			 * Condition needed to accept deals without AT_LEAST products.
			 *
			 * @since 1.7
			 */
			$atleast_count = $required_min_occurrence;
		}

		$min_occurrence = min($required_min_occurrence, $atleast_count);
		
		$MIN_QUANTITY_TO_PUSH = $deal->get('params')->min;
		$min_occurrence = intval($min_occurrence / $MIN_QUANTITY_TO_PUSH);

		/**
		 * Try to sort the items by price ASC, so that cheaper products
		 * will be fetched and discounted first.
		 *
		 * @since 1.7.4
		 */
		usort($cartItems, function($a, $b)
		{
			$aPrice = $a->getOriginalPrice();
			$bPrice = $b->getOriginalPrice();

			if ($a > $b)
			{
				return 1;
			}
			else if ($a < $b)
			{
				return -1;
			}

			return 0;
		});

		/** @var object[] */
		$dealGifts = $deal->getGifts();
		
		$giftCount = 0;

		foreach ($cartItems as $item)
		{
			$found = false;

			for ($k = 0; $k < count($dealGifts) && !$found; $k++)
			{
				$prod = $dealGifts[$k];

				$found = $prod->id_product == $item->getItemID()
					&& ($prod->id_option <= 0 || $prod->id_option == $item->getOptionID())
					&& $prod->params->units <= $item->getQuantity();
				
				if (!$found)
				{
					continue;
				}
					
				$units_to_add = $item->getQuantity();
				
				if ($units_to_add <= 0 || $units_to_add > $min_occurrence - $giftCount)
				{
					$units_to_add = max(1, $min_occurrence - $giftCount);
				}
				
				if ($item->getDealID() == $deal->get('id'))
				{
					if ($min_occurrence - ($giftCount + $units_to_add) >= 0
						&& ($deal->get('max_quantity') <= 0 || $giftCount + $units_to_add <= $deal->get('max_quantity')))
					{
						/**
						 * @todo 	Should we check the number of applies after entering within this statement?
						 * 			Because currently, if the number of units exceeds the limit, the system 
						 * 			executes the else statement, which unsets the current deal.
						 * 			
						 *			Some tests are required.
						 */
						$item->setDealQuantity($units_to_add);
					}
					else
					{
						$item->setPrice($item->getOriginalPrice());
						$item->setDealQuantity(0);
						$item->setDealID(-1);
						$item->setRemovable(true);
					}
				}
				else if ($min_occurrence - ($giftCount + $units_to_add) >= 0
					&& ($deal->get('max_quantity') <= 0 || $giftCount + $units_to_add <= $deal->get('max_quantity')))
				{
					$item->setDealQuantity($units_to_add);
					$item->setDealID($deal->get('id'));
					$item->setPrice(0.0);
				}

				$giftCount += $item->getDealQuantity();
			}
		}

		if ($deal->get('params')->autoinsert ?? false)
		{
			for ($k = 0; $k < count($dealGifts) && ($min_occurrence - $giftCount > 0); $k++)
			{
				$gift = $dealGifts[$k];
				
				$units = floor(($min_occurrence - $giftCount) / $gift->params->units);

				$newItem = new Item([
					'id_menu'     => $gift->id_takeaway_menu,
					'id'          => $gift->id_product, 
					'id_option'   => $gift->id_option, 
					'name'        => $gift->product_name, 
					'option_name' => $gift->option_name, 
					'price'       => (float) $gift->product_price + (float) $gift->option_price,
					'quantity'    => $units, 
					'ready'       => $gift->ready,
				]);
					
				if ($deal->get('max_quantity') <= 0 || $giftCount + $units <= $deal->get('max_quantity'))
				{
					$newItem->setDealID($deal->get('id'));
					$newItem->setDealQuantity($units);
					$newItem->setPrice(0.0);
					$newItem->setRemovable(false);
					
					$giftCount += $units;
				
					$cart->addItem($newItem);
				}
			}
		}

		return (bool) $giftCount;
	}
}
