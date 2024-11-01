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
 * Before the 1.9 version of the program, the take-away orders and the related products did not
 * support a column to store the total net. We can easily calculate it by subtracting the total taxes
 * from the total gross and update each record accordingly.
 *
 * @since 1.9
 */
class OrderPricesFixer extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		try
		{
			$this->updateItems();
			$this->setOrdersNet();
		}
		catch (\Exception $e)
		{
			// an error has occurred
			\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Calculates the net price of the take-away orders products.
	 * 
	 * @return  void
	 */
	protected function updateItems()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_res_prod_assoc'))
			// the total gross is equals to the price, which is the the unit price per the selected units
			->set($db->qn('gross') . ' = ' . $db->qn('price'))
			// calculate the total net by subtracting the taxes from the total gross
			->set($db->qn('net') . ' = ' . $db->qn('price') . ' - ' . $db->qn('tax'))
			// divide price by quantity to preserve the price per unit
			->set($db->qn('price') . ' = ' . $db->qn('price') . ' / ' . $db->qn('quantity'));

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Calculates the net price of the take-away orders.
	 * 
	 * @return  void
	 */
	protected function setOrdersNet()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_takeaway_reservation'))
			->set($db->qn('total_net') . ' = ' . $db->qn('total_to_pay') . ' - ' . $db->qn('total_tax') . ' - ' . $db->qn('payment_charge') . ' - ' . $db->qn('delivery_charge') . ' - ' . $db->qn('tip_amount'));

		$db->setQuery($query);
		$db->execute();
	}
}
