<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\Payment;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Factory class used to access the payment details and instances depending on
 * the platform currently in use.
 * 
 * @since 1.9
 */
interface PaymentFactoryInterface
{
	/**
	 * Returns a list of supported payment gateways.
	 *
	 * @return  string[]  A list of paths.
	 */
	public function getDrivers();

	/**
	 * Returns the configuration form of the given payment.
	 *
	 * @param   string  $payment  The name of the payment.
	 *
	 * @return  mixed   The configuration array/object.
	 */
	public function getConfigurationForm(string $payment);

	/**
	 * Provides a new payment instance with the specified arguments.
	 *
	 * @param   string  $payment  The name of the payment that should be instantiated.
	 * @param   mixed   $order    The details of the order that has to be paid.
	 * @param   mixed   $config   The payment configuration array or a JSON string.
	 *
	 * @return  mixed   The payment instance.
	 */
	public function getInstance(string $payment, $order = [], $config = []);
}
