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

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants restaurant reservation controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerReservation extends VREControllerAdmin
{
	/**
	 * This is the end-point used by the gateway to validate a payment transaction.
	 * It is mandatory to send the following parameters (via GET or POST) in order to
	 * retrieve the correct details of the order transaction.
	 *
	 * @param   int     ordnum  The order number (ID).
	 * @param   string 	ordkey  The order key (SID).
	 *
	 * @return  void
	 */
	public function notifypayment()
	{
		$app    = JFactory::getApplication();
		$config = VREFactory::getConfig();

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = VREFactory::getPlatform()->getDispatcher();
			
		$oid = $app->input->getUint('ordnum', 0);
		$sid = $app->input->getAlnum('ordkey', '');

		// Get reservation details (filter by ID and SID).
		// In case the reservation doesn't exist, an
		// exception will be thrown.
		$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

		/**
		 * Added support for online bill payment.
		 * Temporarily revert status to PENDING to allow
		 * payments in case the bill is closed and the
		 * remaining balance is higher than 0.
		 *
		 * @since 1.8.1
		 */
		if ($reservation->bill_closed && $reservation->bill_value - $reservation->tot_paid)
		{
			$reservation->statusRole = 'PENDING';
		}

		/**
		 * This event is triggered every time a payment tries to validate a transaction made.
		 *
		 * DOES NOT trigger in case the reservation doesn't exist.
		 *
		 * @param   mixed  $reservation  The details of the restaurant reservation.
		 *
		 * @return  void
		 *
		 * @since   1.8.1
		 */
		$dispatcher->trigger('onReceivePaymentNotification', [$reservation]);

		// build return and error URL
		$return_url = "index.php?option=com_vikrestaurants&view=reservation&ordnum={$oid}&ordkey={$sid}";
		$error_url  = "index.php?option=com_vikrestaurants&view=reservation&ordnum={$oid}&ordkey={$sid}";

		/**
		 * If we are trying to validate an order already paid/confirmed, auto-redirect to
		 * the return URL instead of throwing an exception.
		 * 
		 * @since 1.9
		 */
		if ($reservation->statusRole == 'APPROVED')
		{
			$this->setRedirect(JRoute::rewrite($return_url, false));
			return;
		}
		
		/**
		 * Allow the payment for REMOVED reservations because they
		 * have been probably paid while they were PENDING.
		 * 
		 * @since 1.8
		 */
		$accepted = [
			'PENDING',
			'EXPIRED',
		];
		
		// make sure the order can be paid
		if (!in_array($reservation->statusRole, $accepted))
		{
			// status not allowed
			throw new Exception('The current status of the reservation does not allow any payments.', 403);
		}

		if (!$reservation->payment)
		{
			// payment method not found
			throw new Exception('The selected payment does not exist', 404);
		}

		// reload payment details to access the parameters
		$payment = JModelVRE::getInstance('payment')->getItem($reservation->payment->id);

		/** @var E4J\VikRestaurants\Platform\Uri\UriInterface */
		$uri = VREFactory::getPlatform()->getUri();
			
		// fetch transaction data	
		$paymentData = [];

		/**
		 * The payment URLs are correctly routed for external usage.
		 *
		 * @since 1.8
		 */
		$return_url = $uri->route($return_url, false);
		$error_url  = $uri->route($error_url, false);

		/**
		 * Include the Notification URL in both the PLAIN and ROUTED formats.
		 *
		 * @since 1.8.1
		 */
		$notify_url = "index.php?option=com_vikrestaurants&task=reservation.notifypayment&ordnum={$oid}&ordkey={$sid}";

		/**
		 * Calculate total amount to pay.
		 *
		 * @since 1.8.1
		 * @since 1.9   Do not check whether the bill has been closed because fixed menu
		 *              includes the total to pay within the bill value.
		 */
		if ($reservation->bill_value > 0)
		{
			// pay remaining balance after ordering
			$total_to_pay = $reservation->bill_value;
		}
		else
		{
			// leave a deposit online
			$total_to_pay = $reservation->deposit;
		}

		// subtract amount already paid
		$total_to_pay = (float) max(0, $total_to_pay - $reservation->tot_paid);
	
		$paymentData['type']                 = 'restaurant.validate';
		$paymentData['oid']                  = $reservation->id;
		$paymentData['sid']                  = $reservation->sid;
		$paymentData['tid']                  = 0;
		$paymentData['transaction_name']     = JText::sprintf('VRRESTRANSACTIONNAME', $config->get('restname'));
		$paymentData['transaction_currency'] = $config->get('currencyname');
		$paymentData['currency_symb']        = $config->get('currencysymb');
		$paymentData['tax']                  = 0;
		$paymentData['return_url']           = $return_url;
		$paymentData['error_url']            = $error_url;
		$paymentData['notify_url']           = $uri->route($notify_url, false);
		$paymentData['notify_url_plain']     = JUri::root() . $notify_url;
		$paymentData['total_to_pay']         = $total_to_pay;
		$paymentData['total_net_price']      = $total_to_pay;
		$paymentData['total_tax']            = 0;
		$paymentData['shipping']             = 0;
		$paymentData['payment_info']         = $payment;
		$paymentData['details'] = [
			'purchaser_nominative' => $reservation->purchaser_nominative,
			'purchaser_mail'       => $reservation->purchaser_mail,
			'purchaser_phone'      => $reservation->purchaser_phone,
		];

		/**
		 * Added support for customer billing details.
		 *
		 * @since 1.9
		 */
		$paymentData['billing'] = $reservation->billing;

		/**
		 * Trigger event to manipulate the payment details.
		 *
		 * @param 	array 	&$order   The transaction details.
		 * @param 	mixed 	&$params  The payment configuration as array or JSON.
		 *
		 * @return 	void
		 *
		 * @since 	1.8.1
		 */
		$dispatcher->trigger('onInitPaymentTransaction', [&$paymentData, &$payment->params]);

		/**
		 * Instantiate the payment using the platform handler.
		 *
		 * @since 1.8
		 */
		$obj = VREApplication::getInstance()->getPaymentInstance($payment->file, $paymentData, $payment->params);
		
		try
		{
			// validate payment transaction
			$result = $obj->validatePayment();
		}
		catch (Exception $e)
		{
			// catch any exceptions that might have been thrown by the gateway
			$result = [];
			$result['verified'] = 0;
			$result['log']      = $e->getMessage();
		}

		$model = $this->getModel();
		
		// successful response
		if ($result['verified'])
		{
			$reservation->statusRole = 'APPROVED';

			if (!empty($result['tot_paid']))
			{
				// increase total amount paid
				$reservation->tot_paid += (float) $result['tot_paid'];
			}

			if ($reservation->bill_value > 0 && $reservation->tot_paid >= $reservation->bill_value)
			{
				// the whole amount has been paid, use the apposite PAID status
				$reservation->status = JHtml::fetch('vrehtml.status.paid', 'restaurant', 'code');
				$reservation->paid   = 1;
			}
			else
			{
				// a deposit have been left, use CONFIRMED status
				$reservation->status = JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code');
				$reservation->paid   = 0;
			}

			// prepare data to dave
			$data = [
				'id'       => $reservation->id,
				'status'   => $reservation->status,
				'tot_paid' => $reservation->tot_paid,
			];

			$model->save($data);

			/**
			 * Auto-set "Leave" reservation code when the customer
			 * pays the remaining balance after closing the bill.
			 *
			 * @since 1.8.1
			 */
			if ($reservation->bill_closed)
			{
				// get reservation code used to "leave" the table
				$leaveCodeId = JHtml::fetch('vikrestaurants.rescoderule', 'leave', 1);

				if ($leaveCodeId)
				{
					// update reservation status code
					$model->changeCode($reservation->id, $leaveCodeId);
				}
			}
			
			////////////////////////////////////////////////////////////
			////////////////////// NOTIFICATIONS ///////////////////////
			////////////////////////////////////////////////////////////

			$mailOptions = [];
			// validate e-mail rules before sending
			$mailOptions['check'] = true;

			// send e-mail notification to the customer
			$model->sendEmailNotification($reservation->id, $mailOptions);

			// send e-mail notification to the administrators and operators
			$mailOptions['client'] = 'admin';
			$model->sendEmailNotification($reservation->id, $mailOptions);
			
			// try to send SMS notifications (0: restaurant)
			VikRestaurants::sendSmsAction($reservation->purchaser_phone, $reservation->id, 0);

			/**
			 * Trigger event after the validation of a successful transaction.
			 *
			 * @param   array  $order  The transaction details.
			 * @param   array  $args   The response array.
			 *
			 * @return  void
			 *
			 * @since   1.8.1
			 */
			$dispatcher->trigger('onSuccessPaymentTransaction', [$paymentData, $result]);
		}
		// failure response
		else
		{
			// check if the payment registered any logs
			if (!empty($result['log']))
			{
				$text = array(
					'Order #' . $reservation->id . '-' . $reservation->sid . ' (Restaurant)',
					nl2br($result['log']),
				);

				// send error logs to administrator(s)
				VikRestaurants::sendAdminMailPaymentFailed($reservation->id, $text);

				// get current date and time
				$timeformat = preg_replace("/:i/", ':i:s', $config->get('timeformat'));
				$now = JHtml::fetch('date', 'now', $config->get('dateformat') . ' ' . $timeformat, $app->get('offset', 'UTC'));

				// build log string
				$log  = str_repeat('-', strlen($now) + 4) . "\n";
				$log .= "| $now |\n";
				$log .= str_repeat('-', strlen($now) + 4) . "\n";
				$log .= "\n" . $result['log'];

				if (!empty($reservation->payment_log))
				{
					// always prepend new logs at the beginning
					$log = $log . "\n\n" . $reservation->payment_log;
				}

				// prepare save data
				$data = [
					'id'          => $reservation->id,
					'payment_log' => $log,
				];

				// update order logs
				$model->save($data);
			}

			/**
			 * Trigger event after the validation of a failed transaction.
			 *
			 * @param   array  $order  The transaction details.
			 * @param   array  $args   The response array.
			 *
			 * @return  void
			 *
			 * @since   1.8.1
			 */
			$dispatcher->trigger('onFailPaymentTransaction', [$paymentData, $result]);
		}

		// check whether the payment instance supports a method
		// to be executed after the validation
		if (method_exists($obj, 'afterValidation'))
		{
			$obj->afterValidation($result['verified'] ? 1 : 0);
		}
	}

	/**
	 * Approves the specified reservation.
	 *
	 * @return  void
	 */
	function approve()
	{
		$app = JFactory::getApplication();

		$id       = $app->input->getUint('oid', 0);
		$conf_key = $app->input->getAlnum('conf_key', '');
		
		if (!$conf_key)
		{
			// missing confirmation key
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDNOROWS') . '</div>';
			return;
		}

		$model = $this->getModel();

		try
		{
			// check if the reservation has expired
			$model->checkExpired(['id' => $id]);

			// get restaurant reservation details
			$reservation = VREOrderFactory::getReservation($id, null, ['conf_key' => $conf_key]);
		}
		catch (Exception $e)
		{
			// order not found
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDNOROWS') . '</div>';
			return;
		}
		
		// make sure the order can be approved
		if ($reservation->statusRole != 'PENDING')
		{
			if ($reservation->statusRole == 'APPROVED')
			{
				// the order was already approved
				echo '<div class="vr-confirmpage order-notice">' . JText::translate('VRCONFORDISCONFIRMED') . '</div>';
			}
			else
			{
				// the order cannot be approved anymore
				echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDISREMOVED') . '</div>';
			}

			return;
		}
	
		$data = [
			'id'     => $reservation->id,
			'status' => JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code'),
		];

		// approve order
		$model->save($data);
		
		$mailOptions = [];
		// validate e-mail rules before sending
		$mailOptions['check'] = true;

		// send e-mail notification to the customer
		$model->sendEmailNotification($id, $mailOptions);

		// send e-mail notification to the administrator(s)
		$mailOptions['client'] = 'admin';
		$model->sendEmailNotification($id, $mailOptions);
		
		echo '<div class="vr-confirmpage order-good">' . JText::translate('VRCONFORDCOMPLETED') . '</div>';
	}

	/**
	 * Rejects the specified reservation.
	 *
	 * @return  void
	 * @since   1.9
	 */
	function reject()
	{
		$app = JFactory::getApplication();

		$id       = $app->input->getUint('oid', 0);
		$conf_key = $app->input->getAlnum('conf_key', '');
		
		if (!$conf_key)
		{
			// missing confirmation key
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDNOROWS') . '</div>';
			return;
		}

		$model = $this->getModel();

		try
		{
			// get restaurant reservation details
			$reservation = VREOrderFactory::getReservation($id, null, ['conf_key' => $conf_key]);
		}
		catch (Exception $e)
		{
			// order not found
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDNOROWS') . '</div>';
			return;
		}
		
		// make sure the order can be rejected
		if ($reservation->statusRole != 'PENDING')
		{
			if ($reservation->statusRole == 'EXPIRED')
			{
				// the order was already rejected
				echo '<div class="vr-confirmpage order-notice">' . JText::translate('VRORDALREADYREJECTED') . '</div>';
			}
			else
			{
				// the order cannot be rejected anymore
				echo '<div class="vr-confirmpage order-error">' . JText::translate('VRORDCANNOTREJECT') . '</div>';
			}

			return;
		}

		try
		{
			// look for a specific rejected status code
			$status = JHtml::fetch('vrehtml.status.rejected', 'restaurant', 'code');
		}
		catch (Exception $e)
		{
			// rejected status code not supported, fallback to removed status
			$status = JHtml::fetch('vrehtml.status.removed', 'restaurant', 'code');	
		}
	
		$data = [
			'id'     => $reservation->id,
			'status' => $status,
		];

		// reject order
		$model->save($data);
		
		$mailOptions = [];
		// validate e-mail rules before sending
		$mailOptions['check'] = true;

		// send e-mail notification to the customer
		$model->sendEmailNotification($id, $mailOptions);

		// send e-mail notification to the administrator(s)
		$mailOptions['client'] = 'admin';
		$model->sendEmailNotification($id, $mailOptions);
		
		echo '<div class="vr-confirmpage order-good">' . JText::translate('VRREJECTORDCOMPLETED') . '</div>';
	}

	/**
	 * Mark the specified reservation as cancelled.
	 *
	 * @return 	void
	 */
	public function cancel()
	{	
		$app    = JFactory::getApplication();
		$config = VREFactory::getConfig();
		
		$oid = $app->input->getUint('oid', 0);
		$sid = $app->input->getString('sid', '');

		// get cancellation reason, if specified
		$reason = trim($app->input->getString('reason', ''));

		$itemid = $app->input->getUint('Itemid');

		// set redirection URL
		$uri = 'index.php?option=com_vikrestaurants&view=reservation&ordnum=' . $oid . '&ordkey=' . $sid . ($itemid ? '&Itemid=' . $itemid : '');
		$this->setRedirect(JRoute::rewrite($uri, false));

		// validate cancellation reason requirements
		if ((strlen($reason) > 0 && strlen($reason) < 32)
			|| (strlen($reason) == 0 && $config->getUint('cancreason') == 2))
		{
			// invalid reason
			return false;
		}
		
		if (!$config->getBool('enablecanc'))
		{
			// cancellation disabled
			$app->enqueueMessage(JText::translate('VRORDERCANCDISABLEDERROR'), 'error');
			return false;
		}

		// Get reservation details.
		// In case the reservation doesn't exist, an exception
		// will be thrown.
		$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

		// validate cancellation time restrictions
		if (!VikRestaurants::canUserCancelOrder($reservation))
		{
			/**
			 * The translation varies according to the number (singular or plural)
			 * and to the unit (days or hours).
			 * 
			 * @since 1.9.1
			 */
			$langstr = 'VRORDERCANCEXPIREDERROR_N_' . strtoupper($config->get('cancunit', 'days'));

			// currently unable to cancel the reservation
			$error = JText::plural($langstr, $config->getUint('canctime'));
			$app->enqueueMessage($error, 'error');
			return false;
		}

		// get reservation model
		$model = $this->getModel();

		/**
		 * NOTE: it is possible to use the onBeforeSaveReservation hook to validate the order
		 * before saving it. The "scope" attribute will let you understand that we are
		 * going to cancel the reservation.
		 */

		// prepare save data
		$data = [
			'id'     => $oid,
			'status' => JHtml::fetch('vrehtml.status.cancelled', 'restaurant', 'code'),
			'scope'  => 'cancellation',
		];

		// update records
		if (!$model->save($data))
		{
			// get last registered error
			$error = $model->getError($index = null, $string = true);
			$app->enqueueMessage($error ? $error : JText::translate('ERROR'), 'error');
			return false;
		}

		$mailOptions = [];
		// validate e-mail rules before sending
		$mailOptions['check'] = true;

		// send e-mail notification to the customer
		$model->sendEmailNotification($oid, $mailOptions);

		// send e-mail notification to the administrator(s)
		$mailOptions['client'] = 'cancellation';
		$mailOptions['cancellation_reason'] = $reason;
		$model->sendEmailNotification($oid, $mailOptions);

		return true;
	}
}
