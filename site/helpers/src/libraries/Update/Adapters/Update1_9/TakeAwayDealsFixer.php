<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * The database structure of the take-away deals have been changed. Some columns have been migrated within
 * the table used to store the take-away products and gifts. Therefore we need to adjust those tables in
 * order to keep working on the latest versions too.
 * 
 * In addition, the ID of the rules have been changed from INT to STRING to represent them in a better way.
 *
 * @since 1.9
 */
class TakeAwayDealsFixer extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$db = \JFactory::getDbo();

		// fetch all the existing deals
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__vikrestaurants_takeaway_deal'))
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $deal)
		{
			// adjust the take-away deal 
			$this->mapDealRule($deal);

			// commit changes
			$db->updateObject('#__vikrestaurants_takeaway_deal', $deal, 'id');

			$this->adjustProducts($deal);
			$this->adjustGifts($deal);
		}

		return true;
	}

	/**
	 * Adjusts the deal type from INT to STRING.
	 * 
	 * @param   object  $deal  The deal to update.
	 * 
	 * @return  void
	 */
	private function mapDealRule(object $deal)
	{
		// define lookup to convert the type of the take-away deal
		$lookup = [
			1 => 'aboveall',
			2 => 'discountitem',
			3 => 'freecombination',
			4 => 'freetotal',
			5 => 'coupon',
			6 => 'discounttotal',
		];

		if (isset($lookup[$deal->__type]))
		{
			$deal->type = $lookup[$deal->__type];
		}

		$deal->params = new \stdClass;

		// set up deal parameters
		if ($deal->type === 'aboveall')
		{
			$deal->params->amount    = $deal->__amount;
			$deal->params->percentot = $deal->__percentot;
			$deal->params->min       = $deal->__min_quantity;
		}
		else if ($deal->type === 'freecombination')
		{
			$deal->params->min        = $deal->__min_quantity;
			$deal->params->autoinsert = $deal->__auto_insert;
		}
		else if ($deal->type === 'freetotal')
		{
			$deal->params->amount     = $deal->__amount;
			$deal->params->autoinsert = $deal->__auto_insert;
		}
		else if ($deal->type === 'discounttotal')
		{
			$deal->params->amount    = $deal->__amount;
			$deal->params->percentot = $deal->__percentot;
			$deal->params->totalcost = $deal->__cart_tcost;
		}

		$deal->params = json_encode($deal->params);
	}

	/**
	 * Adjusts the parameters of the target products assigned to the given deal.
	 * 
	 * @param   object  $deal  The deal to update.
	 * 
	 * @return  void
	 */
	private function adjustProducts(object $deal)
	{
		$db = \JFactory::getDbo();

		// fetch all the products assigned to this deal
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__vikrestaurants_takeaway_deal_product_assoc'))
			->where($db->qn('id_deal') . ' = ' . (int) $deal->id);

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $product)
		{
			$product->params = new \stdClass;

			if ($deal->type === 'discountitem')
			{
				// take discount from deal parameters
				$product->params->amount    = $deal->__amount;
				$product->params->percentot = $deal->__percentot;
				// take from product parameters
				$product->params->units     = $product->__quantity;
				$product->params->published = 1;
			}
			else
			{
				// take from product parameters
				$product->params->required  = $product->__required;
				$product->params->units     = $product->__quantity;
				$product->params->published = 1;
			}

			$product->params = json_encode($product->params);

			// commit changes
			$db->updateObject('#__vikrestaurants_takeaway_deal_product_assoc', $product, 'id');
		}
	}

	/**
	 * Adjusts the parameters of the gift products assigned to the given deal.
	 * 
	 * @param   object  $deal  The deal to update.
	 * 
	 * @return  void
	 */
	private function adjustGifts(object $deal)
	{
		$db = \JFactory::getDbo();

		// fetch all the products assigned to this deal
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__vikrestaurants_takeaway_deal_free_assoc'))
			->where($db->qn('id_deal') . ' = ' . (int) $deal->id);

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $product)
		{
			$product->params = new \stdClass;

			// take from product parameters
			$product->params->units     = $product->__quantity;
			$product->params->published = 1;

			$product->params = json_encode($product->params);

			// commit changes
			$db->updateObject('#__vikrestaurants_takeaway_deal_free_assoc', $product, 'id');
		}
	}
}
