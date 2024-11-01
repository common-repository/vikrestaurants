<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Decorators;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplate;
use E4J\VikRestaurants\Mail\MailTemplateDecorator;

/**
 * Adds support to the following template data (tags):
 * 
 * - {order_number}         The order number (ID).
 * - {order_key}            The order key (SID).
 * - {order_date_time}      The check-in date and time.
 * - {order_status}         The order status label.
 * - {order_payment}        The selected payment name.
 * - {order_payment_notes}  The selected payment notes.
 * - {order_coupon_code}    The coupon text as [CODE] : [AMOUNT].
 * - {user_name}            The name of the CMS user.
 * - {user_email}           The e-mail of the CMS user.
 * - {user_username}        The login username of the CMS user.
 *
 * @since 1.9
 */
final class OrderMailDecorator implements MailTemplateDecorator
{
	/** @var \VREOrderWrapper */
	private $order;

	/**
	 * Class constructor.
	 * 
	 * @param  VREOrderWrapper  $order  The order details.
	 */
	public function __construct(\VREOrderWrapper $order)
	{
		$this->order = $order;
	}

	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		/** @var E4J\VikRestaurants\Config\AbstractConfiguration */
		$config = \VREFactory::getConfig();

		/** @var E4J\VikRestaurants\Currency\Currency */
		$currency = \VREFactory::getCurrency();

		// convert the check-in date and time into a more readable format
		$formattedCheckin = \JHtml::fetch(
			'date',
			$this->order->checkin_ts,
			\JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'),
			date_default_timezone_get()
		);

		$paymentNotes = '';

		// fetch payment details
		if ($this->order->payment)
		{
			// use payment name
			$paymentName = $this->order->payment->name;

			if ($this->order->statusRole == 'PENDING')
			{
				// show notes before purchase when waiting for the payment
				$paymentNotes = $this->order->payment->notes->beforePurchase;
			}
			else if ($this->order->statusRole == 'APPROVED')
			{
				// show notes after purchase when the order has been confirmed
				$paymentNotes = $this->order->payment->notes->afterPurchase;	
			}

			if ($paymentNotes)
			{
				/**
				 * Render HTML description to interpret attached plugins.
				 * 
				 * @since 1.9
				 */
				\VREApplication::getInstance()->onContentPrepare($paymentNotes, $full = true);

				$paymentNotes = $paymentNotes->text;
			}
		}
		else
		{
			// payment not selected, use "total to pay" label
			$paymentName = \JText::translate('VRTKORDERTOTALTOPAY');
		}

		// fetch coupon string
		if ($this->order->coupon)
		{
			// obtain coupon code
			$couponText = $this->order->coupon->code;

			if ($this->order->coupon->amount > 0)
			{
				$couponText .= ' : ';

				if ($this->order->coupon->type == 1)
				{
					// we have a percentage coupon code
					$couponText .= $currency->format($this->order->coupon->amount, [
						'symbol'     => '%',
						'position'   => 1,
						'space'      => false,
						'no_decimal' => true,
					]);
				}
				else
				{
					// we have a fixed coupon code
					$couponText .= $currency->format($this->order->coupon->amount);
				}
			}
		}
		else
		{
			// no redeemed coupon code
			$couponText = '';
		}

		// get billing details
		$billing = $this->order->billing;

		if ($billing && $billing->jid > 0)
		{
			// get user details
			$user = \JFactory::getUser($billing->jid);
		}
		else
		{
			// we have a guest user
			$user = null;
		}

		// get name chunks
		$customerNameChunks = preg_split("/\s+/", (string) $this->order->purchaser_nominative);

		// extract last name from the list
		$customerLastName = array_pop($customerNameChunks);
		// join remaining chunks into the first name
		$customerFirstName = implode(' ', $customerNameChunks);

		if (!$customerFirstName)
		{
			// only one name provided, use it as first name
			$customerFirstName = $customerLastName;
			$customerLastName  = '';
		}

		// fetch reservation codes history
		$history = $this->order->history;

		if ($history)
		{
			// history not empty, fetch details of the last registered status code
			$reservationStatusCode  = $history[0]->code;
			$reservationStatusNotes = $history[0]->notes ?: $history[0]->codeNotes;

			if ($history[0]->icon)
			{
				$reservationStatusImage = \JHtml::fetch('vrehtml.media.display', $history[0]->icon, [
					'alt'   => $reservationStatusCode,
					'small' => true,
					'style' => 'max-width: 100%;',
				]);
			}
		}

		// register  template data
		$template->addTemplateData([
			'order_number'             => $this->order->id,
			'order_key'                => $this->order->sid,
			'order_date_time'          => $formattedCheckin,
			'order_status'             => \JHtml::fetch('vrehtml.status.display', $this->order->status),
			'order_status_name'        => \JHtml::fetch('vrehtml.status.display', $this->order->status, 'plain'),
			'order_payment'            => $paymentName,
			'order_payment_notes'      => $paymentNotes,
			'order_coupon_code'        => $couponText,
			'user_name'                => $user ? $user->name : $this->order->purchaser_nominative,
			'user_email'               => $user ? $user->email: $this->order->purchaser_mail,
			'user_username'            => $user ? $user->username : '',
			'customer_full_name'       => $this->order->purchaser_nominative,
			'customer_first_name'      => $customerFirstName,
			'customer_last_name'       => $customerLastName,
			'reservation_status_code'  => $reservationStatusCode ?? '',
			'reservation_status_image' => $reservationStatusImage ?? '',
			'reservation_status_notes' => $reservationStatusNotes ?? '',
		]);
	}
}
