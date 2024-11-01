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
 * VikRestaurants take-away order controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerOrder extends VREControllerAdmin
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

		// Get order details (filter by ID and SID).
		// In case the order doesn't exist, an
		// exception will be thrown.
		$order = VREOrderFactory::getOrder($oid, null, ['sid' => $sid]);

		/**
		 * This event is triggered every time a payment tries to validate a transaction made.
		 *
		 * DOES NOT trigger in case the order doesn't exist.
		 *
		 * @param   mixed  $order  The details of the take-away order.
		 *
		 * @return  void
		 *
		 * @since   1.8.1
		 */
		$dispatcher->trigger('onReceivePaymentNotification', [$order]);

		// build return and error URL
		$return_url = "index.php?option=com_vikrestaurants&view=order&ordnum={$oid}&ordkey={$sid}";
		$error_url  = "index.php?option=com_vikrestaurants&view=order&ordnum={$oid}&ordkey={$sid}";

		/**
		 * If we are trying to validate an order already paid/confirmed, auto-redirect to
		 * the return URL instead of throwing an exception.
		 * 
		 * @since 1.9
		 */
		if ($order->statusRole == 'APPROVED')
		{
			$this->setRedirect(JRoute::rewrite($return_url, false));
			return;
		}
		
		/**
		 * Allow the payment for REMOVED orders because they
		 * have been probably paid while they were PENDING.
		 * 
		 * @since 1.8
		 */
		$accepted = [
			'PENDING',
			'EXPIRED',
		];
		
		// make sure the order can be paid
		if (!in_array($order->statusRole, $accepted))
		{
			// status not allowed
			throw new Exception('The current status of the order does not allow any payments.', 403);
		}

		if (!$order->payment)
		{
			// payment method not found
			throw new Exception('The selected payment does not exist', 404);
		}

		// reload payment details to access the parameters
		$payment = JModelVRE::getInstance('payment')->getItem($order->payment->id);

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
		$notify_url = "index.php?option=com_vikrestaurants&task=order.notifypayment&ordnum={$oid}&ordkey={$sid}";
	
		$paymentData['type']                 = 'takeaway.validate';
		$paymentData['oid']                  = $order->id;
		$paymentData['sid']                  = $order->sid;
		$paymentData['tid']                  = 1;
		$paymentData['transaction_name']     = JText::sprintf('VRTRANSACTIONNAME', $config->get('restname'));
		$paymentData['transaction_currency'] = $config->get('currencyname');
		$paymentData['currency_symb']        = $config->get('currencysymb');
		$paymentData['tax']                  = 0;
		$paymentData['return_url']           = $return_url;
		$paymentData['error_url']            = $error_url;
		$paymentData['notify_url']           = $uri->route($notify_url, false);
		$paymentData['notify_url_plain']     = JUri::root() . $notify_url;
		$paymentData['total_to_pay']         = $order->total_due;
		$paymentData['total_net_price']      = $order->total_due;
		$paymentData['total_tax']            = 0;
		$paymentData['shipping']             = 0;
		$paymentData['payment_info']         = $payment;
		$paymentData['details'] = [
			'purchaser_nominative' => $order->purchaser_nominative,
			'purchaser_mail'       => $order->purchaser_mail,
			'purchaser_phone'      => $order->purchaser_phone,
		];

		/**
		 * Added support for customer billing details.
		 *
		 * @since 1.9
		 */
		$paymentData['billing'] = $order->billing;

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

		$model = $this->getModel('tkreservation');
		
		// successful response
		if ($result['verified'])
		{
			$order->statusRole = 'APPROVED';

			if (!empty($result['tot_paid']))
			{
				// increase total amount paid
				$order->tot_paid += (float) $result['tot_paid'];
			}

			if ($order->tot_paid >= $order->total_to_pay)
			{
				// the whole amount has been paid, use the apposite PAID status
				$order->status = JHtml::fetch('vrehtml.status.paid', 'takeaway', 'code');
				$order->paid   = 1;
			}
			else
			{
				// a deposit have been left, use CONFIRMED status
				$order->status = JHtml::fetch('vrehtml.status.confirmed', 'takeaway', 'code');
				$order->paid   = 0;
			}

			// prepare data to dave
			$data = [
				'id'       => $order->id,
				'status'   => $order->status,
				'tot_paid' => $order->tot_paid,
			];

			$model->save($data);
			
			////////////////////////////////////////////////////////////
			////////////////////// NOTIFICATIONS ///////////////////////
			////////////////////////////////////////////////////////////

			$mailOptions = [];
			// validate e-mail rules before sending
			$mailOptions['check'] = true;

			// send e-mail notification to the customer
			$model->sendEmailNotification($order->id, $mailOptions);

			// send e-mail notification to the administrators and operators
			$mailOptions['client'] = 'admin';
			$model->sendEmailNotification($order->id, $mailOptions);
			
			// try to send SMS notifications (1: take-away)
			VikRestaurants::sendSmsAction($order->purchaser_phone, $order->id, 1);

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
					'Order #' . $order->id . '-' . $order->sid . ' (Take-Away)',
					nl2br($result['log']),
				);

				// send error logs to administrator(s)
				VikRestaurants::sendAdminMailPaymentFailed($order->id, $text);

				// get current date and time
				$timeformat = preg_replace("/:i/", ':i:s', $config->get('timeformat'));
				$now = JHtml::fetch('date', 'now', $config->get('dateformat') . ' ' . $timeformat, $app->get('offset', 'UTC'));

				// build log string
				$log  = str_repeat('-', strlen($now) + 4) . "\n";
				$log .= "| $now |\n";
				$log .= str_repeat('-', strlen($now) + 4) . "\n";
				$log .= "\n" . $result['log'];

				if (!empty($order->payment_log))
				{
					// always prepend new logs at the beginning
					$log = $log . "\n\n" . $order->payment_log;
				}

				// prepare save data
				$data = [
					'id'          => $order->id,
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
	 * Approves the specified order.
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

		$model = $this->getModel('tkreservation');

		try
		{
			// check if the order has expired
			$model->checkExpired(['id' => $id]);

			// get take-away order details
			$order = VREOrderFactory::getOrder($id, null, ['conf_key' => $conf_key]);
		}
		catch (Exception $e)
		{
			// order not found
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDNOROWS') . '</div>';
			return;
		}
		
		// make sure the order can be approved
		if ($order->statusRole != 'PENDING')
		{
			if ($order->statusRole == 'APPROVED')
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
			'id'     => $order->id,
			'status' => JHtml::fetch('vrehtml.status.confirmed', 'takeaway', 'code'),
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
	 * Rejects the specified order.
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

		$model = $this->getModel('tkreservation');

		try
		{
			// get take-away order details
			$order = VREOrderFactory::getOrder($id, null, ['conf_key' => $conf_key]);
		}
		catch (Exception $e)
		{
			// order not found
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFORDNOROWS') . '</div>';
			return;
		}
		
		// make sure the order can be rejected
		if ($order->statusRole != 'PENDING')
		{
			if ($order->statusRole == 'EXPIRED')
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
			$status = JHtml::fetch('vrehtml.status.rejected', 'takeaway', 'code');
		}
		catch (Exception $e)
		{
			// rejected status code not supported, fallback to removed status
			$status = JHtml::fetch('vrehtml.status.removed', 'takeaway', 'code');	
		}
	
		$data = [
			'id'     => $order->id,
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
	 * Mark the specified order as cancelled.
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
		$uri = 'index.php?option=com_vikrestaurants&view=order&ordnum=' . $oid . '&ordkey=' . $sid . ($itemid ? '&Itemid=' . $itemid : '');
		$this->setRedirect(JRoute::rewrite($uri, false));

		// validate cancellation reason requirements
		if ((strlen($reason) > 0 && strlen($reason) < 32)
			|| (strlen($reason) == 0 && $config->getUint('tkcancreason') == 2))
		{
			// invalid reason
			return false;
		}
		
		if (!$config->getBool('tkenablecanc'))
		{
			// cancellation disabled
			$app->enqueueMessage(JText::translate('VRORDERCANCDISABLEDERROR'), 'error');
			return false;
		}

		// Get order details.
		// In case the order doesn't exist, an exception
		// will be thrown.
		$order = VREOrderFactory::getOrder($oid, null, ['sid' => $sid]);

		// validate cancellation time restrictions
		if (!VikRestaurants::canUserCancelOrder($order))
		{
			/**
			 * The translation varies according to the number (singular or plural)
			 * and to the unit (days or hours).
			 * 
			 * @since 1.9.1
			 */
			$langstr = 'VRORDERCANCEXPIREDERROR_N_' . strtoupper($config->get('tkcancunit', 'days'));

			// currently unable to cancel the order
			$error = JText::plural($langstr, $config->getUint('tkcanctime'));
			$app->enqueueMessage($error, 'error');
			return false;
		}

		// get order model
		$model = $this->getModel('tkreservation');

		/**
		 * NOTE: it is possible to use the onBeforeSaveTkreservation hook to validate the order
		 * before saving it. The "scope" attribute will let you understand that we are
		 * going to cancel the order.
		 */

		// prepare save data
		$data = [
			'id'     => $oid,
			'status' => JHtml::fetch('vrehtml.status.cancelled', 'takeaway', 'code'),
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
