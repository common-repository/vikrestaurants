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
 * VikRestaurants restaurant reservation summary view.
 * In case the request doesn't provide the ORDER NUMBER
 * and the ORDER KEY, a form to search a reservation
 * will be displayed.
 *
 * @since 1.8
 */
class VikRestaurantsViewreservation extends JViewVRE
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
		
		$reservation = null;
		
		// make sure the ORDER NUMBER and ORDER KEY have been submitted
		if (!empty($oid) && !empty($sid))
		{
			// check if the reservation has expired
			JModelVRE::getInstance('reservation')->checkExpired(['id' => $oid]);

			try
			{
				// get reservation details (filter by ID and SID)
				$reservation = VREOrderFactory::getReservation($oid, JFactory::getLanguage()->getTag(), ['sid' => $sid]);
			}
			catch (Exception $e)
			{
				// reservation not found
			}

			if ($reservation)
			{
				// check if a payment is required
				if ($reservation->payment)
				{
					// reload payment details to access the parameters
					$payment = JModelVRE::getInstance('payment')->getItem($reservation->payment->id);

					// apply payment translations
					$payment->name    = $reservation->payment->name;
					$payment->prenote = $reservation->payment->notes->beforePurchase;
					$payment->note    = $reservation->payment->notes->afterPurchase;

					$paymentData = [];

					$uri = VREFactory::getPlatform()->getUri();

					/**
					 * The payment URLs are correctly routed for external usage.
					 *
					 * @since 1.8
					 */
					$return_url = $uri->route("index.php?option=com_vikrestaurants&view=reservation&ordnum={$oid}&ordkey={$sid}", false);
					$error_url  = $uri->route("index.php?option=com_vikrestaurants&view=reservation&ordnum={$oid}&ordkey={$sid}", false);

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
				
					$paymentData['type']                 = 'restaurant.create';
					$paymentData['oid']                  = $reservation->id;
					$paymentData['sid']                  = $reservation->sid;
					$paymentData['tid']                  = 0;
					$paymentData['transaction_name']     = JText::sprintf('VRTRANSACTIONNAME', $config->get('restname'));
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
				// raise error, reservation not found
				$app->enqueueMessage(JText::translate('VRORDERRESERVATIONERROR'), 'error');
			}		
		}
		
		if (!$reservation)
		{
			// use "track" layout in case the reservation
			// was not found or in case the order number
			// and the order key was not submitted
			$this->setLayout('track');
		}

		/**
		 * An object containing the details of the specified
		 * restaurant reservation.
		 * 
		 * @var VREOrderRestaurant|null
		 */
		$this->reservation = $reservation;

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

		$reservation = clone $this->reservation;

		/**
		 * Added support for online bill payment.
		 * Temporarily revert status to PENDING to allow
		 * payments in case the bill is closed and the
		 * remaining balance is higher than 0.
		 *
		 * @since 1.8.1
		 */
		if ($reservation->bill_closed && $this->payment['total_to_pay'] && $reservation->statusRole === 'APPROVED')
		{
			$reservation->statusRole = 'PENDING';
		}

		// build display data
		$data = [
			'data'  => $this->payment,
			'order' => $reservation,
			'scope' => 'restaurant',
		];

		// get status role to identify the correct payment layout
		$status = strtolower($reservation->statusRole);

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

		// Make sure the reservation page is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Reservation > [ORDNUM]-[ORDKEY]
		if ($last && strpos($last->link, '&view=reservation') === false && !empty($this->reservation))
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=reservation&ordnum=' . $this->reservation->id . '&ordkey=' . $this->reservation->sid;
			$pathway->addItem($this->reservation->id . '-' . $this->reservation->sid, $link);
		}
	}
}
