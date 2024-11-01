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
 * VikRestaurants operatores area bill view model.
 *
 * @since 1.9
 */
class VikRestaurantsModelOpbill extends JModelVRE
{
	/**
	 * Filter the products that satisfies the provided search string.
	 * 
	 * @param   string  $search
	 * 
	 * @return  object[]
	 */
	public function searchProducts(string $search)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->select($db->qn('name'))
			->from($db->qn('#__vikrestaurants_section_product'))
			->where($db->qn('name') . ' LIKE ' . $db->q("%{$search}%"))
			->order($db->qn('ordering') . ' ASC');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Returns all the sections of the provided menu.
	 * 
	 * @param   int  $idMenu
	 * 
	 * @return  object[]
	 */
	public function getMenuSections(int $idMenu)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__vikrestaurants_menus_section'))
			->where($db->qn('id_menu') . ' = ' . $idMenu)
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Returns all the products of the provided section.
	 * Do not provide the section to return all the hidden products.
	 * 
	 * @param   int  $idSection
	 * 
	 * @return  object[]
	 */
	public function getSectionProducts(int $idSection = 0)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('p.*')
			->from($db->qn('#__vikrestaurants_section_product', 'p'))
			->order($db->qn('p.ordering') . ' ASC');

		if ($idSection)
		{
			$query->leftjoin($db->qn('#__vikrestaurants_section_product_assoc', 'a') . ' ON ' . $db->qn('a.id_product') . ' = ' . $db->qn('p.id'));
			$query->where($db->qn('a.id_section') . ' = ' . $idSection);
		}
		else
		{
			$query->where($db->qn('p.hidden') . ' = 1');
		}

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Fetches the product details.
	 * 
	 * @param   int  $idProduct  The ID of the default product.
	 * @param   int  $idCart     The ID of the bill/cart item to update.
	 * 
	 * @return  object
	 */
	public function getCartItem(int $idProduct, int $idCart = 0)
	{
		// fetch default product details and assigned variations
		$item = JModelVRE::getInstance('menusproduct')->getItem($idProduct);

		if (!$item)
		{
			throw new RuntimeException('Product [' . $idProduct . '] not found', 404);
		}

		// inject default properties
		$item->quantity = 1;
		$item->notes    = '';

		if ($idCart)
		{
			// fetch details of the records stored in the reservation bill
			$cartItem = JModelVRE::getInstance('resprod')->getItem($idCart);

			// inject cart details within the details of the base product
			foreach ((array) $cartItem as $k => $v)
			{
				$item->{$k} = $v;
			}
		}

		return $item;
	}

	/**
	 * Adds the provided item to the bill of the assigned reservation.
	 * 
	 * @param   array  $item       The details of the item to add.
	 * @param   int    $idSection  An optional section to which the product belongs.
	 * 
	 * @return  object|false       A response object on success, false otherwise.
	 */
	public function addItem(array $item, int $idSection = 0)
	{
		// check if we should create the product at runtime
		if (empty($item['id']) && empty($item['id_product']))
		{
			$prodModel = JModelVRE::getInstance('menusproduct');

			// try to save the product at runtime
			$item['id_product'] = $prodModel->save([
				'name'      => $item['name']   ?? '',
				'price'     => $item['price']  ?? 0,
				'id_tax'    => $item['id_tax'] ?? 0,
				'published' => 0,
				'hidden'    => 1,
			]);

			if (!$item['id_product'])
			{
				// something went wrong, propagate error
				$this->setError($prodModel->getError());
				return false;
			}
		}

		// if we are adding a new item and the price is missing, recover it from the details of the product
		if (!isset($item['price']) && empty($item['id']))
		{		
			$product = JModelVRE::getInstance('menusproduct')->getItem($item['id_product'] ?? 0);

			if (!$product)
			{
				$this->setError(new Exception('Product not found', 404));
				return false;
			}

			// use default price
			$item['price'] = $product->price;

			// iterate all the supported options
			foreach ($product->options as $option)
			{
				if ($option->id == ($item['id_product_option']) ?? 0)
				{
					// increase price by the cost of selected variation
					$item['price'] += $option->inc_price;
				}
			}
		}

		if ($idSection > 0)
		{
			// search for the association between the product and the parent section,
			// needed to retrieve any applied charge/discount
			$sectionProduct = JModelVRE::getInstance('sectionproduct')->getItem([
				'id_product' => $item['id_product'] ?? 0,
				'id_section' => $idSection,
			]);

			if ($sectionProduct)
			{
				// item found, set the charge
				$item['charge'] = $sectionProduct->charge;
			}
		}

		$model = JModelVRE::getInstance('reservation');

		// fetch details of the current reservation
		$current = $model->getItem($item['id_reservation'] ?? 0);

		if (!$current)
		{
			$this->setError(new Exception('Reservation not found', 404));
			return false;
		}

		if (!empty($item['id']))
		{
			$currentItem = JModelVRE::getInstance('resprod')->getItem($item['id']);

			if (!isset($item['price']))
			{
				// in case of update, use the previously stored price
				$item['price'] = $currentItem->price ?? 0;
			}

			if (!isset($item['discount']))
			{
				// in case of update, we should recover any previously set discount amount
				$item['discount'] = $currentItem->discount ?? 0;
			}

			// in case of update, we should subtract the item totals from the bill
			$current->total_net  -= $currentItem->net   ?? 0;
			$current->total_tax  -= $currentItem->tax   ?? 0;
			$current->bill_value -= $currentItem->gross ?? 0;
		}

		// calculate base price before the taxes
		$basePrice = ($item['price'] + ($item['charge'] ?? 0)) * ($item['quantity'] ?? 1) - ($item['discount'] ?? 0);

		// calculate taxes for the product to add/update
		$totals = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($item['id_product'] ?? 0, $basePrice, [
			'subject' => 'restaurant.menusproduct',
			'lang'    => $current->langtag,
			'id_user' => $current->id_user,
		]);

		// calculate totals for the item
		$item['net']           = $totals->net;
		$item['tax']           = $totals->tax;
		$item['gross']         = $totals->gross;
		$item['tax_breakdown'] = $totals->breakdown;

		// prepare reservation data to save
		$reservation = [
			'id'    => $item['id_reservation'],
			'items' => [
				$item,
			],
			// increase reservation totals
			'total_net'  => $current->total_net  + $item['net'],
			'total_tax'  => $current->total_tax  + $item['tax'],
			'bill_value' => $current->bill_value + $item['gross'],
		];

		// try to save the reservation
		$id = $model->save($reservation);

		if (!$id)
		{
			// propagate error from parent
			$this->setError($model->getError());
			return false;
		}
			
		// fetch bill items
		$bill = $model->getBillItems($id);

		if (!empty($item['id']))
		{
			// item updated, filter list to obtain only the target item
			$bill = array_filter($bill, function($needle) use ($item)
			{
				return $needle->id == $item['id'];
			});
		}

		// In case the item was added, it will be available within the bill as last item.
		// It words for update too because the list has been filtered to include only
		// the updated item.
		$targetItem = end($bill);

		$response = new stdClass;
		$response->total_net   = $reservation['total_net'];
		$response->total_tax   = $reservation['total_tax'];
		$response->total_gross = $reservation['bill_value'];
		$response->item        = $targetItem;

		return $response;
	}

	/**
	 * Removes the provided item from the given reservation.
	 * 
	 * @param   int  $idReservation  The reservation to which the item belongs.
	 * @param   int  $idCartProduct  The cart item to delete.
	 * 
	 * @return  object|false  The cart details on success, false otherwise.
	 */
	public function removeItem(int $idReservation, int $idCartProduct)
	{
		$reservationModel = JModelVRE::getInstance('reservation');

		// fetch details of the current reservation
		$currentReservation = $reservationModel->getItem($idReservation);

		if (!$currentReservation)
		{
			$this->setError(new Exception('Reservation not found', 404));
			return false;
		}

		// fetch details of the cart item to delete
		$currentItem = JModelVRE::getInstance('resprod')->getItem($idCartProduct);

		if (!$currentItem)
		{
			$this->setError(new Exception('Product not found', 404));
			return false;
		}

		// create record to update
		$reservation = [
			'id'         => $idReservation,
			'total_net'  => $currentReservation->total_net  - $currentItem->net,
			'total_tax'  => $currentReservation->total_tax  - $currentItem->tax,
			'bill_value' => $currentReservation->bill_value - $currentItem->gross,
			'deleted_items' => [
				$currentItem->id,
			],
		];

		// try to save the reservation
		$id = $reservationModel->save($reservation);

		if (!$id)
		{
			// propagate error from parent
			$this->setError($reservationModel->getError());
			return false;
		}

		$response = new stdClass;
		$response->total_net   = $reservation['total_net'];
		$response->total_tax   = $reservation['total_tax'];
		$response->total_gross = $reservation['bill_value'];

		return $response;
	}
}
