<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\Joomla;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Payment\PaymentFactoryInterface;

/**
 * Implements the payment factory interface for the Joomla platform.
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
		/**
		 * Load the available payment drivers through the new framework.
		 *
		 * @since 1.8.5
		 */
		return \E4J\VikRestaurants\Payment\PaymentFactory::getSupportedDrivers();
	}

	/**
	 * @inheritDoc
	 */
	public function getConfigurationForm(string $payment)
	{
		/**
		 * Load the configuration form of the requested driver through
		 * the new framework.
		 *
		 * @since 1.8.5
		 */
		return \E4J\VikRestaurants\Payment\PaymentFactory::getPaymentConfig($payment);
	}

	/**
	 * @inheritDoc
	 */
	public function getInstance(string $payment, $order = [], $config = [])
	{
		/**
		 * Creates a paymentinstance for the requested driver through
		 * the new framework.
		 *
		 * @since 1.8.5
		 */
		return \E4J\VikRestaurants\Payment\PaymentFactory::getPaymentInstance($payment, $order, $config);
	}
}
