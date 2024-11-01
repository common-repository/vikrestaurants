<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\WordPress;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Payment\PaymentFactoryInterface;

/**
 * Implements the payment factory interface for the WordPress platform.
 * 
 * @since 1.9
 */
class PaymentFactory implements PaymentFactoryInterface
{
	/**
	 * @inheritDoc
	 */
	public function getDrivers()
	{
		// import payment dispatcher
		\JLoader::import('adapter.payment.dispatcher');

		// get paths list of supported drivers
		return \JPaymentDispatcher::getSupportedDrivers('vikrestaurants');
	}

	/**
	 * @inheritDoc
	 */
	public function getConfigurationForm(string $payment)
	{
		// instantiate payment and access its configuration form
		return $this->getInstance($payment)->getAdminParameters();
	}

	/**
	 * @inheritDoc
	 */
	public function getInstance(string $payment, $order = [], $config = [])
	{
		// import payment dispatcher
		\JLoader::import('adapter.payment.dispatcher');

		// instantiate payment
		return \JPaymentDispatcher::getInstance('vikrestaurants', $payment, $order, $config);
	}
}
