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
 * VikRestaurants take-away stock model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkstock extends JModelVRE
{
	/**
	 * Sends an e-mail notification to the administrator(s) about products with
	 * low remaining units.
	 *
	 * @param   array  $options  An array of options.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function sendEmailNotification(array $options = [])
	{
		try
		{
			/** @var E4J\VikRestaurants\Mail\MailTemplate */
			$mailTemplate = E4J\VikRestaurants\Mail\MailFactory::getTemplate('takeaway', 'stock', $options);

			if (!$mailTemplate->shouldSend())
			{
				// probably we have nothing to notify
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

		if ($sent)
		{
			// e-mail sent successfully, now we should flag the processed items as notified to prevent duplicate e-mails
			foreach ($mailTemplate->getItems() as $menu)
			{
				foreach ($menu->list as $item)
				{
					// do not notify again until the administrator refills this product
					$this->setNotified($item, 1);
				}
			}
		}

		return $sent;
	}

	/**
	 * Refills the units of the provided item.
	 * 
	 * @param   array|object  $data  The data to save.
	 * 
	 * @return  bool          True on success, false otherwise.
	 */
	public function refill($data)
	{
		// save stock data
		$id = $this->save($data);

		if (!$id)
		{
			return false;
		}

		/**
		 * Reset notification flag for the product/variation.
		 *
		 * @since 1.8.4
		 */
		$this->setNotified($data, 0);

		return true;
	}

	/**
	 * Updates the "notified" flag for the provided item/variation.
	 * 
	 * @param   array|object  $data  The data to save.
	 * @param   bool          Whether the product has been notified (1) or refilled (0).
	 * 
	 * @return  void
	 */
	public function setNotified($data, $notified)
	{
		$data = (array) $data;

		$update = [];

		// reset notified flag according to the specified parameter
		$update['stock_notified'] = $notified ? 1 : 0;

		if (($data['id_takeaway_option'] ?? 0) > 0)
		{
			// use variation ID as primary key
			$update['id'] = (int) $data['id_takeaway_option'];

			// set variation as not-notified
			JModelVRE::getInstance('tkentryoption')->save($update);
		}
		else
		{
			// use product ID as primary key
			$update['id'] = $data['id_takeaway_entry'] ?? 0;

			// set product as not-notified
			JModelVRE::getInstance('tkentry')->save($update);
		}
	}
}
