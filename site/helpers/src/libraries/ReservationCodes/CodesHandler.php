<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\ReservationCodes;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Used to handle the rules of the reservation codes.
 *
 * @since 1.9
 */
class CodesHandler
{
	/**
	 * A list of cached rules.
	 *
	 * @var array
	 */
	protected static $rules = null;

	/**
	 * Helper method used to extend the paths in which the rules
	 * should be found.
	 *
	 * @param   mixed  $path  The path to include (optional).
	 *
	 * @return  array  A list of supported directories.
	 */
	public static function addIncludePath($path = null)
	{
		static $paths = [];
		
		if (!$paths)
		{
			// add standard folder
			$paths[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Rules';
		}

		// include path if specified
		if ($path && is_dir($path))
		{
			$paths[] = $path;

			// reset rules as some of them might need to be reloaded
			static::$rules = null;
		}

		// return list of included paths
		return $paths;
	}

	/**
	 * Returns a list of supported rules.
	 *
	 * @param   mixed  $group  Optionally filter the rules by group (restaurant or takeaway).
	 *
	 * @return  array  A list of supported rules.
	 */
	public static function getSupportedRules($group = null)
	{
		// fetch drivers only once
		if (is_null(static::$rules))
		{
			static::$rules = [];

			// configuration array for rules
			$config = [];

			/**
			 * Keep loading the old resources for backward compatibility.
			 * 
			 * @deprecated 1.10  Use the new framework instead.
			 */
			\VRELoader::import('library.rescodes.handler');
			\VRELoader::import('library.rescodes.rule');

			/**
			 * This event can be used to support custom rules.
			 * It is enough to include the directory containing the new rules.
			 * Only the files that inherits the CodeRule class will be taken.
			 *
			 * Example:
			 * // register custom rule(s)
			 * CodesHandler::addIncludePath($path);
			 * // assign plugin configuration to rule (customrule is the filename)
			 * $config['customrule'] = $this->params;
			 *
			 * @param   array  &$config  It is possible to inject the configuration for
			 *                           a specific rule. The parameters have to be assigned
			 *                           by using the rule file name.
			 *
			 * @return  void
			 *
			 * @since   1.8
			 */
			\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadReservationCodesRules', [&$config]);

			// iterate loaded paths
			foreach (static::addIncludePath() as $path)
			{
				// get all drivers within the specified folder
				$files = \JFolder::files($path, '\.php$');

				// iterate files found
				foreach ($files as $file)
				{
					// require file only once
					$loaded = require_once \JPath::clean($path . '/' . $file);

					// fetch class name from file name
					$filename = preg_replace("/\.php$/i", '', $file);
					$class    = 'E4J\\VikRestaurants\\ReservationCodes\\Rules\\' . $filename;
					$key      = strtolower(preg_replace("/Rule$/i", '', $filename));

					/**
					 * Keep looking for old classes too for backward compatibility.
					 * 
					 * @deprecated 1.10  Use the E4J\VikRestaurants\ReservationCodes\Rules namespace instead.
					 */
					if (!class_exists($class))
					{
						// file not found, try to look for a deprecated resource
						$class = 'ResCodesRule' . $filename;
						$key   = strtolower($filename);
					}

					// make sure the class exists
					if (class_exists($class))
					{
						// fetch configuration params
						$params = isset($config[$key]) ? $config[$key] : [];

						// instantiate class
						$driver = new $class($params);

						// use driver only whether it is a valid instance
						if ($driver instanceof CodeRule)
						{
							// map drivers by key
							static::$rules[$driver->getID()] = $driver;
						}
					}
				}
			}

			// sort rules by ascending name (keep keys)
			uasort(static::$rules, function($a, $b)
			{
				return strcasecmp($a->getName(), $b->getName());
			});
		}

		// check if we have a group
		if (is_null($group))
		{
			// no group filtering
			return static::$rules;
		}

		// filter the rules by group
		return array_filter(static::$rules, function($driver) use($group)
		{
			// make sure the driver supports this group
			return $driver->isSupported($group);
		});
	}

	/**
	 * Returns an instance of the requested rule.
	 *
	 * @param   string  $rule   The rule name.
	 * @param   string  $group  Optionally check whether the rule supports the group.
	 *
	 * @return  CodeRule
	 *
	 * @throws 	\RuntimeException
	 */
	public static function getRule(string $rule, string $group = null)
	{
		// get all supported rules
		foreach (static::getSupportedRules() as $driver)
		{
			// compare rule with the specified one
			if ($driver->getID() == $rule && (is_null($group) || $driver->isSupported($group)))
			{
				// rule found
				return $driver;
			}
		}

		// rule not found, throw exception
		throw new \RuntimeException(sprintf('Reservations code [%s] rule not found', $rule), 404);
	}

	/**
	 * Triggers the rule of the specified reservation code.
	 *
	 * @param   int     $codeId   The reservation code ID.
	 * @param   int     $orderId  The reservation/order ID.
	 * @param   string  $group    The requested group (restaurant or takeaway).
	 *
	 * @return  bool    True if the code owns a rule, false otherwise.
	 *
	 * @throws  \RuntimeException
	 */
	public static function trigger(int $codeId, int $orderId, string $group)
	{
		if (!$codeId || !$orderId || !$group)
		{
			// invalid code, order or group
			throw new \RuntimeException('Cannot perform the code rule due to an invalid request', 400);
		}

		/** @var \stdClass|null */
		$code = \JModelVRE::getInstance('rescode')->getItem($codeId);

		if (!$code)
		{
			// reservation code not found
			throw new \RuntimeException(sprintf('Reservation code [%d] not found', $codeId), 404);
		}

		if ($code->rule)
		{
			if ($group == 'restaurant')
			{
				// load restaurant reservation
				$order = \VREOrderFactory::getReservation($orderId);
			}
			else if ($group == 'takeaway')
			{
				// load take-away order
				$order = \VREOrderFactory::getOrder($orderId);
			}
			else
			{
				// use specified argument
				$order = $orderId;
			}

			// get rule driver
			$driver = static::getRule($code->rule, $group);

			// execute the driver
			$driver->execute($order);	
		}

		/**
		 * Check whether the system should re-send an e-mail notification
		 * whenever the current code is assigned to the selected
		 * reservation/order.
		 * 
		 * @since 1.9
		 */
		if ($code->sendmail)
		{
			if ($group === 'takeaway')
			{
				$model = \JModelVRE::getInstance('tkreservation');
			}
			else
			{
				$model = \JModelVRE::getInstance('reservation');
			}
			
			// send e-mail notification to the customer
			$model->sendEmailNotification($orderId, [
				'client' => 'customer',
			]);
		}

		return true;
	}
}
