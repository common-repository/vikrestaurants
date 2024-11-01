<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Payment;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Payments abstract factory.
 *
 * The I/O of this class MUST be the same for all the E4J programs that support
 * extendable payment methods.
 *
 * @since 1.9
 */
final class PaymentFactory
{
	/**
	 * Returns a list of supported payment gateways.
	 *
	 * @return  array  A list of paths.
	 */
	public static function getSupportedDrivers()
	{
		/**
		 * Add support for the existing payment gateways.
		 *
		 * @note  Use the correct payments path of the current program.
		 */
		$files = \JFolder::files(VREADMIN . '/payments', '\.php$');

		// get rid of the file path and file extension
		$drivers = array_map(function($driver)
		{
			return preg_replace("/\.php$/i", '', $driver);
		}, $files);

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		/**
		 * Trigger hook to extend the available payment methods.
		 *
		 * @note  The caller (1st argument) must be equals to the program in use.
		 *
		 * @param   string 	      $element  The component that triggered this event.
		 *
		 * @return  string|array  Either a string or an array of payment drivers.
		 *
		 * @since   1.8.5
		 */
		$results = $dispatcher->filter('onLoadSupportedPaymentMethods', ['vikrestaurants']);

		/** @var E4J\VikRestaurants\Event\EventResponse $results */

		// join returned payment drivers with the existing ones
		foreach ($results as $result)
		{
			$drivers = array_merge($drivers, (array) $result);
		}

		// get rid of duplicates and empty elements
		$drivers = array_values(array_unique(array_filter($drivers)));

		// sort drivers in ascending order
		sort($drivers);

		return $drivers;
	}

	/**
	 * Returns the configuration form of a payment.
	 *
	 * @param   string  $payment  The name of the payment.
	 *
	 * @return  array   The configuration array.
	 *
	 * @throws 	\RuntimeException
	 */
	public static function getPaymentConfig(string $payment)
	{
		// strip file extension, if specified
		$payment = preg_replace("/\.php$/i", '', $payment);

		/**
		 * Build internal payments path.
		 *
		 * @note  Use the correct payments path of the current program.
		 */
		$path = VREADMIN . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . $payment . '.php';
		
		if (is_file($path))
		{
			// load internal payment driver
			require_once $path;

			// build camel case notation
			$paymentCamelCase = preg_replace("/[^a-z0-9]+/", ' ', $payment);
			$paymentCamelCase = preg_replace("/\s+/", '', ucwords($paymentCamelCase));

			// use specific class name
			$classname = self::PAYMENT_METHOD_CLASS_PREFIX . $paymentCamelCase;

			if (!class_exists($classname))
			{
				// class not found, fallback to the old notation
				$classname = self::PAYMENT_METHOD_CLASS_PREFIX_DEPRECATED;

				if (!class_exists($classname))
				{
					// the loaded file doesn't contain a valid payment processor
					throw new \RuntimeException(sprintf("Payment [%s] not found", $payment), 404);
				}
			}

			// make sure we have a valid instance
			if (method_exists($classname, 'getAdminParameters'))
			{
				// return configuration array
				return $classname::getAdminParameters();
			}
		}
		else
		{
			/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
			$dispatcher = \VREFactory::getPlatform()->getDispatcher();

			/**
			 * Trigger hook to allow third-party plugins to build a configuration array.
			 *
			 * @note  The caller (1st argument) must be equals to the program in use.
			 *        Also make sure that the dispatcher supports triggerOnce method.
			 *
			 * @param   string  $element  The component that triggered this event.
			 * @param   string  $payment  The ID of the payment driver.
			 *
			 * @return  array   The configuration array.
			 *
			 * @since   1.8.5
			 */
			$result = $dispatcher->filter('onLoadPaymentMethodConfigurationForm', ['vikrestaurants', $payment]);

			/** @var E4J\VikRestaurants\Event\EventResponse $result */

			// get the first available configuration
			$config = $result->first();

			if ($config)
			{
				return (array) $config;
			}
		}	

		// fallback to an empty array
		return [];
	}

	/**
	 * Provides a new payment instance for the specified arguments.
	 *
	 * @param   string  $payment  The name of the payment that should be instantiated.
	 * @param   mixed   $order    The details of the order that has to be paid.
	 * @param   mixed   $config   The payment configuration array or a JSON string.
	 *
	 * @return  mixed   The payment instance.
	 *
	 * @throws  \RuntimeException
	 */
	public static function getPaymentInstance(string $payment, $order = [], $config = [])
	{
		if (is_string($config))
		{
			// decode config from JSON
			$config = (array) json_decode($config, true);
		}
		else
		{
			// always cast to array
			$config = (array) $config;
		}

		// strip file extension, if specified
		$payment = preg_replace("/\.php$/i", '', $payment);

		/**
		 * Build internal payments path.
		 *
		 * @note  Use the correct payments path of the current program.
		 */
		$path = VREADMIN . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . $payment . '.php';
		
		if (is_file($path))
		{
			// load internal payment driver
			require_once $path;

			// build camel case notation
			$paymentCamelCase = preg_replace("/[^a-z0-9]+/", ' ', $payment);
			$paymentCamelCase = preg_replace("/\s+/", '', ucwords($paymentCamelCase));

			// use specific class name
			$classname = self::PAYMENT_METHOD_CLASS_PREFIX . $paymentCamelCase;

			if (!class_exists($classname))
			{
				// class not found, fallback to the old notation
				$classname = self::PAYMENT_METHOD_CLASS_PREFIX_DEPRECATED;

				if (!class_exists($classname))
				{
					// the loaded file doesn't contain a valid payment processor
					throw new \RuntimeException(sprintf("Payment [%s] not found", $payment), 404);
				}
			}

			// create default payment processor
			$processor = new $classname($order, $config);
		}
		else
		{
			/**
			 * Load payment plugin adapter class and instantiate it
			 * to process the payment transaction through a plugin.
			 *
			 * @note  The adapter class must be loaded and renamed
			 *        according to the program requirements.
			 */
			$processor = new PaymentPlugin($payment, $order, $config);
		}
		
		return $processor;
	}

	/**
	 * The class prefix of the instance that should be loaded.
	 * 
	 * @note  The class should start with the 3-letter name of the program.
	 *
	 * @var string
	 */
	const PAYMENT_METHOD_CLASS_PREFIX = 'VREPaymentMethod';

	/**
	 * The deprecated class notation used before the implementation
	 * of this new framework.
	 * 
	 * @note  The class should start with the name of the program.
	 *
	 * @var string
	 */
	const PAYMENT_METHOD_CLASS_PREFIX_DEPRECATED = 'VikRestaurantsPayment';
}
