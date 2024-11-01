<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The API event (plugin) representation.
 *
 * @see Response
 *
 * @since 1.9
 */
abstract class Event
{
	/**
	 * The name of the event. Usually equal to the filename.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Internal configuration registry.
	 *
	 * @var \JObject
	 */
	private $options;

	/**
	 * Class constructor.
	 *
	 * @param  string  $name     The name of the event.
	 * @param  array   $options  A configuration array/object.
	 */
	public function __construct(string $name = '', $options = [])
	{
		$this->name = strlen($name) ? $name : uniqid();

		// create configuration registry
		$this->options = new \JObject($options);
	}

	/**
	 * Returns the name of the event.
	 *
	 * @return  string  The name of the event.
	 */
	final public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the title of the event, a more readable representation of the plugin name.
	 *
	 * @return  string  The title of the event.
	 */
	public function getTitle()
	{
		return ucwords(str_replace('_', ' ', $this->name));
	}

	/**
	 * Returns the short description of the plugin.
	 *
	 * @return  string  An empty string. To display a description,
	 *                  override this method from the child class.
	 */
	public function getShortDescription()
	{
		return '';
	}

	/**
	 * Returns the full description of the plugin.
	 *
	 * @return  string  An empty string. To display a description,
	 *                  override this method from the child class.
	 */
	public function getDescription()
	{
		return '';
	}

	/**
	 * Returns true if the plugin is always authorised, otherwise false.
	 * When this value is false, the system will need to authorise the plugin 
	 * through the ACL of the user.
	 *
	 * @return  bool  Always false. To allow always this plugin,
	 *                override this method from the child class.
	 */
	public function alwaysAllowed()
	{
		return false;
	}

	/**
	 * Event configuration getter.
	 *
	 * @param   string  $key  The setting name.
	 * @param   mixed   $def  The default value to use.
	 *
	 * @return  mixed   The setting value or the default one.
	 */
	final public function get(string $key, $def = null)
	{
		return $this->options->get($key, $def);
	}

	/**
	 * Event configuration setter.
	 *
	 * @param   string  $key  The setting name.
	 * @param   mixed   $val  The setting value.
	 *
	 * @return  self    This object to support chaining.
	 */
	final public function set(string $key, $val)
	{
		return $this->options->set($key, $val);
	}

	/**
	 * Returns the event configuration as array.
	 *
	 * @return  array
	 */
	final public function getOptions()
	{
		return $this->options->getProperties();
	}

	/**
	 * Perform the action of the event.
	 *
	 * @param   array     $args      The provided arguments for the event.
	 * @param   Response  $response  The response object for admin.
	 *
	 * @return  mixed     The response to output or the error message (Error).
	 */
	final public function run(array $args, Response $response)
	{
		return $this->execute($args, $response);
	}

	/**
	 * The custom action that the event have to perform.
	 * This method should not contain any exit or die functions,
	 * otherwise the event won't be properly terminated.
	 *
	 * @param   array     $args      The provided arguments for the event.
	 * @param   Response  $response  The response object for admin.
	 *
	 * @return  mixed     The response to output or the error message (Error).
	 */
	abstract protected function execute(array $args, Response $response);
}
