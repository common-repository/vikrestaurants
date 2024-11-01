<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Config;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Configuration pool abstraction.
 *
 * @method  int     getInt($name, $default = null)     Get a signed integer.
 * @method  int     getUint($name, $default = null)    Get an unsigned integer.
 * @method  float   getFloat($name, $default = null)   Get a floating-point number.
 * @method  float   getDouble($name, $default = null)  Get a floating-point number.
 * @method  bool    getBool($name, $default = null)    Get a boolean.
 * @method  string  getString($name, $default = null)  Get a string.
 * @method  array   getArray($name, $default = null)   Decode a JSON string and get an array.
 * @method  mixed   getObject($name, $default = null)  Decode a JSON string and get an object.
 * @method  mixed   getJson($name, $default = null)    Decode a JSON string and get an object.
 *
 * @since 1.9
 */
abstract class AbstractConfiguration
{
	/**
	 * The map containing all the settings retrieved.
	 *
	 * @var array
	 */
	private $pool = [];

	/**
	 * An array of options.
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Class constructor.
	 *
	 * @param  array  $options  An array of options.
	 */
	public function __construct(array $options = [])
	{
		$this->options = $options;
	}

	/**
	 * Returns the value of the specified setting.
	 *
	 * @param   string  $key      Name of the setting.
	 * @param   mixed  	$default  Default value in case the setting is empty.
	 * @param   string  $filter   Filter to apply to the value (string by default).
	 *
	 * @return  mixed   The filtered value of the setting.
	 */
	final public function get(string $key, $default = null, string $filter = 'string')
	{
		// if the setting is alread loaded
		if (!array_key_exists($key, $this->pool))
		{
			// otherwise read it from the apposite handler
			$value = $this->retrieve($key);

			// if the returned value is false
			if ($value === false)
			{
				// return the default specified value
				return $default;
			}

			// register the value into the pool
			$this->pool[$key] = $value;	
		}

		// always filter the value
		return $this->_clean($this->pool[$key], $filter);
	}

	/**
	 * Magic method to get filtered input data.
	 *
	 * @param   string  $name  The name of the function. The string next to "get" word will be used as filter.
	 *                         For example, getInt will use a "int" filter.
	 * @param   array   $args  Array containing arguments to retrieve the setting.
	 *                         Contains name of the key and the default value.
	 *
	 * @return  mixed   The filtered value of the setting.
	 */
	public function __call(string $name, array $args)
	{
		// check if the method is prefixed with 'get' word
		if (substr($name, 0, 3) == 'get')
		{
			$key     = '';
			$default = null;
			$filter  = substr($name, 3);

			// check if setting key is set
			if (isset($args[0]))
			{
				$key = $args[0];
			}

			// check if default value is set
			if (isset($args[1]))
			{
				$default = $args[1];
			}

			// obtain configuration value
			return $this->get($key, $default, $filter);
		}

		// unhandled method, throw error
		throw new \BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
	}

	/**
	 * Custom filter implementation.
	 *
	 * @param   mixed   $value   The value to clean.
	 * @param   string  $filter  The type of the value.
	 *
	 * @return  mixed   The filtered value.
	 */
	protected function _clean($value, string $filter)
	{
		switch (strtolower($filter))
		{
			case 'int': 
				$value = intval($value); 
				break;

			case 'uint':
				$value = abs(intval($value));
				break;

			case 'float':
			case 'double':
				$value = floatval($value);
				break;

			case 'bool':
				$value = (empty($value) === false);
				break;

			case 'array':
				$value = (is_array($value) ? $value : (is_string($value) && strlen($value) ? (array) json_decode($value, true) : []));
				break;

			case 'json':
			case 'object':
				$value = (is_object($value) ? $value : (is_string($value) && strlen($value) ? json_decode($value) : new \stdClass));
				break;

			default:
				$value = (string) $value;
		}

		return $value;
	}

	/**
	 * Store the value of the specified setting.
	 *
	 * @param   string  $key  The name of the setting.
	 * @param   mixed   $val  The value of the setting.
	 *
	 * @return  self    This object to support chaining.
	 */
	final public function set(string $key, $val)
	{	
		// if the registration of the setting went fine
		if ($this->register($key, $val))
		{
			// overwrite/push the value of the setting
			$this->pool[$key] = $val;
		}

		return $this;
	}

	/**
	 * Checks if the specified property exists.
	 *
	 * @param   string  $key  The name of the setting.
	 *
	 * @return  bool    True if exists, otherwise false.
	 */
	final public function has($key)
	{
		return array_key_exists($key, $this->pool) || $this->retrieve($key) !== false;
	}

	/**
	 * Retrieve the value of the setting from the instance in which it is stored. 
	 *
	 * @param   string  $key  The name of the setting.
	 *
	 * @return  mixed   The value of the setting if exists, otherwise false.
	 */
	abstract protected function retrieve(string $key);

	/**
	 * Register the value of the setting into the instance in which should be stored.
	 *
	 * @param   string  $key  The name of the setting.
	 * @param   mixed   $val  The value of the setting.
	 *
	 * @return  bool    True in case of success, otherwise false.
	 */
	abstract protected function register(string $key, $val);
}
