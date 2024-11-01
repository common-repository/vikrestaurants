<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants custom field rules dispatcher.
 *
 * @since 1.9
 */
abstract class FieldRule
{
	/**
	 * Creates a new instance for the specified field rule.
	 *
	 * @param   string  $rule  The requested rule.
	 *
	 * @return  self    A new instance.
	 * 
	 * @throws  \Exception
	 */
	final public static function getInstance(string $rule)
	{
		// set up class name starting from rule identifier
		$classname = 'E4J\\VikRestaurants\\CustomFields\\Rules\\' . ucfirst($rule) . 'Rule';

		// attempt to load a default rule
		if (!class_exists($classname))
		{
			// unable to find a class for the specified type
			throw new \Exception(sprintf('Custom field [%s] rule class not found', $classname), 404);
		}

		// create instance
		$handler = new $classname();

		if (!$handler instanceof FieldRule)
		{
			// the class handler must inherit this class
			throw new \UnexpectedValueException(sprintf('Custom field [%s] rule is not a valid instance', $classname), 404);
		}

		return $handler;
	}

	/**
	 * Returns a unique ID for this rule.
	 *
	 * @return  string
	 */
	public function getID()
	{
		// obtain all namespace components and take the last one
		$chunks = preg_split("/\\\\/", get_class($this));
		$class  = array_pop($chunks);

		// create ID from class name
		return strtolower(preg_replace("/Rule$/i", '', $class));
	}

	/**
	 * Returns the name of the rule.
	 *
	 * @return  string
	 */
	public function getName()
	{
		$id  = $this->getID();
		$key = 'VRECUSTFIELDRULE' . strtoupper($id);

		// try to translate the given language definition
		$name = \JText::translate($key);

		if ($name === $key)
		{
			// translation not found, return plain ID
			return $id;
		}

		return $name;
	}

	/**
	 * Dispatches the field rule.
	 *
	 * @param   mixed  $value  The value of the field set in request.
	 * @param   array  &$args  The array data to fill-in in case of
	 *                         specific rules (name, e-mail, etc...).
	 * @param   Field  $field  The custom field object.
	 *
	 * @return  void
	 */
	abstract public function dispatch($value, array &$args, Field $field);

	/**
	 * Renders the field rule.
	 *
	 * @param   array   &$data  An array of display data.
	 * @param   Field   $field  The custom field object.
	 *
	 * @return  string  The HTML that will be used in place of the layout
	 *                  defined by the field. Omit this value to keep using
	 *                  the default HTML of the field.
	 */
	public function render(array &$data, Field $field)
	{
		// always define this method because the rules
		// might not have to display anything
		return '';
	}
}
