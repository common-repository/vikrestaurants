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
 * VikRestaurants take-away order summary view.
 * In case the request doesn't provide the ORDER NUMBER
 * and the ORDER KEY, a form to search an order
 * will be displayed.
 *
 * @since 1.8
 */
class VikRestaurantsVieworder extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$config = VREFactory::getConfig();
		
		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');
		
		$order = null;

		// make sure the ORDER NUMBER and ORDER KEY have been submitted
		if (!empty($oid) && !empty($sid))
		{
			// check if the order has expired
			JModelVRE::getInstance('tkreservation')->checkExpired(['id' => $oid]);

			try
			{
				// get order details (filter by ID and SID)
				$order = VREOrderFactory::getOrder($oid, JFactory::getLanguage()->getTag(), ['sid' => $sid]);
			}
			catch (Exception $e)
			{
				// order not found
			}

			if ($order)
			{
				// check if a payment is required
				if ($order->id_payment > 0)
				{
					// reload payment details to access the parameters
					$payment = JModelVRE::getInstance('payment')->getItem($order->payment->id);

					// apply payment translations
					$payment->name    = $order->payment->name;
					$payment->prenote = $order->payment->notes->beforePurchase;
					$payment->note    = $order->payment->notes->afterPurchase;

					$paymentData = [];

					$uri = VREFactory::getPlatform()->getUri();

					/**
					 * The payment URLs are correctly routed for external usage.
					 *
					 * @since 1.8
					 */
					$return_url = $uri->route("index.php?option=com_vikrestaurants&view=order&ordnum={$oid}&ordkey={$sid}", false);
					$error_url  = $uri->route("index.php?option=com_vikrestaurants&view=order&ordnum={$oid}&ordkey={$sid}", false);

					/**
					 * Include the Notification URL in both the PLAIN and ROUTED formats.
					 *
					 * @since 1.8.1
					 */
					$notify_url = "index.php?option=com_vikrestaurants&task=order.notifypayment&ordnum={$oid}&ordkey={$sid}";
				
					$paymentData['type']                 = 'takeaway.create';
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
					VREFactory::getPlatform()->getDispatcher()->trigger('onInitPaymentTransaction', [&$paymentData, &$paymentData['payment_info']->params]);
				
					/**
					 * An associative array containing the payment
					 * details, if any.
					 * 
					 * @var array
					 */
					$this->payment = $paymentData;
				}
			}
			else
			{
				// raise error, order not found
				$app->enqueueMessage(JText::translate('VRORDERRESERVATIONERROR'), 'error');
			}		
		}
		
		if (!$order)
		{
			// use "track" layout in case the order
			// was not found or in case the order number
			// and the order key was not submitted
			$this->setLayout('track');
		}

		/**
		 * An object containing the details of the specified
		 * take-away order.
		 * 
		 * @var VREOrderTakeaway|null
		 */
		$this->order = $order;

		/**
		 * The current menu item ID.
		 * 
		 * @var int|null
		 * @since 1.9
		 */
		$this->itemid = $app->input->get('Itemid', null, 'uint');

		// prepare page content
		VikRestaurants::prepareContent($this);

		// extend pathway for breadcrumbs module
		$this->extendPathway($app);
		
		// display the template
		parent::display($tpl);
	}

	/**
	 * Checks whether the payment (if needed) matches
	 * the specified position. In that case, the payment
	 * form/notes will be echoed.
	 *
	 * @param 	string 	$position  The position in which to print the payment.
	 *
	 * @return 	string 	The HTML to display.
	 */
	protected function displayPayment($position)
	{
		if (empty($this->payment))
		{
			// nothing to display
			return '';
		}

		$position = 'vr-payment-position-' . $position;

		// get payment position
		$tmp = $this->payment['payment_info']->position;

		if (!$tmp)
		{
			// use bottom by default
			$tmp = 'vr-payment-position-bottom';
		}

		// compare payment position
		if ($tmp != $position)
		{
			// position doesn't match
			return '';
		}

		// build display data
		$data = [
			'data'  => $this->payment,
			'order' => $this->order,
			'scope' => 'takeaway',
		];

		// get status role to identify the correct payment layout
		$status = strtolower($this->order->statusRole);

		if (!$status)
		{
			// unable to detect the status role...
			return '';
		}

		// return payment layout based on current status role
		return JLayoutHelper::render('blocks.payment.' . $status, $data);
	}

	/**
	 * Extends the pathway for breadcrumbs module.
	 *
	 * @param 	mixed 	$app  The application instance.
	 *
	 * @return 	void
	 *
	 * @since 	1.9
	 */
	protected function extendPathway($app)
	{
		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		// Make sure the order page is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Order > [ORDNUM]-[ORDKEY]
		if ($last && strpos($last->link, '&view=order') === false && !empty($this->order))
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=order&ordnum=' . $this->order->id . '&ordkey=' . $this->order->sid;
			$pathway->addItem($this->order->id . '-' . $this->order->sid, $link);
		}
	}
}
