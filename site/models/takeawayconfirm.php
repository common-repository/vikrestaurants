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
 * VikRestaurants take-away confirmation model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTakeawayconfirm extends JModelVRE
{
	/**
	 * Completes the booking process by saving the take-away order.
	 *
	 * @param 	array  $data  An array containing some order options.
	 *
	 * @return 	mixed  The landing page URL on success, false otherwise.
	 */
	public function save($data)
	{
		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = VREFactory::getPlatform()->getDispatcher();

		/** @var E4J\VikRestaurants\Config\AbstractConfiguration */
		$config = VREFactory::getConfig();

		// get currently logged-in user
		$user = JFactory::getUser();

		// get cart model
		$model = JModelVRE::getInstance('tkcart');

		/** @var E4J\VikRestaurants\TakeAway\Cart */
		$cart = $model->getCart();

		// override provided date with the one saved in the cart
		$data['date'] = date($config->get('dateformat'), $cart->getCheckinTimestamp());

		////////////////////////////////////////////////////////////
		////////////////////// INITIALIZATION //////////////////////
		////////////////////////////////////////////////////////////

		try
		{
			/**
			 * Trigger event to manipulate the search data array.
			 *
			 * @param   array  &$data  The data array.
			 *
			 * @return  void
			 *
			 * @since   1.9
			 */
			$dispatcher->trigger('onInitSaveOrder', [&$data]);
		}
		catch (Exception $e)
		{
			// error thrown during the initialization of the order
			$this->setError($e);
			return false;
		}

		// prepare order array
		$order = [];

		////////////////////////////////////////////////////////////
		//////////////////// AVAILABILITY CHECK ////////////////////
		////////////////////////////////////////////////////////////

		// make sure the provided information are valid and complete
		if (!$model->checkIntegrity($data))
		{
			// propagate error
			$this->setError($model->getError());
			return false;
		}

		// check stock availability
		if (!VikRestaurants::checkCartStockAvailability($cart, $errors))
		{
			// Some products are no more available...
			// Update cart and abort saving process.
			$cart->store();

			// propagate all the registered errors one by one
			foreach ($errors as $error)
			{
				$this->setError($error);
			}

			return false;
		}

		// reset preparation timestamp
		$order['preparation_ts'] = null;

		// prepare availability search
		$search = new VREAvailabilityTakeaway($data['date'], $data['hourmin']);
		// check if the selected time slot is still available
		$avail = $search->isTimeAvailable($cart, $order['preparation_ts']);

		if (!$avail)
		{
			// check if we have a closing day for the selected checkin date
			if (VikRestaurants::isClosingDay($args))
			{
				// the selected day is closed
				$this->setError(JText::translate('VRSEARCHDAYCLOSED'));
			}
			else
			{
				// the selected time is no more available
				$this->setError(JText::translate('VRTKNOTIMEAVERR'));
			}

			return false;
		}

		////////////////////////////////////////////////////////////
		/////////////////// PREPARE BOOKING DATA ///////////////////
		////////////////////////////////////////////////////////////

		// register check-in timestamp
		list($data['hour'], $data['min']) = explode(':', $data['hourmin']);
		$order['checkin_ts'] = VikRestaurants::createTimestamp($data['date'], $data['hour'], $data['min']);

		if (!empty($data['asap']))
		{
			// remind that this order should be prepared as soon as possible
			$order['asap'] = true;
		}

		// register selected service
		$order['service'] = $data['service'];

		// register current language tag
		$order['langtag'] = JFactory::getLanguage()->getTag();

		// use the default status set in configuration
		$order['status'] = $config->get('tkdefstatus');

		// init order totals
		$order['total_net']    = 0;
		$order['total_tax']    = 0;
		$order['total_to_pay'] = 0;
		$order['discount_val'] = 0;

		/**
		 * When provided, add gratuity to grand total.
		 *
		 * @since 1.7.4
		 */
		if ($data['gratuity'])
		{
			$order['tip_amount']   = abs(round($data['gratuity'], 2));
			$order['total_to_pay'] += $order['tip_amount'];
		}

		////////////////////////////////////////////////////////////
		///////////////////// VALIDATE SERVICE /////////////////////
		////////////////////////////////////////////////////////////

		/** @var array (associative) */
		$services = E4J\VikRestaurants\CustomFields\Factory::getSupportedServices(true);

		// make sure the selected service is supported
		if (empty($services[$order['service']]))
		{
			// service not supported, use the default one
			$order['service'] = $cart->getService();
		}

		$deliveryInfo = null;

		if ($order['service'] === 'delivery')
		{
			/** @var E4J\VikRestaurants\DeliveryArea\AreasCollection */
			$zones = E4J\VikRestaurants\DeliveryArea\AreasCollection::getInstance()
				->filter(new E4J\VikRestaurants\DeliveryArea\Filters\PublishedFilter);

			// fetch delivery address specified by the user
			$deliveryInfo = $this->getDeliveryAddress();

			if (count($zones))
			{
				if (!$deliveryInfo)
				{
					// the address hasn't been properly validated against the supported delivery areas
					$this->setError(JText::translate('VRTKDELIVERYLOCNOTFOUND'));
					return false;
				}

				// revalidate the delivery area according to the provided query
				$area = (new E4J\VikRestaurants\DeliveryArea\DeliveryChecker($zones))->search($deliveryInfo->query);

				/** @var E4J\VikRestaurants\DeliveryArea\Area|null $area */

				if (!$area)
				{
					// clear delivery address from session
					$this->setDeliveryAddress(null);

					// cannot deliver to the provided address
					$this->setError(JText::translate('VRTKDELIVERYLOCNOTFOUND'));
					return false;		
				}

				// overwrite area with the provided one
				$deliveryInfo->area = $area;
			}
		}

		if (empty($data['purchaser_address']) && $deliveryInfo)
		{
			// copy full address within order data (if not set)
			$data['purchaser_address'] = $deliveryInfo->query->getComponent('fullAddress');
		}

		// in case of delivery area, revalidate the minimum total order as the default one
		// might have been overwritten with an higher one
		if ($deliveryInfo)
		{
			/**
			 * Use an helper method to calculate the minimum cost 
			 * needed to proceed with the purchase.
			 *
			 * @since 1.8.3
			 */
			$minCost = Vikrestaurants::getTakeAwayMinimumCostPerOrder($deliveryInfo->area->min_cost, $data);

			// make sure the total cost of the cart reached the minimum threshold
			if ($cart->getTotalCost() < $minCost)
			{
				// format minimum cost
				$cost = VREFactory::getCurrency()->format($minCost);

				// continue shopping to reach the minimum cost
				$this->setError(JText::sprintf('VRTAKEAWAYMINIMUMCOST', $cost));
				return false;
			}
		}

		// obtain service charge data
		$serviceCharge = $model->updateServiceTotals($order['service'], $deliveryInfo->area->id ?? 0);

		if ($serviceCharge->type === 'charge')
		{
			// register service net and tax
			$order['delivery_charge'] = $serviceCharge->charge->net;
			$order['delivery_tax']    = $serviceCharge->charge->tax;

			// increase totals too
			$order['total_tax']    += $serviceCharge->charge->tax;
			$order['total_to_pay'] += $serviceCharge->charge->gross;
		}

		// NOTE: in case the service offers a discount, it will be treated accordingly
		// by the cart instance, which considers it as a deal.

		////////////////////////////////////////////////////////////
		//////////////////// FETCH BOOKED ITEMS ////////////////////
		////////////////////////////////////////////////////////////

		/**
		 * The cart totals must be loaded after reassigning all the 
		 * custom "deals", such as the discounts that a service might apply.
		 * 
		 * @since 1.9
		 */
		$cartTotals = $cart->getTotals();

		$itemsTotals = $cart->getTotalsPerItem();

		$order['items'] = [];

		foreach ($cart->getItems() as $index => $item)
		{
			// create order item
			$orderItem = [
				'id_product'        => $item->getItemID(),
				'id_product_option' => $item->getOptionID(),
				'quantity'          => $item->getQuantity(),
				'notes'             => $item->getAdditionalNotes(),
				'price'             => $itemsTotals[$index]->priceBeforeDiscount / $item->getQuantity(),
				'net'               => $itemsTotals[$index]->net,
				'tax'               => $itemsTotals[$index]->tax,
				'gross'             => $itemsTotals[$index]->gross,
				'discount'          => $itemsTotals[$index]->discount,
				'tax_breakdown'     => $itemsTotals[$index]->breakdown,
				'groups'            => [],
			];

			foreach ($item->getToppingsGroups() as $group)
			{
				// set up toppings group
				$itemGroup = [
					'id'       => $group->getGroupID(),
					'toppings' => [],
					'units'    => [],
				];

				foreach ($group->getToppings() as $topping)
				{
					// registed selected topping
					$itemGroup['toppings'][] = $topping->getToppingID();
					// map topping with the number of selected units
					$itemGroup['units'][$topping->getToppingID()] = $topping->getUnits();
				}

				// attach group to order item
				$orderItem['groups'][] = $itemGroup;
			}

			// register order item
			$order['items'][] = $orderItem;
		}

		// increase order totals
		$order['total_net']    += $cartTotals->net;
		$order['total_tax']    += $cartTotals->tax;
		$order['total_to_pay'] += $cartTotals->gross;
		$order['discount_val'] += $cart->getTotalDiscount();

		////////////////////////////////////////////////////////////
		//////////////////// FETCH CUSTOM FIELDS ///////////////////
		////////////////////////////////////////////////////////////

		/**
		 * Retrieve custom fields by using the related helper.
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter);

		/** @var E4J\VikRestaurants\CustomFields\FieldService */
		$fieldService = $services[$data['service']];

		// create requestor for the take-away custom fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor($customFields, $fieldService);

		try
		{
			// load fields
			$order['custom_f'] = $requestor->loadForm($fieldsData, $strict = true);
		}
		catch (Exception $e)
		{
			// invalid fields, propagate error message
			$this->setError($e);
			return false;
		}

		/**
		 * Trigger event to manipulate the custom fields array and the
		 * billing information of the customer, extrapolated from the rules
		 * of the custom fields.
		 *
		 * @param   array   &$order  The order details.
		 * @param   array   &$args   The billing array.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onPrepareFieldsSaveOrder', [&$order, &$fieldsData]);

		// register data fetched by the custom fields so that the order
		// model is able to use them for saving purposes
		$order['fields_data'] = $fieldsData;

		if (empty($order['fields_data']['purchaser_nominative']))
		{
			// use name of the currently logged-in user
			$order['fields_data']['purchaser_nominative'] = $user->name;
		}

		if (empty($order['fields_data']['purchaser_mail']))
		{
			// use e-mail of the currently logged-in user
			$order['fields_data']['purchaser_mail'] = $user->email;
		}

		////////////////////////////////////////////////////////////
		///////////////////// VALIDATE PAYMENT /////////////////////
		////////////////////////////////////////////////////////////

		$payment = null;
		
		if ($order['total_to_pay'] > 0)
		{
			if (!isset($data['id_payment']))
			{
				$data['id_payment'] = 0;
			}

			// unset payment charge
			$order['payment_charge'] = 0;
			$order['payment_tax']    = 0;

			/** @var E4J\VikRestaurants\Collection\Item[] */
			$payments = E4J\VikRestaurants\Payment\PaymentsCollection::getInstance()
				->filter(new E4J\VikRestaurants\Payment\Filters\TakeAwayGroupFilter)
				->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter)
				->filter(new E4J\VikRestaurants\Payment\Filters\TotalCostFilter($cart))
				->filter(new E4J\VikRestaurants\Payment\Filters\TrustedCustomerFilter('takeaway'))
				->filter(new E4J\VikRestaurants\Payment\Filters\PluginAvailabilityFilter('takeaway'));

			if ($payments->count())
			{
				// obtain only the selected payment
				$payments = $payments->filter(new E4J\VikRestaurants\Collection\Filters\NumberFilter('id', $data['id_payment'], '='));

				if (!$payments->count())
				{
					// invalid payment method
					$this->setError(JText::translate('VRERRINVPAYMENT'));
					return false;
				}

				// take the first payment available
				$payment = $payments->getIterator()[0];

				// register payment ID
				$order['id_payment'] = $payment->id;

				if ($payment->charge > 0)
				{
					// apply payment charge to total gross
					if ($payment->percentot == 1)
					{
						// percentage charge based on total gross
						$charge = $order['total_to_pay'] * (float) $payment->charge / 100;
					}
					else
					{
						// fixed amount
						$charge = (float) $payment->charge;
					}

					// calculate taxes
					$charge = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($payment->id, $charge, [
						'subject' => 'payment',
					]);

					// set payment charge
					$order['payment_charge'] = $charge->net;
					$order['payment_tax']    = $charge->tax;

					// increase the bill totals too, because this charge should be an extra
					$order['total_tax']    += $charge->tax;
					$order['total_to_pay'] += $charge->gross;
				}

				// auto-confirm order according to the configuration of
				// the payment, otherwise force PENDING status to let the
				// customers be able to start a transaction
				if ($payment->setconfirmed)
				{
					// auto-confirm order
					$order['status'] = JHtml::fetch('vrehtml.status.confirmed', 'takeaway', 'code');
				}
				else
				{
					// leave it pending
					$order['status'] = JHtml::fetch('vrehtml.status.pending', 'takeaway', 'code');
				}
			}
		}

		////////////////////////////////////////////////////////////
		///////////////////// FETCH COUPON CODE ////////////////////
		////////////////////////////////////////////////////////////

		// check whether the coupon code was set
		$coupon = $model->getCoupon();

		if ($coupon)
		{
			// assign coupon code to the order
			$order['coupon'] = (array) $coupon;

			// redeem coupon code
			JModelVRE::getInstance('coupon')->redeem($coupon);
		}

		////////////////////////////////////////////////////////////
		///////////////////// USER REGISTRATION ////////////////////
		////////////////////////////////////////////////////////////

		// save user data
		if (!$user->guest || !empty($order['fields_data']['purchaser_mail']))
		{
			// create customer data
			$customer = [
				'id'       => 0,
				'jid'      => $user->guest ? 0 : $user->id,
				'tkfields' => $order['custom_f'],
			];

			// inject fetched billing details
			$customer = array_merge($customer, $order['fields_data']);

			// avoid to replace the customer notes with the reservation notes
			unset($customer['notes']);

			// get customer model
			$customerModel = JModelVRE::getInstance('customer');

			// insert/update customer
			if ($id_user = $customerModel->save($customer))
			{
				// assign order to saved customer
				$order['id_user'] = $id_user;
			}
		}

		if (!empty($order['id_user']) && $deliveryInfo)
		{
			// get customer locations
			$userLocations = VikRestaurants::getCustomer($order['id_user'])->locations;

			$locationData = [];

			// iterate all user locations
			foreach ($userLocations as $addr)
			{
				/**
				 * Check if the address already exists by comparing the single components
				 * of the full address.
				 *
				 * @since 1.7.4
				 */
				if (!$locationData && VikRestaurants::compareAddresses((array) $addr, $deliveryInfo->query))
				{
					/**
					 * Register ID found.
					 *
					 * @since 1.8.3
					 */
					$locationData['id'] = (int) $addr->id;
				}
			}

			if (!$locationData)
			{
				// prepare delivery location data for INSERT
				$locationData = [
					'id_user'   => $order['id_user'],
					'country'   => $deliveryInfo->query->getComponent('country'),
					'state'     => $deliveryInfo->query->getComponent('state'),
					'city'      => $deliveryInfo->query->getCity(),
					'address'   => $deliveryInfo->query->getAddress(),
					'zip'       => $deliveryInfo->query->getZipCode(),
					'latitude'  => $deliveryInfo->query->getCoordinates()->latitude,
					'longitude' => $deliveryInfo->query->getCoordinates()->longitude,
				];

				if (is_array($locationData['address']))
				{
					$locationData['address'] = implode(' ', $locationData['address']);
				}
			}

			/**
			 * Register delivery notes within location record.
			 *
			 * @since 1.8.3
			 */
			if (!empty($fieldsData['delivery_notes']))
			{
				$locationData['note'] = $fieldsData['delivery_notes'];
			}

			/**
			 * Save only in case there's something to bind in addition
			 * to the ID (UPDATE). Needed to apply the delivery notes
			 * to locations that have been already saved.
			 *
			 * @since 1.8.3
			 */
			if (count($locationData) >= 2)
			{
				// save location
				JModelVRE::getInstance('userlocation')->save($locationData);
			}
		}

		////////////////////////////////////////////////////////////
		//////////////////////// SAVE ORDER ////////////////////////
		////////////////////////////////////////////////////////////

		$orderModel = JModelVRE::getInstance('tkreservation');

		// save the order
		if (!$orderModel->save($order))
		{
			// propagate error
			$this->setError($orderModel->getError());
			return false;	
		}

		// flush the items saved in the cart
		$cart->clear()->store();

		// unset delivery address from session
		$this->setDeliveryAddress(null);

		// get order saved data
		$ordData = $orderModel->getData();

		$ordnum = $ordData['id'];
		$ordkey = $ordData['sid'];

		////////////////////////////////////////////////////////////
		////////////////////// NOTIFICATIONS ///////////////////////
		////////////////////////////////////////////////////////////

		$mailOptions = [];
		// validate e-mail rules before sending
		$mailOptions['check'] = true;

		// send e-mail notification to the customer
		$orderModel->sendEmailNotification($ordnum, $mailOptions);

		// send e-mail notification to the administrators and operators
		$mailOptions['client'] = 'admin';
		$orderModel->sendEmailNotification($ordnum, $mailOptions);
		
		if (JHtml::fetch('vrehtml.status.isapproved', 'takeaway', $ordData['status']))
		{
			// try to send SMS notifications, only for approved statuses (0: takeaway)
			VikRestaurants::sendSmsAction($ordData['purchaser_phone'], $ordnum, 1);
		}

		$redirect_url = "index.php?option=com_vikrestaurants&view=order&ordnum={$ordnum}&ordkey={$ordkey}";

		if (!empty($data['itemid']))
		{
			$redirect_url .= "&Itemid={$data['itemid']}";
		}

		/**
		 * Trigger event to manipulate the redirect URL after completing
		 * the take-away ordering process.
		 *
		 * Use VREOrderFactory::getOrder($ordnum) to access the order details.
		 *
		 * @param   string  &$url   The redirect URL (plain).
		 * @param   int     $order  The order id.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onRedirectOrder', [&$redirect_url, $ordnum]);
		
		// rewrite landing page
		return JRoute::rewrite($redirect_url, false);
	}

	/**
	 * Returns the delivery address specified by the user.
	 * 
	 * @return  object|null
	 */
	public function getDeliveryAddress()
	{
		// obtain serialized delivery address from session, if any
		$data = JFactory::getSession()->get('delivery_address', null, 'vikrestaurants');

		if (!$data)
		{
			// no specified delivery address
			return null;
		}

		// return unserialized delivery address
		return unserialize($data);
	}

	/**
	 * Updates the delivery address of the user.
	 * 
	 * @param   object|null
	 * 
	 * @return  void
	 */
	public function setDeliveryAddress($data = null)
	{
		$session = JFactory::getSession();

		if ($data)
		{
			// register serialized delivery address in session
			$session->set('delivery_address', serialize($data), 'vikrestaurants');
		}
		else
		{
			// clear delivery address from session
			$session->clear('delivery_address', 'vikrestaurants');
		}
	}
}
