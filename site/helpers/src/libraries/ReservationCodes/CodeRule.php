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
 * Reservation code rule abstraction.
 *
 * @since 1.9
 */
abstract class CodeRule implements \JsonSerializable
{
	/**
	 * A configuration array.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Class constructor.
	 *
	 * @param  mixed  $options  A configuration registry.
	 */
	public function __construct(array $options = [])
	{
		// set-up configuration
		$this->options = $options;
	}

	/**
	 * Returns the reservation code rule identifier.
	 *
	 * @return  string
	 */
	final public function getID()
	{
		// obtain all namespace components and take the last one
		$chunks = preg_split("/\\\\/", get_class($this));
		$class  = array_pop($chunks);

		return preg_replace("/CodeRule$/i", '', $class);
	}

	/**
	 * Returns a code readable name.
	 *
	 * @return  string
	 */
	public function getName()
	{
		$id = $this->getID();

		// fetch language key from ID
		$key = 'VRRESCODESRULE' . strtoupper($id);
		// try to translate name
		$text = \JText::translate($key);

		if ($key !== $text)
		{
			// translation found
			return $text;
		}

		// add a space between each lower-upper case intersection
		return preg_replace("/([a-z0-9])([A-Z])/", '$1 $2', $id);
	}

	/**
	 * Returns the description of the reservation code.
	 *
	 * @return  string
	 */
	public function getDescription()
	{
		// fetch language key from ID
		$key = 'VRRESCODESRULEDESC' . strtoupper($this->getID());
		// try to translate description
		$text = \JText::translate($key);

		if ($key !== $text)
		{
			// translation found
			return $text;
		}

		// return empty description
		return '';
	}

	/**
	 * Checks whether the specified group is supported
	 * by the rule. Children classes can override this
	 * method to drop the support for a specific group.
	 *
	 * @param   string 	$group  The group to check.
	 *
	 * @return  bool    True if supported, false otherwise.
	 */
	public function isSupported(string $group)
	{
		return true;
	}

	/**
	 * Returns the value for the requested configuration option.
	 * 
	 * @param   string  $key  The option key.
	 * @param   mixed   $def  The default value in case of missing option.
	 * 
	 * @return  mixed   The option value.
	 */
	final public function getOption(string $key, $def = null)
	{
		if (isset($this->options[$key]) && $this->options[$key] !== '')
		{
			return $this->options[$key];
		}

		return $def;
	}

	/**
	 * Executes the rule.
	 *
	 * @param   mixed  $record  The record to dispatch.
	 *
	 * @return  void
	 */
	abstract public function execute($record);

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	final public function jsonSerialize()
	{
		// create empty object
		$json = new \stdClass;
		// register rule ID
		$json->id = $this->getID();
		// register rule name
		$json->name = $this->getName();
		// register rule description
		$json->description = $this->getDescription();
		// register empty groups
		$json->groups = [];

		// test restaurant group
		if ($this->isSupported('restaurant'))
		{
			$json->groups[] = 'restaurant';
		}

		// test take-away group
		if ($this->isSupported('takeaway'))
		{
			$json->groups[] = 'takeaway';
		}

		// test food group
		if ($this->isSupported('food') || $this->isSupported('tkfood'))
		{
			$json->groups[] = 'food';
		}

		return $json;
	}
}
