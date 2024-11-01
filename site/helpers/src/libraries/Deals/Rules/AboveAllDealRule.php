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
 * Class used to apply the "Above All" deal type.
 *
 * @since 1.8
 * @since 1.9  Renamed from DealRuleAboveAll.
 */
class AboveAllDealRule extends DealRule
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'aboveall';
	}

	/**
	 * @inhritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKDEALTYPE1');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRTKDEALTYPEDESC1');
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

		foreach ($deal->getProducts() as $prod)
		{
			$occurrence = 0;

			foreach ($cart->getItems() as $item)
			{
				$opt_id = $item->getOptionID();
				
				/**
				 * Auto consider all the variations that belong to the selected parent item
				 * by using this statement: $prod['id_option'] <= 0.
				 * 
				 * @since 1.8.5
				 */

				if ($item->getPrice() > 0
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
		 * @since 1.8.5
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
		
		if ($deal->max_quantity > 0 && $min_occurrence > $deal->max_quantity)
		{
			$min_occurrence = $deal->max_quantity;
		}

		$discount = new Discount([
			'id'       => $deal->get('id'),
			'amount'   => $deal->get('params')->amount,
			'percent'  => $deal->get('params')->percentot == 1,
			'type'     => $deal->get('type'),
			'quantity' => $min_occurrence,
		]);

		// remove discount if already applied
		$cart->deals()->remove($discount);

		if ($min_occurrence > 0)
		{
			// add discount
			$cart->deals()->insert($discount);

			return true;
		}
		
		return false;
	}
}
