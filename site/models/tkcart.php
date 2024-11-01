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
 * VikRestaurants take-away cart model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkcart extends JModelVRE
{
	/**
	 * Helper method used to obtain an instance of the cart.
	 *
	 * @return  E4J\VikRestaurants\TakeAway\Cart
	 */
	public function getCart()
	{
		static $cart = null;

		if (!$cart)
		{
			$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();
		}

		return $cart;
	}

	/**
	 * Adds the provided take-away item data into the cart.
	 * 
	 * @param   array|object  $data  The item data.
	 * 
	 * @return  object|bool  An object holding the cart data on success, otherwise false.
	 */
	public function addItem($data)
	{
		$data = (array) $data;

		$dbo    = JFactory::getDbo();
		$config = VREFactory::getConfig();
		
		// validate quantity
		if (!isset($data['quantity']) || (int) $data['quantity'] <= 0)
		{
			$data['quantity'] = 1;
		}
		
		// get product details
		$entry = $this->getProduct($data['id_entry'] ?? 0, $data['id_option'] ?? 0);
		
		if (!$entry)
		{
			// the product does not exist
			$this->setError(new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404));
			return false;
		}

		$index = $data['index'] ?? 0;

		// fetch provided toppings
		$toppings = $data['toppings'] ?? [];
		$units    = $data['units']    ?? [];
		
		// get cart instance
		$cart = $this->getCart();
		
		if ($index < 0)
		{
			// create take-away cart item
			$item = new E4J\VikRestaurants\TakeAway\Cart\Item([
				'id_menu'     => $entry->id_takeaway_menu,
				'id'          => $entry->id, 
				'id_option'   => $entry->option->id ?? 0, 
				'name'        => $entry->name, 
				'option_name' => $entry->option->name ?? '',
				'price'       => $entry->totalPrice,
				'quantity'    => $data['quantity'], 
				'ready'       => $entry->ready,
				'notes'       => $data['notes'],
			]);            
		}
		else
		{
			// update existing record
			$item = $cart->getItemAt($index);

			if ($item === null)
			{
				// the record does not exist
				$this->setError(new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404));
				return false;
			}
			
			$item->setQuantity($data['quantity']);
			$item->setAdditionalNotes($data['notes'] ?? '');
		}
		
		// refresh toppings
		$item->clearGroups();
		
		// validate toppings against groups
		foreach ($entry->toppings as $group)
		{	
			// create take-away cart item group
			$itemGroup = new E4J\VikRestaurants\TakeAway\Cart\Item\ToppingsGroup([
				'id'       => $group->id,
				'title'    => $group->title,
				'multiple' => $group->multiple,
				'quantity' => $group->use_quantity,
			]);
			
			if (!isset($toppings[$group->id]))
			{
				$toppings[$group->id] = [];
			}
			
			$to_remove = [];

			$toppingsCount = 0;

			// validate selected toppings
			for ($i = 0; $i < count($toppings[$group->id]); $i++)
			{
				$found = false;

				for ($j = 0; $j < count($group->list) && !$found; $j++)
				{
					$id_topping = $toppings[$group->id][$i];

					if ($id_topping == $group->list[$j]->assoc_id)
					{
						$found = true;

						$toppingUnits = 1;

						/**
						 * Count the selected toppings by considering the total number
						 * of picked units, always if the group supports them.
						 *
						 * @since 1.8.2
						 */
						if ($itemGroup->useQuantity() && !empty($units[$group->id][$id_topping]))
						{
							// use the specified units
							$toppingUnits = $units[$group->id][$id_topping];
						}

						// increase toppings counter
						$toppingsCount += $toppingUnits;
					}
				}

				if (!$found)
				{
					$to_remove[] = $i;
				}
			}
			
			// remove wrong toppings
			foreach ($to_remove as $i)
			{
				unset($toppings[$group->id][$i]);
			}

			// remove repeated toppings
			$toppings[$group->id] = array_values(array_unique($toppings[$group->id]));
			
			// check selected quantity toppings
			if ($group->min_toppings > $toppingsCount || $toppingsCount > $group->max_toppings)
			{
				// invalid quantity
				$this->setError(new Exception(JText::translate('VRTKADDITEMERR1'), 400));
				return false;
			}
			
			// get toppings objects
			for ($i = 0; $i < count($toppings[$group->id]); $i++)
			{
				$found = false;

				for ($j = 0; $j < count($group->list) && !$found; $j++)
				{
					if ($toppings[$group->id][$i] == $group->list[$j]->assoc_id)
					{		
						// create take-away cart item group
						$itemGroupTopping = new E4J\VikRestaurants\TakeAway\Cart\Item\Topping([
							'id'       => $group->list[$j]->id,
							'id_assoc' => $group->list[$j]->assoc_id,
							'name'     => $group->list[$j]->name,
							'rate'     => $group->list[$j]->rate
						]);

						$id_topping = $itemGroupTopping->getAssocID();

						/**
						 * Check if the customer was allowed to specify the units.
						 *
						 * @since 1.8.2
						 */
						if ($itemGroup->useQuantity() && !empty($units[$group->id][$id_topping]))
						{
							// use the specified units
							$itemGroupTopping->setUnits($units[$group->id][$id_topping]);
						}

						$itemGroup->addTopping($itemGroupTopping);
						
						$found = true;
					}
				}
			}

			$item->addToppingsGroup($itemGroup);
		}
		
		if ($index < 0)
		{
			// search for a similar item already added into the cart
			$index = $cart->indexOf($item);

			if ($index !== false)
			{
				// a similar item already exists, update it
				$item = $cart->getItemAt($index);
				
				$item->setQuantity($item->getQuantity() + $data['quantity']);
				$item->setAdditionalNotes($data['notes'] ?? '');
			}
			else
			{
				// add the new item
				$index = $cart->addItem($item);

				if ($index === false)
				{
					// unable to add the item
					$this->setError(new Exception(JText::sprintf('VRTKMAXSIZECARTERR', $cart->getMaxSize()), 400));
					return false;
				}
			}
		}

		// check max quantity for update or merge functions
		if ($cart->getPreparationItemsQuantity() > $cart->getMaxSize())
		{
			$this->setError(new Exception(JText::sprintf('VRTKMAXSIZECARTERR', $cart->getMaxSize()), 400));
			return false;
		}

		$msg = null;

		// get item again after insert/update
		$stock = $cart->getItemAt($index);

		// check item remaining quantity in stock
		$in_stock = VikRestaurants::getTakeawayItemRemainingInStock($stock->getItemID(), $stock->getOptionID());

		// make sure the stock system is enabled before to proceed
		if ($in_stock != -1)
		{
			// get total number of the same items within the cart
			$item_quantity = $cart->getQuantityItems($stock->getItemID(), $stock->getOptionID());
			
			// make sure the total number of purchased items doesn't exceed the remaining stock
			if ($in_stock - $item_quantity < 0)
			{
				// remove exceeding items
				$removed_items = $item_quantity - $in_stock;
				$stock->remove($removed_items);

				$msg = new stdClass;

				if ($data['quantity'] == $removed_items)
				{
					// no more items in stock
					$this->setError(new Exception(JText::sprintf('VRTKSTOCKNOITEMS', $item->getName()), 404));
					return false;
				}
				else
				{
					// only a few items were added
					$msg->text = JText::sprintf('VRTKSTOCKREMOVEDITEMS', $item->getName(), $removed_items);
					$msg->status = 2;
				}
			}
		}

		try
		{
			/**
			 * Fires an event whenever an item is going to be added/updated into the cart.
			 * 
			 * @param   E4J\VikRestaurants\TakeAway\Cart\Item  $item
			 * @param   E4J\VikRestaurants\TakeAway\Cart\Cart  $cart
			 * 
			 * @return  void
			 * 
			 * @throws  Exception  To abort the saving process.
			 * 
			 * @since   1.9.1
			 */
			VREFactory::getPlatform()->getDispatcher()->trigger('onAddTakeAwayCartItem', [$item, $cart]);
		}
		catch (Exception $error)
		{
			// a plugin aborted the saving process
			$this->setError($error);
			return false;
		}
		
		/**
		 * Reset cart to handle correctly deal_quantities.
		 *
		 * @since 1.7
		 */
		VikRestaurants::resetDealsInCart($cart);
		VikRestaurants::checkForDeals($cart);
		
		$cart->store();

		$totals = $cart->getTotals();
		
		$response = new stdClass;
		$response->total      = $cart->getTotalCost();
		$response->discount   = $cart->getTotalDiscount();
		$response->finalNet   = $totals->net;
		$response->finalTax   = $totals->tax;
		$response->finalTotal = $totals->gross;
		$response->items      = [];
		$response->message    = $msg;

		// prepare for JSON
		foreach ($cart->getItems() as $item_index => $item)
		{
			$std = new stdClass;
			$std->item_name      = $item->getItemName();
			$std->var_name       = $item->getOptionName();
			$std->price          = $item->getTotalCost();
			$std->original_price = $item->getTotalCostBeforeDiscount();
			$std->quantity       = $item->getQuantity();
			$std->index          = $item_index;
			$std->removable      = $item->canBeRemoved();

			$response->items[] = $std;
		}
		
		return $response;
	}

	/**
	 * Removes the specified item from the cart.
	 *
	 * @param   int  The index of the item to remove.
	 *
	 * @return  object|bool  An object holding the cart data on success, otherwise false.
	 */
	public function removeItem(int $index)
	{
		$cart = $this->getCart();
		
		// get selected item
		$item = $cart->getItemAt($index);

		if (!$item)
		{
			// item not found
			$this->setError(new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404));
			return false;
		}

		$item->remove($item->getQuantity());

		try
		{
			/**
			 * Fires an event whenever an item is going to be removed from the cart.
			 * 
			 * @param   E4J\VikRestaurants\TakeAway\Cart\Item  $item
			 * @param   E4J\VikRestaurants\TakeAway\Cart\Cart  $cart
			 * 
			 * @return  void
			 * 
			 * @throws  Exception  To abort the removing process.
			 * 
			 * @since   1.9.1
			 */
			VREFactory::getPlatform()->getDispatcher()->trigger('onRemoveTakeAwayCartItem', [$item, $cart]);
		}
		catch (Exception $error)
		{
			// a plugin aborted the removing process
			$this->setError($error);
			return false;
		}
		
		/**
		 * Reset cart to handle correctly deal_quantities.
		 *
		 * @since 1.8.5
		 */
		VikRestaurants::resetDealsInCart($cart);
		VikRestaurants::checkForDeals($cart);

		$cart->store();

		$totals = $cart->getTotals();
		
		// prepare AJAX response
		$response = new stdClass;
		$response->total      = $cart->getTotalCost();
		$response->discount   = $cart->getTotalDiscount();
		$response->finalNet   = $totals->net;
		$response->finalTax   = $totals->tax;
		$response->finalTotal = $totals->gross;
		$response->items      = [];

		// prepare for JSON
		foreach ($cart->getItems() as $item_index => $item)
		{
			$std = new stdClass;
			$std->item_name      = $item->getItemName();
			$std->var_name       = $item->getOptionName();
			$std->price          = $item->getTotalCost();
			$std->original_price = $item->getTotalCostBeforeDiscount();
			$std->quantity       = $item->getQuantity();
			$std->index          = $item_index;
			$std->removable      = $item->canBeRemoved();

			$response->items[] = $std;
		}

		return $response;
	}

	/**
	 * Validates the search arguments.
	 * 
	 * @param   array  &$data  The search data.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function checkIntegrity(array &$data = [])
	{
		$config = VREFactory::getConfig();

		// get cart instance
		$cart = $this->getCart();

		$reset_deals = false;

		if (empty($data['hourmin']))
		{
			// get check-in time, only if set
			$time = $cart->getCheckinTime();

			if ($time)
			{
				// update time from filters
				$data['hourmin'] = $time;
			}
		}
		else
		{
			$reset_deals = true;
		}
		
		// check if we should update the check-in date
		if ($config->getBool('tkallowdate') && $data['date'])
		{
			// update only if date is set and the check-in can be changed
			$cart->setCheckinTimestamp(VikRestaurants::createTimestamp($data['date'], 0, 0));

			$reset_deals = true;
		}
		else
		{
			// otherwise retrieve stored check-in
			$data['date'] = date($config->get('dateformat'), $cart->getCheckinTimestamp());
		}

		// validate the time against the available ones,
		// because the selected time might be not available
		// and the next one could be on a different shift
		if (!VikRestaurants::validateTakeAwayTime($data['hourmin'], $data['date']))
		{
			// invalid time, reset deals
			$reset_deals = true;
		}

		// refresh cart time
		$cart->setCheckinTime($data['hourmin']);

		// get service previously selected
		$prev_service = $cart->getService();

		if (is_null($data['service']))
		{
			// keep current service, if any
			$data['service'] = $prev_service;
		}

		// update service
		$cart->setService($data['service']);
		// make sure the provided service is supported
		$data['service'] = $cart->getService();

		if ($prev_service != $cart->getService())
		{
			// refresh deals in case the service has changed
			$reset_deals = true;
		}

		if ($reset_deals)
		{
			// re-check for deals when the date or time change
			VikRestaurants::resetDealsInCart($cart, $data['hourmin']);
			VikRestaurants::checkForDeals($cart);
		}
		
		// update cart
		$cart->store();

		// make sure the orders are allowed for the selected date time
		if (!VikRestaurants::isTakeAwayReservationsAllowedOn($cart->getCheckinTimestamp()))
		{
			// orders have been stopped
			$this->setError(JText::translate('VRTKMENUNOTAVAILABLE3'));
			return false;
		}

		/**
		 * Use an helper method to calculate the minimum cost 
		 * needed to proceed with the purchase.
		 *
		 * @since 1.8.3
		 */
		$mincost = Vikrestaurants::getTakeAwayMinimumCostPerOrder();

		// make sure the total cost of the cart reached the minimum threshold
		if ($cart->getTotalCost() < $mincost)
		{
			// format minimum cost
			$cost = VREFactory::getCurrency()->format($mincost);

			// continue shopping to reach the minimum cost
			$this->setError(JText::sprintf('VRTAKEAWAYMINIMUMCOST', $cost));
			return false;
		}

		// always revalidate the coupon code
		$this->revalidateCoupon($data);
		$cart->store();

		return true;
	}

	/**
	 * Returns the details of the redeemed coupon code.
	 * 
	 * @return  object|null
	 */
	public function getCoupon()
	{
		// clears the coupon from the session after retrieving it
		return JFactory::getSession()->set('order.coupon', null, 'vikrestaurants');
	}

	/**
	 * Helper method used to redeem the specified coupon code.
	 *
	 * @param   mixed  $coupon  Either the coupon details or its code.
	 * @param   array  $data    The search data.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function redeemCoupon($coupon, array $data = [])
	{
		if (empty($coupon))
		{
			// coupon code not specified
			$this->setError(JText::translate('VRCOUPONNOTVALID'));
			return false;
		}

		/** @var E4J\VikRestaurants\TakeAway\Cart */
		$cart = $this->getCart();

		// inject "takeaway" group within search arguments
		$data['group'] = 'takeaway';

		// inject total cost
		$data['total'] = $cart->getTotalCost();

		try
		{
			// validate coupon code compliance
			$coupon = (new E4J\VikRestaurants\Coupon\CouponValidator($data))->validate($coupon);

			// search for a coupon already registered within the cart
			$index = $cart->deals()->indexOfType('coupon');

			if ($index !== false)
			{
				// remove coupon discount from cart
				$cart->deals()->removeAt($index);
			}

			// insert discount within the cart as "coupon" deal
			$cart->deals()->insert(new E4J\VikRestaurants\TakeAway\Cart\Deals\Discount([
				'id'      => $coupon->code,
				'amount'  => $coupon->value,
				'percent' => $coupon->percentot == 1,
				'type'    => 'coupon',
			]));

			// commit changes
			$cart->store();
		}
		catch (Exception $e)
		{
			// cannot apply the coupon code
			$this->setError(JText::translate('VRCOUPONNOTVALID'));
			return false;
		}

		// coupon valid, register it within the user session
		JFactory::getSession()->set('order.coupon', $coupon, 'vikrestaurants');

		return true;
	}

	/**
	 * Revalidates the internal coupon code, since the cart might be no more
	 * compliant with the coupon restrictions after some changes.
	 * 
	 * @param   array  $data  The search data.
	 *
	 * @return  bool   True in case of valid coupon, false otherwise.
	 */
	public function revalidateCoupon(array $data = [])
	{
		$session = JFactory::getSession();

		// fetch coupon from the user session
		$coupon = $session->get('order.coupon', null, 'vikrestaurants');

		if (!$coupon)
		{
			// coupon discount not set
			return false;
		}

		// try to redeem the coupon code one more time
		if ($this->redeemCoupon($coupon, $data) === false)
		{
			// coupon no more valid, unset it
			$session->set('order.coupon', null, 'vikrestaurants');

			$cart = $this->getCart();

			// search for a coupon already registered within the cart
			$index = $cart->deals()->indexOfType('coupon');

			if ($index !== false && $index >= 0)
			{
				// remove coupon discount from cart too
				$cart->deals()->removeAt($index);

				// commit changes
				$cart->store();
			}

			return false;
		}

		// coupon still valid
		return true;
	}

	/**
	 * Updates the service charge/discount.
	 * 
	 * @param   string  $service  The service type.
	 * @param   int     $areaId   The area where the delivery service should be offered.
	 * 
	 * @return  object  An object holding the totals.
	 */
	public function updateServiceTotals(string $service, int $areaId = 0)
	{
		/** @var E4J\VikRestaurants\TakeAway\Cart */
		$cart = $this->getCart();

		/**
		 * Force the cart to use the provided service.
		 * 
		 * @since 1.9.1
		 */
		$cart->setService($service);

		$totalCost = $cart->getTotalCost();

		if ($service === 'delivery')
		{
			if (VikRestaurants::isTakeAwayFreeDeliveryService($cart))
			{
				// delivery is included within the total cost
				$value = 0;
			}
			else
			{
				// fetch delivery area from provided ID
				$area = JModelVRE::getInstance('tkarea')->getItem($areaId);

				// calculate delivery charge/discount
				$value = VikRestaurants::getTakeAwayDeliveryServiceAddPrice($totalCost, $area);
			}
		}
		else if ($service === 'pickup')
		{
			// calculate takeaway charge/discount
			$value = VikRestaurants::getTakeAwayPickupAddPrice($totalCost);
		}
		else
		{
			// service not supported...
			$value = 0;
		}

		/** @var E4J\VikRestaurants\TakeAway\Cart\Deals */
		$deals = $cart->deals();

		// check whether we already registered a service deal type
		$serviceIndex = $deals->indexOfType('service');

		if ($serviceIndex !== false)
		{
			// always remove any previously registered deal
			$deals->removeAt($serviceIndex);
		}

		$returnValue = new stdClass;
		$returnValue->type = 'none';

		if ($value > 0)
		{
			$returnValue->type = 'charge';

			// calculate taxes for the payment charge
			$returnValue->charge = E4J\VikRestaurants\Taxing\TaxesFactory::calculate(0, $value, [
				'subject' => 'takeaway.service',
			]);
		}
		else if ($value < 0)
		{
			$returnValue->type = 'discount';

			// register the charge as a discount
			$returnValue->discount = abs($value);

			// register a new deal to offer the discount per service
			$deals->insert(new E4J\VikRestaurants\TakeAway\Cart\Deals\Discount([
				'amount'   => $returnValue->discount,
				'percent'  => false,
				'type'     => 'service',
			]));
		}

		// refresh cart totals too
		$returnValue->totals = $cart->getTotals();

		// include total discount too
		$returnValue->totals->discount = $cart->getTotalDiscount();

		/**
		 * Save the cart details to avoid losing the selected service
		 * and any registered discount.
		 * 
		 * @since 1.9.1
		 */
		$cart->store();

		return $returnValue;
	}

	/**
	 * Returns an object containing the details of the
	 * take-away product and the related option, if requested.
	 *
	 * @param   int    $id_entry   The product ID.
	 * @param   int    $id_option  The variation ID, if supported.
	 *
	 * @return  mixed  An object in case the product exists, null otherwise.
	 */
	protected function getProduct(int $id_entry, int $id_option = 0)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select('e.*');
		$q->select($dbo->qn('o.id', 'oid'));
		$q->select($dbo->qn('o.name', 'oname'));
		$q->select($dbo->qn('o.inc_price', 'oprice'));
		$q->select($dbo->qn('m.taxes_type'));
		$q->select($dbo->qn('m.taxes_amount'));

		$q->from($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'e'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $dbo->qn('o.id_takeaway_menu_entry') . ' = ' . $dbo->qn('e.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $dbo->qn('e.id_takeaway_menu') . ' = ' . $dbo->qn('m.id'));

		$q->where($dbo->qn('e.id') . ' = ' . (int) $id_entry);
		$q->where($dbo->qn('e.published') . ' = 1');
		$q->where($dbo->qn('m.published') . ' = 1');

		if ((int) $id_option > 0)
		{
			$q->where($dbo->qn('o.id') . ' = ' . (int) $id_option);
			$q->where($dbo->qn('o.published') . ' = 1');
		}

		$dbo->setQuery($q, 0, 1);
		$item = $dbo->loadObject();

		if (!$item)
		{
			// product not found
			return null;
		}

		// In case the request didn't specify the option for
		// an item that supports options, the first one will
		// be automatically taken.

		if ($item->oid)
		{
			// build option object
			$item->option = new stdClass;
			$item->option->id    = $item->oid;
			$item->option->name  = $item->oname;
			$item->option->price = $item->oprice;
		}
		else
		{
			$item->option = null;
		}

		// calculate total cost
		$item->totalPrice = $item->price + (float) $item->oprice;

		// apply product translation
		VikRestaurants::translateTakeawayProducts($item);

		if ($item->option)
		{
			// apply variation translation
			VikRestaurants::translateTakeawayProductOptions($item->option);
		}

		// fetch full name (after translation)
		$item->fullName = $item->name . ($item->option ? ' - ' . $item->option->name : '');

		if ($item->taxes_type == 0)
		{
			// use global taxes
			$item->taxes_amount = VREFactory::getConfig()->getFloat('tktaxesratio');
		}

		$item->toppings = [];

		// fetch toppings groups

		$q = $dbo->getQuery(true);

		$q->select('g.*');
		$q->select($dbo->qn('a.id', 'topping_group_assoc_id'));
		$q->select($dbo->qn('a.id_topping'));
		$q->select($dbo->qn('a.rate', 'topping_rate'));
		$q->select($dbo->qn('t.name', 'topping_name'));

		$q->from($dbo->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_group_topping_assoc', 'a') . ' ON ' . $dbo->qn('a.id_group') . ' = ' . $dbo->qn('g.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_topping', 't') . ' ON ' . $dbo->qn('a.id_topping') . ' = ' . $dbo->qn('t.id'));

		$q->where($dbo->qn('g.id_entry') . ' = ' . $item->id);
		$q->where($dbo->qn('t.published') . ' = 1');

		if ($item->option)
		{
			$q->andWhere([
				$dbo->qn('g.id_variation') . ' <= 0',
				$dbo->qn('g.id_variation') . ' = ' . $item->option->id,
			], 'OR');
		}
		
		$q->order($dbo->qn('g.ordering') . ' ASC');
		$q->order($dbo->qn('a.ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $group)
		{
			if (!isset($item->toppings[$group->id]))
			{
				$tmp = new stdClass;
				$tmp->id           = $group->id;
				$tmp->title        = $group->title;
				$tmp->multiple     = $group->multiple;
				$tmp->min_toppings = $group->min_toppings;
				$tmp->max_toppings = $group->max_toppings;
				$tmp->use_quantity = $group->use_quantity;
				$tmp->list         = [];

				$item->toppings[$group->id] = $tmp;
			}
			
			if (!empty($group->topping_group_assoc_id))
			{
				$topping = new stdClass;
				$topping->id       = $group->id_topping;
				$topping->assoc_id = $group->topping_group_assoc_id;
				$topping->name     = $group->topping_name;
				$topping->rate     = $group->topping_rate;

				$item->toppings[$group->id]->list[] = $topping;
			}
		}

		return $item;
	}
}
