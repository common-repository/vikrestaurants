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
 * VikRestaurants table reservation confirmation view model.
 *
 * @since 1.9
 */
class VikRestaurantsModelConfirmres extends JModelVRE
{
	/**
	 * Completes the booking process by saving the table reservation.
	 *
	 * @param 	array  $data  An array containing some booking options.
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
		$model = JModelVRE::getInstance('rescart');

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
			$dispatcher->trigger('onInitSaveReservation', [&$data]);
		}
		catch (Exception $e)
		{
			// error thrown during the initialization of the reservation
			$this->setError($e);
			return false;
		}

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

		////////////////////////////////////////////////////////////
		/////////////////// PREPARE BOOKING DATA ///////////////////
		////////////////////////////////////////////////////////////

		// prepare order array
		$order = [];

		// register check-in timestamp
		list($data['hour'], $data['min']) = explode(':', $data['hourmin']);
		$order['checkin_ts'] = VikRestaurants::createTimestamp($data['date'], $data['hour'], $data['min']);

		// register number of participants
		$order['people'] = $data['people'];

		// register current language tag
		$order['langtag'] = JFactory::getLanguage()->getTag();

		// calculate total deposit to leave
		$order['deposit'] = $model->getTotalDeposit($data);

		// use the default status set in configuration
		$order['status'] = $config->get('defstatus');

		// reset bill totals
		$order['total_net']  = 0;
		$order['total_tax']  = 0;
		$order['bill_value'] = 0;

		////////////////////////////////////////////////////////////
		////////////////////// VALIDATE TABLES /////////////////////
		////////////////////////////////////////////////////////////

		// recover family flag from user state
		$family = JFactory::getApplication()->getUserState('vre.search.family', false);
		
		/**
		 * Look for COVID19 prevention measures.
		 *
		 * @since 1.8
		 *
		 * @see COVID-19
		 */
		$people = VikRestaurants::getPeopleSafeDistance($data['people'], $family);

		// create availability search object
		$search = new VREAvailabilitySearch($data['date'], $data['hourmin'], $people);

		// make sure the table is available
		$available = $search->isTableAvailable($data['table'], null, $cluster);

		if (!$available)
		{
			// get details of selected table
			$tmp = $search->getTable($data['table']);

			// unset selected table
			$data['table'] = null;

			// get all available tables
			$tables = $search->getAvailableTables();

			/**
			 * The table is no more available. In case the selection
			 * of the table is not allowed, we should automatically search
			 * for a different available table, so that the customer
			 * won't have to restart with the booking process.
			 */
			if ($config->getUint('reservationreq') == 1)
			{
				// rooms selection allowed, search for a table available
				// for the room selected during the booking process
				$id_room = $tmp ? $tmp->id_room : 0;

				for ($i = 0; $i < count($tables) && !$data['table']; $i++)
				{
					if ($tables[$i]->id_room == $id_room)
					{
						// table found
						$data['table'] = $tables[$i]->id;
					}
				}
			}
			else if ($config->getUint('reservationreq') == 2)
			{
				// no table/rooms selection, search for the first available table
				if (count($tables))
				{
					// get first available table
					$data['table'] = $tables[0]->id;
				}
			}

			if ($data['table'])
			{
				// make sure again the table is available and
				// retrieve a cluster of assigned tables, if any
				$available = $search->isTableAvailable($data['table'], null, $cluster);

				if (!$available)
				{
					// something went wrong, unset table
					$data['table'] = null;
				}
			}
			
			// make sure the table found is now available
			if (!$data['table'])
			{
				// no available tables for the selected date and time
				$this->setError(JText::translate('VRERRTABNOLONGAV'));
				return false;
			}
		}

		if ($cluster)
		{
			// join selected table with cluster
			$order['id_table'] = array_merge([$data['table']], $cluster);
		}
		else
		{
			// booked only a single table
			$order['id_table'] = $data['table'];
		}

		////////////////////////////////////////////////////////////
		//////////////////// FETCH BOOKED MENUS ////////////////////
		////////////////////////////////////////////////////////////

		// fetch the menus booked by the customer
		$menus = $model->getMenus();

		if ($menus)
		{
			$order['menus'] = [];

			foreach ($menus as $menu)
			{
				// multiply the cost by the number of selected units or, in case there is no
				// freedom of choice, by the number of participants
				$menuCost = $menu->cost * ($menu->freechoose ? $menu->units : $order['people']);

				// calculate taxes
				$menuCost = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($menu->id, $menuCost, [
					'subject' => 'restaurant.menu',
				]);

				// increase bill totals
				$order['total_net']  += $menuCost->net;
				$order['total_tax']  += $menuCost->tax;
				$order['bill_value'] += $menuCost->gross;

				// attach menu to reservation
				$order['menus'][$menu->id] = $menu->units;
			}
		}

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
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter);

		// create requestor for the restaurant custom fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor($customFields);

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
		 * @param   array   &$order  The reservation details.
		 * @param   array   &$args   The billing array.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onPrepareFieldsSaveReservation', [&$order, &$fieldsData]);

		// register data fetched by the custom fields so that the reservation
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
		
		if ($order['deposit'] > 0)
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
				->filter(new E4J\VikRestaurants\Payment\Filters\RestaurantGroupFilter)
				->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter)
				->filter(new E4J\VikRestaurants\Payment\Filters\TotalCostFilter($order['deposit']))
				->filter(new E4J\VikRestaurants\Payment\Filters\TrustedCustomerFilter('restaurant'))
				->filter(new E4J\VikRestaurants\Payment\Filters\PluginAvailabilityFilter('restaurant'));

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
					// apply payment charge to total deposit
					if ($payment->percentot == 1)
					{
						// percentage charge based on total deposit
						$charge = $order['deposit'] * (float) $payment->charge / 100;
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

					// increase deposit
					$order['deposit'] += $charge->gross;

					// increase the bill totals too, because this charge should be an extra
					$order['total_tax']  += $charge->tax;
					$order['bill_value'] += $charge->gross;
				}

				// auto-confirm reservation according to the configuration of
				// the payment, otherwise force PENDING status to let the
				// customers be able to start a transaction
				if ($payment->setconfirmed)
				{
					// auto-confirm order
					$order['status'] = JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code');
				}
				else
				{
					// leave it pending
					$order['status'] = JHtml::fetch('vrehtml.status.pending', 'restaurant', 'code');
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
				'id'     => 0,
				'jid'    => $user->guest ? 0 : $user->id,
				'fields' => $order['custom_f'],
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
				// assign reservation to saved customer
				$order['id_user'] = $id_user;
			}
		}

		////////////////////////////////////////////////////////////
		///////////////////// SAVE RESERVATION /////////////////////
		////////////////////////////////////////////////////////////

		$reservationModel = JModelVRE::getInstance('reservation');

		// save the reservation
		if (!$reservationModel->save($order))
		{
			// propagate error
			$this->setError($reservationModel->getError());
			return false;	
		}

		// get reservation saved data
		$resData = $reservationModel->getData();
		// register the reservation data for being retrieved from the outside
		$this->set('data', $resData);

		$ordnum = $resData['id'];
		$ordkey = $resData['sid'];

		////////////////////////////////////////////////////////////
		////////////////////// NOTIFICATIONS ///////////////////////
		////////////////////////////////////////////////////////////

		$mailOptions = [];
		// validate e-mail rules before sending
		$mailOptions['check'] = true;

		// send e-mail notification to the customer
		$reservationModel->sendEmailNotification($ordnum, $mailOptions);

		// send e-mail notification to the administrators and operators
		$mailOptions['client'] = 'admin';
		$reservationModel->sendEmailNotification($ordnum, $mailOptions);
		
		if (JHtml::fetch('vrehtml.status.isapproved', 'restaurant', $resData['status']))
		{
			// try to send SMS notifications, only for approved statuses (0: restaurant)
			VikRestaurants::sendSmsAction($resData['purchaser_phone'], $ordnum, 0);
		}

		$redirect_url = "index.php?option=com_vikrestaurants&view=reservation&ordnum={$ordnum}&ordkey={$ordkey}";

		if (!empty($data['itemid']))
		{
			$redirect_url .= "&Itemid={$data['itemid']}";
		}

		/**
		 * Trigger event to manipulate the redirect URL after completing
		 * the table booking process.
		 *
		 * Use VREOrderFactory::getReservation($ordnum) to access the order details.
		 *
		 * @param   string  &$url   The redirect URL (plain).
		 * @param   int     $order  The reservation id.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onRedirectReservation', [&$redirect_url, $ordnum]);
		
		// rewrite landing page
		return JRoute::rewrite($redirect_url, false);
	}
}
