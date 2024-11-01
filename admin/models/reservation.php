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
 * VikRestaurants restaurant reservation model.
 *
 * @since 1.9
 */
class VikRestaurantsModelReservation extends JModelVRE
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
		$reservation = parent::getItem($pk, $new);

		if (!$reservation)
		{
			return null;
		}

		$db = JFactory::getDbo();

		$config = VREFactory::getConfig();

		// decode registered custom fields
		$reservation->custom_f = $reservation->custom_f ? json_decode($reservation->custom_f, true) : [];

		$reservation->tables = [];
		$reservation->menus  = [];

		if ($reservation->id)
		{
			// convert check-in date and time from timestamp
			$reservation->date    = date($config->get('dateformat'), $reservation->checkin_ts);
			$reservation->hourmin = date('H:i', $reservation->checkin_ts);

			// recover all the clustered tables
			$query = $db->getQuery(true)
				->select($db->qn(['ti.id', 'ti.name', 'ti.id_room']))
				->from($db->qn('#__vikrestaurants_reservation', 'ri'))
				->join('INNER', $db->qn('#__vikrestaurants_table', 'ti') . ' ON ' . $db->qn('ri.id_table') . ' = ' . $db->qn('ti.id'))
				->where([
					$db->qn('ri.id') . ' = ' . (int) $reservation->id,
					$db->qn('ri.id_parent') . ' = ' . (int) $reservation->id,
				], 'OR');

			$db->setQuery($query);
			$reservation->tables = $db->loadObjectList();

			// recover all the selected menus
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__vikrestaurants_res_menus_assoc', 'a'))
				->where($db->qn('id_reservation') . ' = ' . (int) $reservation->id);

			$db->setQuery($query);
			
			foreach ($db->loadObjectList() as $menu)
			{
				$reservation->menus[$menu->id_menu] = $menu;
			}
		}
		else
		{
			// create default date and time
			$reservation->date    = date($config->get('dateformat'), VikRestaurants::now());
			$reservation->hourmin = VikRestaurants::getClosestTime($reservation->date, true);

			if (is_numeric($reservation->date))
			{
				$reservation->date = date($config->get('dateformat'), $reservation->date);
			}

			// set number of people equals to the minimum accepted value (do not go under 2)
			$reservation->people = max(2, $config->getUint('minimumpeople'));
		}

		return $reservation;
	}

	/**
	 * Recovers the list of items assigned to the bill of the specified reservation.
	 * 
	 * @param   int       $id  The reservation ID.
	 * 
	 * @return  object[]  A list of items.
	 */
	public function getBillItems(int $id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('`i`.*');
		$query->from($db->qn('#__vikrestaurants_res_prod_assoc', 'i'));

		$query->select($db->qn('p.name', 'product_name'));
		$query->leftjoin($db->qn('#__vikrestaurants_section_product', 'p') . ' ON ' . $db->qn('p.id') . ' = ' . $db->qn('i.id_product'));

		$query->select($db->qn('o.name', 'option_name'));
		$query->leftjoin($db->qn('#__vikrestaurants_section_product_option', 'o') . ' ON ' . $db->qn('o.id') . ' = ' . $db->qn('i.id_product_option'));

		$query->where($db->qn('i.id_reservation') . ' = ' . $id);
		
		$query->order($db->qn('i.servingnumber') . ' ASC');
		$query->order($db->qn('i.id') . ' ASC');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $item)
		{
			if (!$item->product_name)
			{
				// product name not found, try to extract it from the saved name
				$chunks = explode(' - ', $item->name);

				$item->product_name = (string) array_shift($chunks);
				$item->option_name  = (string) array_pop($chunks);

				if ($chunks)
				{
					$item->product_name = implode(' - ', array_merge([$item->product_name], $chunks));
				}
			}

			// always use the default product name
			$item->name = $item->product_name;
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

		// create a cache of the reservation before applying the changes
		$changesLogger = $this->observeChanges($data['id'] ?? 0);

		if (!empty($data['id']))
		{
			// register current datetime as modified date, if not specified
			if (!isset($data['modifiedon']))
			{
				$data['modifiedon'] = VikRestaurants::now();
			}

			// load reservation details before saving them
			$previousReservation = $this->getTable();
			$previousReservation->load($data['id']);
			$previousReservation = (object) $previousReservation->getProperties();
		}
		else
		{
			// create empty reservation for a better ease of use
			$previousReservation = (object) $this->getTable()->getProperties();
		}

		$tables = null;

		if (isset($data['id_table']))
		{
			if (is_string($data['id_table']) && !is_numeric($data['id_table']))
			{
				// probably a JSON string was provided, convert it to an array of integers
				$tables = (array) json_decode($data['id_table']);
				$data['id_table'] = array_shift($tables);
			}
			else if (is_array($data['id_table']))
			{
				// an array of tables have been provided
				$tables = array_map('intval', $data['id_table']);
				$data['id_table'] = array_shift($tables);
			}
			
		}

		if (empty($data['id']) && empty($data['status']))
		{
			// status not specified, use the default confirmed one
			$data['status'] = JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code');
		}

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		$savedData = $this->getData();

		if (isset($data['menus']))
		{
			// update the booked menus
			$this->updateBookedMenus($id, $data['menus']);
		}

		if (isset($data['items']))
		{
			$model = JModelVRE::getInstance('resprod');

			// iterate all the provided items
			foreach ($data['items'] as $i => $item)
			{
				if (is_string($item))
				{
					// JSON given, decode it
					$item = json_decode($item, true);
				}

				// attach item to this reservation
				$item['id_reservation'] = $id;

				// save item
				$model->save($item);
			}
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

		if (isset($data['id_payment']) && $data['id_payment'] != $previousReservation->id_payment)
		{
			// payment changed, look for a new charge to apply
			$this->applyPaymentCharge($id, $data['id_payment']);
		}

		if (isset($tables))
		{
			// update also the clustered reservations
			$this->updateTablesCluster($savedData, $tables);
		}

		// in case of update, propagate changes to all the clustered reservations
		if (!empty($data['id']))
		{
			// refresh all the details of the reservation
			$reservationTable = $this->getTable();
			$reservationTable->load($data['id']);
			$tmp = (object) $reservationTable->getProperties();

			// replace the parent ID with the ID of the updated reservation
			$tmp->id_parent = $data['id'];

			// do not affect the only columns that do not depend on the parent
			unset($tmp->id);
			unset($tmp->id_table);

			// update by using the parent ID as primary key
			JFactory::getDbo()->updateObject('#__vikrestaurants_reservation', $tmp, 'id_parent');
		}

		if (!empty($data['deleted_items']))
		{
			// Delete the items here in order to properly apply all the changes before
			// sending the e-email and checking any differences with the previous record
			JModelVRE::getInstance('resprod')->delete($data['deleted_items']);
		}

		// generate changes log (0: restaurant)
		$changesLogger->generate($id, 0);

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

		// check whether the status of the reservation has been changed
		if (!empty($data['status']) && $data['status'] !== $previousReservation->status)
		{
			/**
			 * Trigger event to let the plugins be notified every time the status of the reservations change.
			 *
			 * @param   array  $data  The saved record.
			 *
			 * @return  void
			 *
			 * @since   1.9
			 */
			$dispatcher->trigger('onStatusChangeRestaurantReservation', [$data]);
		}

		/**
		 * Trigger event to allow the plugins to make something after saving
		 * a reservation into the database. Fires once all the details of
		 * the reservation has been saved.
		 *
		 * @param   array   $data   The saved record.
		 * @param   bool    $isNew  True if the record was inserted.
		 * @param   JModel  $model  The model instance.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onAfterSaveReservationLate', [$data, $isNew, $this]);

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
	 * @param   int    $id    The reservation ID.
	 * @param   mixed  $code  Either an array/object containing the code details
	 *                        or the primary key of the code.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function changeCode(int $id, $code)
	{
		if ((int) $id <= 0)
		{
			$this->setError(new Exception('Missing reservation ID', 400));
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

		// save reservation first
		$saved = $this->save([
			'id'          => (int) $id,
			'rescode'     => $code['id'],
			'id_operator' => $code['id_operator'] ?? null,
		]);

		if (!$saved)
		{
			// unable to save the reservation
			return false;
		}

		if ($code['id'])
		{
			/** @var JModelLegacy */
			$resCodeModel = JModelVRE::getInstance('rescodeorder');

			// try to update the history of the reservation
			$saved = $resCodeModel->save([
				'group'      => 1,
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

		// load any assigned menus
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_res_menus_assoc'))
			->where($db->qn('id_reservation') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($menus = $db->loadColumn())
		{
			// delete all the menus that belong to the removed reservations
			JModelVRE::getInstance('resmenu')->delete($menus);
		}

		// load any assigned products
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_res_prod_assoc'))
			->where($db->qn('id_reservation') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($products = $db->loadColumn())
		{
			// delete all the products that belong to the removed reservations
			JModelVRE::getInstance('resprod')->delete($products);
		}

		// load any assigned order status
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_order_status'))
			->where($db->qn('group') . ' = 1')
			->where($db->qn('id_order') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($statuses = $db->loadColumn())
		{
			// delete all the statuses that belong to the removed reservations
			JModelVRE::getInstance('rescodeorder')->delete($statuses);
		}

		// load any clustered reservation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_reservation'))
			->where($db->qn('id_parent') . ' IN (' . implode(',', $ids) . ')');

		$db->setQuery($q);

		if ($children = $db->loadColumn())
		{
			// delete all the clustered reservations that belong to the removed ones
			$this->delete($children);
		}

		return true;
	}

	/**
	 * Sends an e-mail notification to the customer of the
	 * specified reservation.
	 *
	 * @param   int    $id       The reservation ID.
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
			$mailTemplate = E4J\VikRestaurants\Mail\MailFactory::getTemplate('restaurant', $client, $id, $options);

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
	 * Sends a SMS notification to the customer of the
	 * specified reservation.
	 *
	 * @param 	integer  $id  The reservation ID.
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
			// load reservation details
			$order = VREOrderFactory::getReservation($id);
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
		$text = VikRestaurants::getSmsCustomerTextMessage($order, $group = 0);

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
	 * Adds a discount to the specified reservation.
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
			->from($db->qn('#__vikrestaurants_res_prod_assoc'))
			->where($db->qn('id_reservation') . ' = ' . (int) $id)
			->where($db->qn('price') . ' > 0');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		// load reservation details
		$reservation = $this->getTable();
		$reservation->load((int) $id);

		// define options for tax calculation
		$options = [
			'subject' => 'restaurant.menusproduct',
			'lang'    => $reservation->langtag,
			'id_user' => $reservation->id_user,
		];

		$total_c = 0;

		// calculate total cost
		foreach ($items as $item)
		{
			$total_c += (float) $item->price;
		}

		// prepare order data
		$orderData = [
			'id'           => $reservation->id,
			'bill_value'   => $reservation->payment_charge + $reservation->payment_tax + $reservation->tip_amount,
			'total_net'    => 0,
			'total_tax'    => $reservation->payment_tax,
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
			$orderData['total_net']  += $itemData['net'];
			$orderData['total_tax']  += $itemData['tax'];
			$orderData['bill_value'] += $itemData['gross'];

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
	 * Removes discount from the specified reservation.
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
	 * Adds a tip to the specified reservation.
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

		$reservation = $this->getTable();
		$reservation->load($id);

		// subtract the previous tip amount (if any) from the bill value
		$reservation->bill_value -= $reservation->tip_amount;

		if (empty($tip['percentot']) || $tip['percentot'] == 2)
		{
			// we have a fixed amount
			$reservation->tip_amount = abs($tip['value']);
		}
		else
		{
			// we have a percentage amount, calculate it above the grand total
			$reservation->tip_amount = $reservation->bill_value * abs($tip['value']) / 100;
		}

		// add new tip to the bill total
		$reservation->bill_value += $reservation->tip_amount;

		// commit changes
		$data = new stdClass;
		$data->id         = $reservation->id;
		$data->bill_value = $reservation->bill_value;
		$data->tip_amount = $reservation->tip_amount;

		return JFactory::getDbo()->updateObject('#__vikrestaurants_reservation', $data, 'id');
	}

	/**
	 * Removes the previous tip from the specified reservation.
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
	 * Updates the status of all the reservations out of time to REMOVED.
	 * This method is used to free the slots occupied by pending reservations
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
		$pending = JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'reserved' => 1, 'approved' => 0]);

		// take the removed status
		$removed = JHtml::fetch('vrehtml.status.removed', 'restaurant', 'code');

		$query = $db->getQuery(true);
		
		// take all the expired reservations
		$query->select($db->qn('id'));
		$query->from($db->qn('#__vikrestaurants_reservation'));
		$query->where($db->qn('id_parent') . ' <= 0');
		$query->where($db->qn('locked_until') . ' < ' . time());

		if ($pending)
		{
			// filter by pending status
			$query->where($db->qn('status') . ' IN (' . implode(',', array_map(array($db, 'q'), $pending)) . ')');
		}

		if (!empty($options['id']))
		{
			// take only the specified reservations
			$query->where($db->qn('id') . ' IN (' . implode(',', array_map('intval', (array) $options['id'])) . ')');
		}

		$db->setQuery($query);
		
		foreach ($db->loadColumn() as $resId)
		{
			// auto-remove the reservation
			$this->save([
				'id'     => $resId,
				'status' => $removed,
			]);

			/**
			 * @todo Consider to send an e-mail notification to the customer.
			 *       Passing `check` = `true` to the options array of the
			 *       sendEmailNotification method would check whether the
			 *       email should be actually sent. This way, in case the
			 *       customers don't need to observe the "removed" status,
			 *       the notifications will be ignored.
			 */
		}
	}

	/**
	 * Resets the pin of the provided reservation.
	 * In case the number of failed attempts is lower than 3, only the number of failed
	 * attempts will be reset.
	 * 
	 * @param   int|object  $reservation  Either a reservation ID or a reservation object.
	 * 
	 * @return  mixed  The reservation pin code on success, false otherwise.
	 */
	public function resetPin($reservation)
	{
		if (!is_object($reservation))
		{
			// fetch reservation details from the provided ID
			$reservation = $this->getItem((int) $reservation);
		}

		if (!$reservation)
		{
			// reservation not found
			$this->setError(new Exception(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 404));
			return false;
		}

		if ($reservation->pinattempts == 0)
		{
			// no failed attempts, save an update process
			return $reservation->pin;
		}

		if ($reservation->pinattempts >= 3)
		{
			// reset pin only in case the number of attempts is equals or higher than 3
			$reservation->pin = VikRestaurants::generateSerialCode(4, 'reservation-pin', '0123456789');
		}

		// create a new object to update only the needed columns
		$data              = new stdClass;
		$data->id          = $reservation->id;
		$data->pin         = $reservation->pin;
		$data->pinattempts = 0;

		// update record without using the model as we don't want to trigger an event change
		// whenever the system resets the pin code of a reservation
		\JFactory::getDbo()->updateObject('#__vikrestaurants_reservation', $data, 'id');

		// return the new pin code (or the same one if not changed)
		return $reservation->pin;
	}

	/**
	 * Increases the number of failed attempts for the provided reservation.
	 * 
	 * @param   int|object  $reservation  Either a reservation ID or a reservation object.
	 * 
	 * @return  void
	 */
	public function wrongPin($reservation)
	{
		if (!is_object($reservation))
		{
			// fetch reservation details from the provided ID
			$reservation = $this->getItem((int) $reservation, $blank = true);
		}

		// increase pin attempts by one
		$reservation->pinattempts++;

		// create a new object to update only the needed columns
		$data              = new stdClass;
		$data->id          = $reservation->id;
		$data->pinattempts = $reservation->pinattempts; 

		// update record without using the model as we don't want to trigger an event change
		// whenever a user fails to enter the pin code
		\JFactory::getDbo()->updateObject('#__vikrestaurants_reservation', $data, 'id');
	}

	/**
	 * Updates the menus assigned to the specified reservation.
	 * 
	 * @param   int    $reservationId  The ID of the reservation to update.
	 * @param   array  $menus          An associative array containing the ID of the menu (key)
	 *                                 and the number of booked units (value).
	 * 
	 * @return  void
	 */
	protected function updateBookedMenus(int $reservationId, array $menus)
	{
		// exclude the menus with quantity equals or lower than 0
		$menus = array_filter($menus, function($quantity)
		{
			return $quantity > 0;
		});

		// update the menus assigned to the reservation ID
		JModelVRE::getInstance('resmenu')->setRelation($reservationId, array_map('intval', array_keys($menus)));

		if ($menus)
		{
			$db = JFactory::getDbo();

			// iterate all the menus
			foreach ($menus as $menuId => $units)
			{
				$assoc = new stdClass;
				$assoc->id_reservation = $reservationId;
				$assoc->id_menu        = (int) $menuId;
				$assoc->quantity       = (int) $units;

				// update the number of selected units per each menu
				$db->updateObject('#__vikrestaurants_res_menus_assoc', $assoc, ['id_reservation', 'id_menu']);
			}
		}
	}

	/**
	 * Updates a cluster of tables.
	 * 
	 * @param   array  $data    The changes made for the parent reservation.
	 * @param   array  $tables  An array of remaining tables.
	 * 
	 * @return  void
	 */
	protected function updateTablesCluster(array $data, array $tables)
	{
		$id_parent = (int) $data['id'];

		$lookup = [];

		// create tables lookup for a better ease of use
		foreach ($tables as $table)
		{
			$lookup[$table] = (int) $table;
		}

		$tables = $lookup;

		$data = (object) $data;

		$db = JFactory::getDbo();

		// recover all the clustered tables
		$query = $db->getQuery(true)
			->select($db->qn(['id', 'id_table']))
			->from($db->qn('#__vikrestaurants_reservation'))
			->where($db->qn('id_parent') . ' = ' . $id_parent);

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $reservation)
		{
			if ($tables)
			{
				$data->id = $reservation->id;

				if (isset($tables[$reservation->id_table]))
				{
					$data->id_table = $reservation->id_table;
				}
				else
				{
					// use the next available one
					$data->id_table = reset($tables);
				}

				// update changes
				$db->updateObject('#__vikrestaurants_reservation', $data, 'id');

				// remove table from lookup
				unset($tables[$data->id_table]);
			}
			else
			{
				// there are no more table, therefore we should delete the remaining ones
				$this->delete($reservation->id);
			}
		}

		// iterate the remaining tables, if any
		foreach ($tables as $id_table)
		{
			$data->id        = 0;
			$data->id_table  = (int) $id_table;
			$data->id_parent = (int) $id_parent;

			// register a new reservation and assign it to the correct parent
			$db->insertObject('#__vikrestaurants_reservation', $data, 'id');
		}
	}

	/**
	 * Refreshes the payment charge of the specified reservation.
	 * 
	 * @param   int  $id         The reservation ID.
	 * @param   int  $paymentId  The payment ID.
	 * 
	 * @return  bool  True on success, false otherwise.
	 */
	protected function applyPaymentCharge(int $id, int $paymentId)
	{
		$reservation = $this->getTable();
		$reservation->load($id);

		// first of all, substract the payment charge from the totals
		$reservation->total_tax  -= $reservation->payment_tax;
		$reservation->bill_value -= $reservation->payment_tax + $reservation->payment_charge;

		/** @var stdClass */
		$payment = JModelVRE::getInstance('payment')->getItem($paymentId, $blank = true);

		if ($payment->charge > 0)
		{
			if ($payment->percentot == 1)
			{
				// we have a percentage amount, calculate the charge among the total bill value
				$payment->charge = $reservation->bill_value * $payment->charge / 100;
			}

			// calculate taxes for the payment charge
			$result = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($payment->id, $payment->charge, [
				'lang'    => $reservation->langtag,
				'id_user' => $reservation->id_user,
				'subject' => 'payment',
			]);

			// refresh payment NET and TAX
			$reservation->payment_charge = $result->net;
			$reservation->payment_tax    = $result->tax;

			// increase the totals by the amount specified here
			$reservation->total_tax  += $reservation->payment_tax;
			$reservation->bill_value += $reservation->payment_tax + $reservation->payment_charge;
		}
		else
		{
			// reset payment NET and TAX
			$reservation->payment_charge = 0;
			$reservation->payment_tax    = 0;
		}

		// commit changes
		$data = new stdClass;
		$data->id             = $reservation->id;
		$data->total_tax      = $reservation->total_tax;
		$data->bill_value     = $reservation->bill_value;
		$data->payment_tax    = $reservation->payment_tax;
		$data->payment_charge = $reservation->payment_charge;

		JFactory::getDbo()->updateObject('#__vikrestaurants_reservation', $data, 'id');

		if ($payment->charge < 0)
		{
			if ($payment->percentot == 1)
			{
				// we have a percentage amount, calculate the charge among the total bill value
				$payment->charge = $reservation->bill_value * abs($payment->charge) / 100;
			}

			// negative charge, add to the existing discount
			return $this->addDiscount($id, [
				'value'     => abs($payment->charge) + $reservation->discount_val,
				'percentot' => 2, // fixed
			]);
		}

		return true;
	}

	/**
	 * Caches the current information of the specified order before applying any changes.
	 * 
	 * @param   int  $orderId  The reservation ID.
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
		if (!$logger->getCached($orderId, 0))
		{
			// cache previous order details (0: restaurant)
			$logger->cache($orderId, 0);
		}
		else
		{
			// preload the tables
			$logger->getCached($orderId, 0)->tables;
		}

		return $logger;
	}
}
