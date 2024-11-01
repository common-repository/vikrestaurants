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
 * Before the 1.9 version of the program, the restaurant reservations and the related products did not
 * support a column to store the total net or the total taxes. Therefore we should make sure that the 
 * created products will be updated with the correct taxes value.
 *
 * @since 1.9
 */
class ReservationPricesFixer extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		try
		{
			$this->updateItems();
			$this->updateReservations();
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
	 * Calculates the net price of the restaurant reservations products.
	 * 
	 * @return  void
	 */
	protected function updateItems()
	{
		$dbo = \JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('COUNT(1)')
			->from($dbo->qn('#__vikrestaurants_res_prod_assoc'));

		$dbo->setQuery($q);
		$total = (int) $dbo->loadResult();

		// take at most 1000 records per time in order to avoid exceeding the memory limit
		for ($offset = 0, $limit = 1000; $offset < $total; $offset += $limit)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn(['id', 'id_product', 'price', 'quantity']))
				->from($dbo->qn('#__vikrestaurants_res_prod_assoc'));

			$dbo->setQuery($q, $offset, $limit);
			foreach ($dbo->loadObjectList() as $item)
			{
				// calculate taxes for this product
				$result = \E4J\VikRestaurants\Taxing\TaxesFactory::calculate($item->id_product, $item->price, [
					'subject' => 'restaurant.menusproduct',
				]);

				// register taxing information
				$item->gross         = $result->gross;
				$item->net           = $result->net;
				$item->tax           = $result->tax;
				$item->tax_breakdown = json_encode($result->breakdown);

				// divide total price by quantity to preserve the price per unit
				$item->price /= $item->quantity;

				// commit changes
				$dbo->updateObject('#__vikrestaurants_res_prod_assoc', $item, 'id');
			}
		}
	}

	/**
	 * Calculates the net price of the restaurant reservations.
	 * 
	 * @return  void
	 */
	protected function updateReservations()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			// the total gross is equals to the price, which is the 
			->set($db->qn('total_net') . ' = ' . $db->qn('bill_value') . ' - ' . $db->qn('total_tax'). ' - ' . $db->qn('tip_amount'));

		$db->setQuery($query);
		$db->execute();
	}
}
