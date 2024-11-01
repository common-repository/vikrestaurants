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
 * VikRestaurants credit card details view.
 *
 * @since 1.7
 */
class VikRestaurantsViewccdetails extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

		// request
		$id    = $input->get('id', 0, 'uint');
		$group = $input->get('tid', 0, 'uint');

		// check whether the credit card details should be removed
		$real_hash = $this->checkForRemove($id, $group);

		if ($real_hash === true)
		{
			// display DELETE template
			$this->setLayout('delete');
			parent::display($tpl);
			return;
		}

		// get model instance
		$model = JModelVRE::getInstance($group == 0 ? 'reservation' : 'tkreservation');

		// fetch order details
		$order = $model->getItem($id);

		if (!$order)
		{
			// order not found, display ERROR template
			$this->setLayout('error');
			parent::display($tpl);
			return;
		}

		if (!strlen((string) $order->cc_details))
		{
			// credit card details empty, display ERROR template
			$this->setLayout('error');
			parent::display($tpl);
			return;
		}

		// fetch credit card details
		$this->creditCard = $this->getCreditCard($order);

		// fetch expiration date
		$this->expDate = date('Y-m-d H:i:s', strtotime('+1 day', $order->checkin_ts));

		// register order details
		$this->order = $order;

		// preserve request data
		$this->id     = $id;
		$this->group  = $group;
		$this->rmHash = $real_hash;

		// display the template
		parent::display($tpl);
	}

	/**
	 * Extracts the credit card details of the specified order.
	 *
	 * @param 	object 	$order  The order details.
	 * 
	 * @return 	mixed 	An object with the credit details, null otherwise.
	 *
	 * @since 	1.8
	 */
	private function getCreditCard($order)
	{
		VRELoader::import('library.crypt.cipher');

		/**
		 * Since the decryption is made using mcrypt package, an exception
		 * could be thrown as the server might not have it installed.
		 * 			
		 * We need to wrap the code below within a try/catch and take
		 * the plain string without decrypting it. This was just an 
		 * additional security layer that doesn't alter the compliance
		 * with PCI/DSS rules.
		 *
		 * @since 1.8
		 */
		try
		{
			// unmask encrypted string
			$cipher = SecureCipher::getInstance();

			$card = $cipher->safeEncodingDecryption($order->cc_details);
		}
		catch (Exception $e)
		{
			// This server doesn't support current decryption algorithm.
			// Try decoding plain text
			$card = base64_decode($order->cc_details);
		}

		// decode credit card JSON-string
		return json_decode($card);
	}

	/**
	 * Checks whether the credit card details of the
	 * specified order should be removed.
	 *
	 * @param   int    $id     The order ID.
	 * @param   int    $group  The order group (0: restaurant, 1: takeaway).
	 *
	 * @return  mixed  The hash to use to complete the cancellation, true on delete.
	 */
	private function checkForRemove($id, $group)
	{
		$app = JFactory::getApplication();

		// obtain remove hash from request
		$rm_hash = $app->input->get('rmhash', '', 'string');

		// generate hash to complete remove process
		$real_hash = md5($id . ':' . $group);

		// make sure both the hash strings are equals
		if (!empty($rm_hash) && !strcmp($rm_hash, $real_hash))
		{
			// get model instance
			$model = JModelVRE::getInstance($group == 0 ? 'reservation' : 'tkreservation');

			// prepare save data
			$data = [
				'id'         => $id,
				'cc_details' => '',
			];

			// remove credit card details
			if ($model->save($data))
			{
				return true;
			}
		}

		return $real_hash;
	}
}
