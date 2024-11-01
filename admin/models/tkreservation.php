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
 * VikRestaurants take-away order model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkreservation extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return  mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		$order = parent::getItem($pk, $new);

		if (!$order)
		{
			return null;
		}

		$db = JFactory::getDbo();

		$config = VREFactory::getConfig();

		// decode registered custom fields
		$order->custom_f = $order->custom_f ? json_decode($order->custom_f, true) : [];
		$order->route    = $order->route    ? json_decode($order->route)          : null;

		if ($order->id)
		{
			// convert check-in date and time from timestamp
			$order->date    = date($config->get('dateformat'), $order->checkin_ts);
			$order->hourmin = date('H:i', $order->checkin_ts);
		}
		else
		{
			// create default date and time
			$order->date    = date($config->get('dateformat'), VikRestaurants::now());
			$order->hourmin = VikRestaurants::getClosestTime($order->date, true);

			if (is_numeric($order->date))
			{
				$order->date = date($config->get('dateformat'), $order->date);
			}
		}

		return $order;
	}

	/**
	 * Recovers the list of items assigned to the bill of the specified order.
	 * 
	 * @param   int       $id  The order ID.
	 * 
	 * @return  object[]  A list of items.
	 */
	public function getBillItems(int $id)
	{
		$orderItemModel = JModelVRE::getInstance('tkresprod');

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('i.*');
		$query->from($db->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'i'));

		$query->select($db->qn('p.name', 'product_name'));
		$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'p') . ' ON ' . $db->qn('p.id') . ' = ' . $db->qn('i.id_product'));

		$query->select($db->qn('o.name', 'option_name'));
		$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('o.id') . ' = ' . $db->qn('i.id_product_option'));

		$query->select($db->qn('m.id', 'menu_id'));
		$query->select($db->qn('m.title', 'menu_title'));
		$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $db->qn('p.id_takeaway_menu') . ' = ' . $db->qn('m.id'));

		$query->where($db->qn('i.id_res') . ' = ' . $id);
		$query->order($db->qn('i.id') . ' ASC');		

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $item)
		{
			// append the option name, if selected
			$item->name = $item->product_name . ($item->option_name ? ' - ' . $item->option_name : '');

			/** @var object[] */
			$item->groups = $orderItemModel->getToppings($item->id);
		}

		return $items;
	}

	/**
	 * Basic save implementation.
	 *
	 * @param   mixed  $data  Either an array or an object of data to save.
	 *
	 * @return  mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		// create a cache of the order before applying the changes
		$changesLogger = $this->observeChanges($data['id'] ?? 0);

		if (!empty($data['id']))
		{
			// register current datetime as modified date, if not specified
			if (!isset($data['modifiedon']))
			{
				$data['modifiedon'] = VikRestaurants::now();
			}

			// load order details before saving them
			$previousOrder = $this->getTable();
			$previousOrder->load($data['id']);
			$previousOrder = (object) $previousOrder->getProperties();
		}
		else
		{
			// create empty order for a better ease of use
			$previousOrder = (object) $this->getTable()->getProperties();
		}

		if (empty($data['id']) && empty($data['status']))
		{
			// status not specified, use the default confirmed one
			$data['status'] = JHtml::fetch('vrehtml.status.confirmed', 'takeaway', 'code');
		}

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		$savedData = $this->getData();

		if (isset($data['items']))
		{
			$model = JModelVRE::getInstance('tkresprod');

			// iterate all the provided items
			foreach ($data['items'] as $i => $item)
			{
				if (is_string($item))
				{
					// JSON given, decode it
					$item = json_decode($item, true);
				}

				// attach item to this order
				$item['id_res'] = $id;

				// save item
				$model->save($item);
			}
		}

		/**
		 * Check whether we should apply or delete the service charge.
		 * 
		 * NOTE: the `updateServiceCharge()` method is executed before applying the discount.
		 * This means that, in case the service offers the discount, it will be overwritten by 
		 * the discount in case the user chooses to apply a coupon or a manual offer simultaneously
		 * with the update of the service. In order to bypass this limitation, the discount has to be
		 * offered before the update of the service.
		 */
		if (!empty($data['update_service_charge']))
		{
			$this->updateServiceCharge($id, $data['update_service_charge']);
		}

		// check whether we should apply or delete a discount
		if (!empty($data['add_discount']))
		{
			$this->addDiscount($id, $data['add_discount']);
		}
		else if (!empty($data['remove_discount']))
		{
			$this->removeDiscount($id);
		}

		// check whether we should apply or delete a tip
		if (!empty($data['add_tip']))
		{
			$this->addTip($id, $data['add_tip']);
		}
		else if (!empty($data['remove_tip']))
		{
			$this->removeTip($id);
		}

		if (isset($data['id_payment']) && $data['id_payment'] != $previousOrder->id_payment)
		{
			// payment changed, look for a new charge to apply
			$this->applyPaymentCharge($id, $data['id_payment']);
		}

		if (!empty($data['deleted_items']))
		{
			// Delete the items here in order to properly apply all the changes before
			// sending the e-email and checking any differences with the previous record
			JModelVRE::getInstance('tkresprod')->delete($data['deleted_items']);
		}

		// generate changes log (1: take-away)
		$changesLogger->generate($id, 1);

		if (!empty($data['notifycust']))
		{
			// send e-mail notification to customer
			$this->sendEmailNotification($id);
		}

		// prepare event data
		$isNew      = empty($data['id']);
		$data['id'] = $id;

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = VREFactory::getPlatform()->getDispatcher();

		// check whether the status of the order has been changed
		if (!empty($data['status']) && $data['status'] !== $previousOrder->status)
		{
			/**
			 * Trigger event to let the plugins be notified every time the status of the orders change.
			 *
			 * @param   array  $data  The saved record.
			 *
			 * @return  void
			 *
			 * @since   1.9
			 */
			$dispatcher->trigger('onStatusChangeTakeawayOrder', [$data]);
		}

		/**
		 * Trigger event to allow the plugins to make something after saving
		 * an order into the database. Fires once all the details of
		 * the order has been saved.
		 *
		 * @param   array   $data   The saved record.
		 * @param   bool    $isNew  True if the record was inserted.
		 * @param   JModel  $model  The model instance.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onAfterSaveTkreservationLate', [$data, $isNew, $this]);

		/**
		 * Overwrite internal saved data, otherwise in case of self-save the
		 * internal data would be replaced by the last update call.
		 * 
		 * @since 1.9.1
		 */
		$this->set('data', $savedData);

		return $id;
	}

	/**
	 * Changes the reservation code for the specified record.
	 * 
	 * @param   int    $id    The order ID.
	 * @param   mixed  $code  Either an array/object containing the code details
	 *                        or the primary key of the code.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function changeCode(int $id, $code)
	{
		if ((int) $id <= 0)
		{
			$this->setError(new Exception('Missing order ID', 400));
			return false;
		}

		if (is_numeric($code))
		{
			/** @var stdClass */
			$code = JModelVRE::getInstance('rescode')->getItem((int) $code, $blank = true);
		}

		$code = (array) $code;

		if (!isset($code['id']))
		{
			$this->setError(new Exception('Missing reservation code ID', 400));
			return false;
		}

		// save order first
		$saved = $this->save([
			'id'      => (int) $id,
			'rescode' => $code['id'],
		]);

		if (!$saved)
		{
			// unable to save the order
			return false;
		}

		if ($code['id'])
		{
			/** @var JModelLegacy */
			$resCodeModel = JModelVRE::getInstance('rescodeorder');

			// try to update the history of the order
			$saved = $resCodeModel->save([
				'group'      => 2,
				'id_order'   => (int) $id,
				'id_rescode' => (int) $code['id'],
				'notes'      => $code['notes'] ?? null,
			]);

			if (!$saved)
			{
				// propagate encountered error
				$this->setError($resCodeModel->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function delete($ids)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		// invoke parent first
		if (!parent::delete($ids))
		{
			// nothing to delete
			return false;
		}

		$db = JFactory::getDbo();

		// load any assigned products
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_assoc'))
			->where($db->qn('id_res') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($products = $db->loadColumn())
		{
			// delete all the products that belong to the removed orders
			JModelVRE::getInstance('tkresprod')->delete($products);
		}

		// load any assigned order status
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_order_status'))
			->where($db->qn('group') . ' = 2')
			->where($db->qn('id_order') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($statuses = $db->loadColumn())
		{
			// delete all the statuses that belong to the removed orders
			JModelVRE::getInstance('rescodeorder')->delete($statuses);
		}

		return true;
	}

	/**
	 * Sends an e-mail notification to the customer of the specified order.
	 *
	 * @param   int    $id       The order ID.
	 * @param   array  $options  An array of options.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function sendEmailNotification(int $id, array $options = [])
	{
		// fetch receiver alias
		$client = isset($options['client']) ? $options['client'] : 'customer';

		try
		{
			/** @var E4J\VikRestaurants\Mail\MailTemplate */
			$mailTemplate = E4J\VikRestaurants\Mail\MailFactory::getTemplate('takeaway', $client, $id, $options);

			// in case the "check" attribute is set, we need to make
			// sure whether the specified client should receive the
			// e-mail according to the configuration rules
			if (!empty($options['check']) && !$mailTemplate->shouldSend())
			{
				// configured to avoid receiving this kind of e-mails
				return false;
			}

			// send notification
			$sent = (new E4J\VikRestaurants\Mail\MailDeliverer)->send($mailTemplate->getMail());
		}
		catch (Exception $e)
		{
			// probably order not found, register error message
			$this->setError($e->getMessage());

			return false;
		}

		return $sent;
	}

	/**
	 * Sends a SMS notification to the customer of the specified order.
	 *
	 * @param 	integer  $id  The order ID.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function sendSmsNotification($id)
	{
		try
		{
			// get current SMS instance
			$smsapi = VREApplication::getInstance()->getSmsInstance();
		}
		catch (Exception $e)
		{
			// SMS API not configured
			$this->setError(JText::translate('VRSMSESTIMATEERR1'));

			return false;
		}

		try
		{
			// load order details
			$order = VREOrderFactory::getOrder($id);
		}
		catch (Exception $e)
		{
			// order not found
			$this->setError($e->getMessage());

			return false;
		}

		// make sure we have a phone number
		if (!$order->purchaser_phone)
		{
			// register error
			$this->setError('Missing phone number.');

			return false;
		}

		// make sure the phone number reports a dial code
		if ($order->purchaser_prefix && !preg_match("/^\+/", $order->purchaser_phone))
		{
			// nope, add the specified one (backward compatibility)
			$order->purchaser_phone = $order->purchaser_prefix . $order->purchaser_phone;
		}

		// fetch sms message
		$text = VikRestaurants::getSmsCustomerTextMessage($order, $group = 1);

		// send message
		$response = $smsapi->sendMessage($order->purchaser_phone, $text);

		// validate response
		if (!$smsapi->validateResponse($response))
		{
			// unable to send the notification, register error message
			$log = $smsapi->getLog();

			if ($log)
			{
				$this->setError($log);
			}

			return false;
		}

		return true;
	}

	/**
	 * Updates the service charge for the specified order.
	 *
	 * @param 	integer  $id      The order ID.
	 * @param 	array    $coupon  An array containing the charges to apply.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function updateServiceCharge(int $id, array $charges)
	{
		$order = $this->getTable();
		$order->load($id);

		$discount = 0;

		// first of all, substract the delivery charge from the totals
		$order->total_tax    -= $order->delivery_tax;
		$order->total_to_pay -= $order->delivery_tax + $order->delivery_charge;

		// reset delivery charge
		$order->delivery_tax    = 0;
		$order->delivery_charge = 0;

		foreach ($charges as $charge)
		{
			if (empty($charge['value']))
			{
				continue;
			}

			if (empty($charge['percentot']) || $charge['percentot'] == 1)
			{
				// we have a percentage amount, calculate the charge among the total bill value
				$value = $order->total_to_pay * (float) $charge['value'] / 100;
			}
			else
			{
				// we have a fixed amount
				$value = (float) $charge['value'];
			}

			if ($value > 0)
			{
				// calculate taxes for the payment charge
				$result = E4J\VikRestaurants\Taxing\TaxesFactory::calculate(0, $value, [
					'lang'    => $order->langtag,
					'id_user' => $order->id_user,
					'subject' => 'takeaway.service',
				]);

				// refresh service NET and TAX
				$order->delivery_charge += $result->net;
				$order->delivery_tax    += $result->tax;
			}
			else if ($value < 0)
			{
				// register the charge as a discount
				$discount += abs($value);
			}
		}

		// commit changes
		$data = new stdClass;
		$data->id              = $order->id;
		$data->total_tax       = $order->total_tax + $order->delivery_tax;
		$data->total_to_pay    = $order->total_to_pay + $order->delivery_tax + $order->delivery_charge;
		$data->delivery_tax    = $order->delivery_tax;
		$data->delivery_charge = $order->delivery_charge;

		JFactory::getDbo()->updateObject('#__vikrestaurants_takeaway_reservation', $data, 'id');

		if ($discount > 0)
		{
			// the selected service includes a discount, register it accordingly
			return $this->addDiscount($id, [
				'value'     => $discount + $order->discount_val,
				'percentot' => 2, // fixed
			]);
		}

		return true;
	}

	/**
	 * Adds a discount to the specified order.
	 *
	 * @param 	integer  $id      The order ID.
	 * @param 	mixed    $coupon  Either a coupon code or an array/object
	 *                            containing its details.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function addDiscount(int $id, $coupon)
	{
		// get coupon model
		$couponModel = JModelVRE::getInstance('coupon');

		if (is_string($coupon))
		{
			// get coupon code details
			$coupon = $couponModel->getItem(['code' => $coupon]);
		}
		else
		{
			// treat as object
			$coupon = (object) $coupon;
		}

		// make sure we have a valid coupon code
		if (!$coupon || !isset($coupon->value))
		{
			// invalid/missing coupon
			$this->setError('Missing coupon code');

			return false;
		}

		$db = JFactory::getDbo();

		// load any children (items)
		$query = $db->getQuery(true)
			->select($db->qn(['id', 'id_product', 'price', 'quantity']))
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_assoc'))
			->where($db->qn('id_res') . ' = ' . (int) $id)
			->where($db->qn('price') . ' > 0');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		// load order details
		$order = $this->getTable();
		$order->load((int) $id);

		// define options for tax calculation
		$options = [
			'subject' => 'takeaway.item',
			'lang'    => $order->langtag,
			'id_user' => $order->id_user,
		];

		$total_c = 0;

		// calculate total cost
		foreach ($items as $item)
		{
			$total_c += (float) $item->price;
		}

		// prepare order data
		$orderData = [
			'id'           => $order->id,
			'total_to_pay' => $order->payment_charge + $order->payment_tax + $order->delivery_charge + $order->delivery_tax + $order->tip_amount,
			'total_net'    => 0,
			'total_tax'    => $order->payment_tax + $order->delivery_tax,
			'discount_val' => 0,
			'coupon'       => '',
			'items'        => [],
		];

		// recalculate products
		foreach ($items as $i => $item)
		{
			$cost_with_disc = $item->price * $item->quantity;

			if (empty($coupon->percentot) || $coupon->percentot == 1)
			{
				// percentage discount
				$disc_val = round($cost_with_disc * $coupon->value / 100, 2);
			}
			else
			{
				if ($i < count($items) - 1)
				{
					// fixed discount, apply proportionally according to
					// the total cost of all the items
					$percentage = $cost_with_disc * 100 / $total_c;
					$disc_val = round($coupon->value * $percentage / 100, 2);
				}
				else
				{
					// We are fetching the last element of the list, instead of calculating the
					// proportional discount, we should subtract the total discount from the coupon
					// value, in order to avoid rounding issues. Let's take as example a coupon of
					// EUR 10 applied on 3 options. The final result would be 3.33 + 3.33 + 3.33,
					// which won't match the initial discount value of the coupon. With this
					// alternative way, the result would be: 10 - 3.33 - 3.33 = 3.34.
					$disc_val = $coupon->value - $orderData['discount_val'];
				}

				// the discount cannot exceed the total price of the item
				$disc_val = min(array($cost_with_disc, $disc_val));
			}

			// increase total discount
			$orderData['discount_val'] += $disc_val;

			// subtract discount from item cost
			$cost_with_disc -= $disc_val;

			// recalculate totals
			$totals = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($item->id_product, $cost_with_disc, $options);

			// prepare item to save
			$itemData = [
				'id'            => $item->id,
				'net'           => $totals->net,
				'tax'           => $totals->tax,
				'gross'         => $totals->gross,
				'discount'      => $disc_val,
				'tax_breakdown' => $totals->breakdown,
			];

			// update order totals
			$orderData['total_net']    += $itemData['net'];
			$orderData['total_tax']    += $itemData['tax'];
			$orderData['total_to_pay'] += $itemData['gross'];

			// append to options list
			$orderData['items'][] = $itemData;
		}

		if (!empty($coupon->code))
		{
			// save coupon data
			$orderData['coupon'] = $coupon;

			// redeem coupon usage
			$couponModel->redeem($coupon);
		}
		else if (!empty($coupon->remove))
		{
			// remove any registered coupon
			$orderData['coupon_str'] = '';
		}

		// update order details
		return $this->save($orderData);
	}

	/**
	 * Removes discount from the specified order.
	 *
	 * @param 	integer  $id  The order ID.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function removeDiscount(int $id)
	{
		return $this->addDiscount($id, [
			'value'     => 0,
			'percentot' => 2,
			'remove'    => true,
		]);
	}

	/**
	 * Adds a tip to the specified order.
	 *
	 * @param 	integer  $id   The order ID.
	 * @param 	mixed    $tip  Either an array/object containing the tip details.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function addTip(int $id, $tip)
	{
		if (is_numeric($tip))
		{
			// fixed amount received, create rule
			$tip = [
				'value'     => (float) $tip,
				'percentot' => 2,
			];
		}
		else if (is_string($tip) && preg_match("/([0-9.,]+)\s*%/", $tip, $match))
		{
			// percentage amount received, create rule
			$tip = [
				'value'     => (float) str_replace(',', '.', end($match)),
				'percentot' => 1,
			];
		}
		else
		{
			// convert rule into an array for a better ease of use
			$tip = (array) $tip;
		}

		$order = $this->getTable();
		$order->load($id);

		// subtract the previous tip amount (if any) from the total cost
		$order->total_to_pay -= $order->tip_amount;

		if (empty($tip['percentot']) || $tip['percentot'] == 2)
		{
			// we have a fixed amount
			$order->tip_amount = abs($tip['value']);
		}
		else
		{
			// we have a percentage amount, calculate it above the grand total
			$order->tip_amount = $order->total_to_pay * abs($tip['value']) / 100;
		}

		// add new tip to the total
		$order->total_to_pay += $order->tip_amount;

		// commit changes
		$data = new stdClass;
		$data->id           = $order->id;
		$data->total_to_pay = $order->total_to_pay;
		$data->tip_amount   = $order->tip_amount;

		return JFactory::getDbo()->updateObject('#__vikrestaurants_takeaway_reservation', $data, 'id');
	}

	/**
	 * Removes the previous tip from the specified order.
	 *
	 * @param 	integer  $id  The order ID.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function removeTip(int $id)
	{
		return $this->addTip($id, 0);
	}

	/**
	 * Updates the status of all the orders out of time to REMOVED.
	 * This method is used to free the slots occupied by pending orders
	 * that haven't been confirmed within the specified range of time.
	 *
	 * @param   array  $options  An array of options to filter the records.
	 *
	 * @return  void
	 */
	public function checkExpired(array $options = [])
	{
		$db = JFactory::getDbo();

		// get any pending codes
		$pending = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'reserved' => 1, 'approved' => 0]);

		// take the removed status
		$removed = JHtml::fetch('vrehtml.status.removed', 'takeaway', 'code');

		$query = $db->getQuery(true);
		
		// take all the expired orders
		$query->select($db->qn('id'));
		$query->from($db->qn('#__vikrestaurants_takeaway_reservation'));
		$query->where($db->qn('locked_until') . ' < ' . time());

		if ($pending)
		{
			// filter by pending status
			$query->where($db->qn('status') . ' IN (' . implode(',', array_map(array($db, 'q'), $pending)) . ')');
		}

		if (!empty($options['id']))
		{
			// take only the specified orders
			$query->where($db->qn('id') . ' IN (' . implode(',', array_map('intval', (array) $options['id'])) . ')');
		}

		$db->setQuery($query);
		
		foreach ($db->loadColumn() as $ordId)
		{
			// auto-remove the order
			$this->save([
				'id'     => $ordId,
				'status' => $removed,
			]);
		}
	}

	/**
	 * Refreshes the payment charge of the specified order.
	 * 
	 * @param   int  $id         The order ID.
	 * @param   int  $paymentId  The payment ID.
	 * 
	 * @return  bool  True on success, false otherwise.
	 */
	protected function applyPaymentCharge(int $id, int $paymentId)
	{
		$order = $this->getTable();
		$order->load($id);

		// first of all, substract the payment charge from the totals
		$order->total_tax    -= $order->payment_tax;
		$order->total_to_pay -= $order->payment_tax + $order->payment_charge;

		/** @var stdClass */
		$payment = JModelVRE::getInstance('payment')->getItem($paymentId, $blank = true);

		if ($payment->charge > 0)
		{
			if ($payment->percentot == 1)
			{
				// we have a percentage amount, calculate the charge among the total bill value
				$payment->charge = $order->total_to_pay * $payment->charge / 100;
			}

			// calculate taxes for the payment charge
			$result = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($payment->id, $payment->charge, [
				'lang'    => $order->langtag,
				'id_user' => $order->id_user,
				'subject' => 'payment',
			]);

			// refresh payment NET and TAX
			$order->payment_charge = $result->net;
			$order->payment_tax    = $result->tax;

			// increase the totals by the amount specified here
			$order->total_tax    += $order->payment_tax;
			$order->total_to_pay += $order->payment_tax + $order->payment_charge;
		}
		else
		{
			// reset payment NET and TAX
			$order->payment_charge = 0;
			$order->payment_tax    = 0;
		}

		// commit changes
		$data = new stdClass;
		$data->id             = $order->id;
		$data->total_tax      = $order->total_tax;
		$data->total_to_pay   = $order->total_to_pay;
		$data->payment_tax    = $order->payment_tax;
		$data->payment_charge = $order->payment_charge;

		JFactory::getDbo()->updateObject('#__vikrestaurants_takeaway_reservation', $data, 'id');

		if ($payment->charge < 0)
		{
			if ($payment->percentot == 1)
			{
				// we have a percentage amount, calculate the charge among the total bill value
				$payment->charge = $order->total_to_pay * abs($payment->charge) / 100;
			}

			// negative charge, add to the existing discount
			return $this->addDiscount($id, [
				'value'     => abs($payment->charge) + $order->discount_val,
				'percentot' => 2, // fixed
			]);
		}

		return true;
	}

	/**
	 * Caches the current information of the specified order before applying any changes.
	 * 
	 * @param   int  $orderId  The order ID.
	 * 
	 * @return  VREOperatorLogger
	 */
	protected function observeChanges(int $orderId)
	{
		// track log if the user is an operator
		VRELoader::import('library.operator.logger');

		// instantiate logger
		$logger = VREOperatorLogger::getInstance();

		// check if the order has been already cached
		if (!$logger->getCached($orderId, 1))
		{
			// cache previous order details (1: takeaway)
			$logger->cache($orderId, 1);
		}

		return $logger;
	}
}
