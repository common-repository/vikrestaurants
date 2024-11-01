<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\ReservationCodes\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\ReservationCodes\CodeRule;

/**
 * Class used to send a notification to start the dishes ordering when this
 * reservation code rule is invoked.
 *
 * This rule should be invoked when the customer arrives at the restaurant
 * or when they sit at the table.
 *
 * @since 1.9
 */
class OrderDishesCodeRule extends CodeRule
{
	/**
	 * The first name of the customer.
	 *
	 * @var string
	 */
	protected $customerName;

	/**
	 * The URL to reach to start ordering the dishes.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * @inheritDoc
	 * 
	 * Available only for restaurant reservations.
	 */
	public function isSupported(string $group)
	{
		return !strcasecmp($group, 'restaurant');
	}

	/**
	 * @inheritDoc
	 */
	public function execute($order)
	{
		// get current language tag
		$langtag = \JFactory::getLanguage()->getTag();

		// load front-end language according to the tag assigned to the reservation
		\VikRestaurants::loadLanguage($order->langtag ?: $langtag);

		// extract first name from nominative
		$this->customerName = $order->purchaser_nominative;

		if ($this->customerName)
		{
			$chunks = preg_split("/\s+/", $this->customerName);
			// assume the customer specified its name first
			$this->customerName = array_shift($chunks);
		}
		else
		{
			// use default "Customer"
			$this->customerName = \JText::translate('VRORDERCUSTOMER');
		}

		// fetch URL
		$this->url = 'index.php?option=com_vikrestaurants&view=orderdishes&ordnum=' . $order->id . '&ordkey=' . $order->sid;
		$this->url = \VREFactory::getPlatform()->getUri()->route($this->url, false);
		
		// send e-mail notification
		$this->sendMail($order);
		// send SMS notification
		$this->sendSMS($order);

		// reload previous language
		\VikRestaurants::loadLanguage($langtag, 'auto');
	}

	/**
	 * Sends a SMS notification to the phone number
	 * specified by the customer during the purchase.
	 *
	 * @param   mixed  $order  The order details object.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	protected function sendSMS($order)
	{
		if (!$order->purchaser_phone)
		{
			// missing phone number
			return false;
		}

		try
		{
			// get current SMS instance
			$smsapi = \VREApplication::getInstance()->getSmsInstance();

			// prepare message
			$text = \JText::sprintf('VRE_ORDERDISHES_SMS_NOTIFICATION', $this->customerName, $this->url);

			// try to send a notification to the specified phone number
			$smsapi->sendMessage($order->purchaser_phone, $text);
		}
		catch (\Exception $e)
		{
			// SMS framework not supported
			return false;
		}

		return true;
	}

	/**
	 * Sends an e-mail notification to the address
	 * specified by the customer during the purchase.
	 *
	 * @param   mixed  $order  The order details object.
	 *
	 * @return 	bool   True on success, false otherwise.
	 */
	protected function sendMail($order)
	{
		if (!$order->purchaser_mail)
		{
			// missing phone number
			return false;
		}

		// get sender e-mail address
		$sendermail = \VikRestaurants::getSenderMail();
		// get restaurant name
		$fromname = \VREFactory::getConfig()->getString('restname');

		// prepare subject
		$subject = \JText::sprintf('VRE_ORDERDISHES_EMAIL_NOTIFICATION_SUBJECT');
		// prepare message
		$text = \JText::sprintf('VRE_ORDERDISHES_EMAIL_NOTIFICATION', $this->customerName, $fromname, $this->url);

		// try to send the e-mail to the specified address
		return \VREApplication::getInstance()->sendMail(
			$sendermail,
			$fromname,
			$order->purchaser_mail,
			$sendermail,
			$subject,
			$text,
			$attachments = null,
			$is_html = false
		);
	}
}
