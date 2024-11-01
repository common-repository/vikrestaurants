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

use E4J\VikRestaurants\Collection\Item;

/**
 * VikRestaurants custom field holder.
 *
 * @since 1.9
 */
abstract class Field extends Item
{
	/**
	 * Creates a new instance for the specified custom field.
	 *
	 * @param   mixed  $field  Either an array or an object holding the details
	 *                         of the custom field.
	 *
	 * @return  self   A new custom field instance.
	 * 
	 * @throws  \Exception
	 */
	final public static function getInstance($field)
	{
		if (is_string($field))
		{
			$field = array('type' => $field);
		}
		else
		{
			$field = (array) $field;
		}

		if (empty($field['type']))
		{
			// the type is mandatory in order to fetch the correct instance
			throw new \Exception('Missing custom field type', 400);
		}

		/**
		 * Trigger hook to allow external plugins to include new types of custom
		 * fields that have been implemented out of this project. Plugins must
		 * include here the file holding the class of the field type.
		 *
		 * @param   string  $type  The requested custom field type.
		 *
		 * @return  string  The classname of the object.
		 *
		 * @since   1.9
		 */
		$result = \VREFactory::getPlatform()->getDispatcher()->filter('onLoadCustomField', [$field['type']]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		// take the first available one		
		$classname = $result->first();

		if (!$classname)
		{
			// set up class name starting from rule identifier
			$classname = 'E4J\\VikRestaurants\\CustomFields\\Types\\' . ucfirst($field['type']) . 'Field';
		}

		if (!class_exists($classname))
		{
			// unable to find a class for the specified type
			throw new \Exception(sprintf('Custom field [%s] class not found', $classname), 404);
		}

		// create instance
		$handler = new $classname($field);

		if (!$handler instanceof Field)
		{
			// the class handler must inherit this class
			throw new \UnexpectedValueException(sprintf('Custom field [%s] is not a valid instance', $classname), 404);
		}

		return $handler;
	}

	/**
	 * Returns the form field ID.
	 *
	 * @return  string
	 */
	public function getID()
	{
		$id = $this->get('id', '');

		if (!$id)
		{
			// ID not provided, use the name
			return $this->get('name', '');
		}

		// build default ID
		return 'vrcf' . $this->get('id', 0);
	}

	/**
	 * Returns the form field attribute ID.
	 *
	 * @return  string
	 */
	public function getFormID()
	{
		// extract suffix from object, useful in case the custom fields have to be
		// displayed more than once within the same page
		$suffix = preg_replace("/[^a-zA-Z0-9_\-]+/", '', $this->get('idsuffix', ''));

		// build default ID
		return 'vrcf' . $suffix . $this->get('id', $this->get('name', 0));
	}

	/**
	 * Returns the form field name.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the name of the field type.
	 *
	 * @return  string
	 */
	public function getType()
	{
		$type = $this->get('type');
		$key  = 'VRECUSTOMFTYPEOPTION' . strtoupper($type);

		// try to translate the given language definition
		$name = \JText::translate($key);

		if ($name === $key)
		{
			// translation not found, return plain type
			return $type;
		}

		return $name;
	}

	/**
	 * Whether this field can be used to collect data or not.
	 * 
	 * @return  bool
	 */
	public function isCollectable()
	{
		return true;
	}

	/**
	 * Children classes can override this method to alter the
	 * value to display.
	 *
	 * @param   mixed   $value  The value stored within the database.
	 *
	 * @param   string  A readable text.
	 */
	public function getReadableValue($value)
	{
		if (is_array($value))
		{
			// join values
			return implode(', ', $value);
		}

		// return plain value by default
		return (string) $value;
	}

	/**
	 * Loads the input from the request and returns the
	 * manipulated value, ready for saving.
	 *
	 * @return  mixed  A scalar value of the custom field.
	 */
	final public function save()
	{
		// extract value from request
		$value = $this->extract();

		// validate field value
		if (!$this->validate($value))
		{
			// raise an error, the custom field is not valid
			throw new \Exception(\JText::translate('VRERRINSUFFCUSTF'));
		}

		if (!is_null($value) && !is_scalar($value))
		{
			// cannot accept non-scalar values, JSON encode them
			$value = json_encode($value);
		}

		return $value;
	}

	/**
	 * Extracts the value of the custom field and applies any
	 * sanitizing according to the settings of the field.
	 *
	 * @return  mixed  A scalar value of the custom field.
	 */
	protected function extract()
	{
		$app = \JFactory::getApplication();

		// treat by default as string
		return $app->input->get($this->getID(), '', 'string');
	}

	/**
	 * Validates the field value.
	 *
	 * @param   mixed  $value  The field raw value.
	 *
	 * @return  bool   True if valid, false otherwise.
	 */
	protected function validate($value)
	{
		if ((int) $this->get('required') == 0)
		{
			// always return true in case of optional field
			return true;
		}

		// make sure the value is not empty
		return (is_array($value) && count($value)) || strlen((string) $value);
	}

	/**
	 * Sets a custom path in which the system should search for
	 * the layout files to display.
	 *
	 * @param   string  $path  The folder path.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setLayoutPath($path = null)
	{
		$this->set('layoutpath', $path);

		return $this;
	}

	/**
	 * Renders the input field.
	 *
	 * @param   array   $data  An array of display data.
	 *
	 * @return  string  The resulting HTML.
	 */
	public function render(array $data = array())
	{
		// prepare layout data
		$data = $this->getDisplayData($data);

		// try to dispatch the assigned rule to render a different layout
		$input = Factory::renderRule($this, $data);

		if (!$input)
		{
			// rules didn't define a specific layout, fallback to the default one
			$input = $this->getInput($data);
		}

		// do not display the field control in case of hidden input
		if ($this->get('type') === 'hidden' || $this->get('hidden'))
		{
			return $input;
		}

		// create field control
		return $this->getControl($data, $input);
	}

	/**
	 * Returns the HTML of the field input.
	 *
	 * @param   array   $data  An array of display data.
	 *
	 * @return  string  The HTML of the input.
	 */
	protected function getInput($data)
	{
		// attempt to use the specified layout, otherwise
		// use the layout related to the given field type
		$layout = $this->get('layout', $this->get('type'));

		// create layout file
		$layoutFile = new \JLayoutFile('form.fields.' . $layout, null, [
			// force the option to "com_vikrestaurants" as the layout file might
			// be also displayed by a module and so we need to properly access the
			// override files of this component
			'component' => 'com_vikrestaurants',
		]);

		if ($path = $this->get('layoutpath'))
		{
			// search layout files also within the specified path
			$layoutFile->addIncludePath($path);
		}

		// render input field
		return $layoutFile->render($data);
	}

	/**
	 * Sets the control handler to allow the style rewriting.
	 *
	 * @param   FieldControl  $control  The handler or null.
	 *
	 * @return  self  This instance to support chaining.
	 */
	public function setControl(FieldControl $control = null)
	{
		$this->set('control', $control);

		return $this;
	}

	/**
	 * Returns the HTML of the field.
	 *
	 * @param   array   $data   An array of display data.
	 * @param   string  $input  The HTML of the input to wrap.
	 *
	 * @return  string  The HTML of the input.
	 */
	protected function getControl(array $data, string $input = '')
	{
		// check if we should use a specific wrapper
		$control = $this->get('control', null);

		// render input if not specified
		$input = is_null($input) ? $this->getInput($data) : $input;

		if ($control instanceof FieldControl)
		{
			// prepare control array
			$data['control'] = $data['control'] ?? [];

			foreach ($data as $k => $v)
			{
				if (!preg_match("/^control\.(.*?)$/", $k, $match))
				{
					continue;
				}

				// detected "control." attribute, inject it within the correct slot
				$data['control'][$match[1]] = $v;
				unset($data[$k]);
			}

			// use custom renderer
			$html = $control->render($data, $input);
		}
		else
		{
			/**
			 * @todo With this solution, the system is currently unable to render the control from a different layout path.
			 *       Therefore, we should study a solution to inject "layoutpath" within the form factory.
			 */

			// display control by using the platform form factory renderer
			$html = \VREFactory::getPlatform()->getFormFactory()->createField($data)->render(function($data) use ($input) {
				return $input;
			});
		}

		return $html;
	}

	/**
	 * Returns an array of display data.
	 *
	 * @param   array   $data  An array of display data.
	 *
	 * @return  array
	 */
	protected function getDisplayData(array $data)
	{
		$data['value']       = isset($data['value']) ? $data['value'] : $this->get('value', '');
		$data['label']       = $this->get('langname');
		$data['name']        = $this->getID();
		$data['id']          = !empty($data['id']) ? $data['id'] : $this->getFormID();
		$data['description'] = isset($data['description']) ? $data['description'] : $this->get('description');
		$data['field']       = $this->getProperties();
		$data['required']    = isset($data['required']) ? (bool) $data['required'] : $this->get('required', false);
		$data['class']       = isset($data['class']) ? $data['class'] : '';

		// add class to recognize custom fields
		$data['class'] = trim('custom-field' . ' ' . $data['class']);

		if ($data['required'])
		{
			$data['class'] .= ' required';
		}

		// register class control
		$data['control.class'] = 'control-custom-field';

		// fetch rule identifier
		$rule = preg_replace("/[^a-zA-Z0-9_\-]+/", '', (string) $this->get('rule'));

		if ($rule)
		{
			// register rule identifier within the class attribute
			$data['class']         .= ' field-' . $rule;
			$data['control.class'] .= ' control-' . $rule;
		}

		// fetch service identifier
		$service = preg_replace("/[^a-zA-Z0-9_\-]+/", '', (string) $this->get('service'));

		if ($service)
		{
			// register service identifier within the class attribute
			$data['class']         .= ' service-' . $service;
			$data['control.class'] .= ' control-service-' . $service;
		}

		if ($this->get('multiple'))
		{
			// set multiple attribute
			$data['multiple'] = true;

			// normalize name to support arrays
			if (!preg_match("/\[\]$/", $data['name']))
			{
				$data['name'] .= '[]';
			}

			if (is_string($data['value']) && preg_match("/^\[/", $data['value']))
			{
				// JSON decode stored value
				$data['value'] = json_decode($data['value']);
			}
			else
			{
				// attempt to cast the value as fallback
				$data['value'] = $data['value'] ? (array) $data['value'] : array();
			}
		}

		// make ID safe
		$data['id'] = preg_replace("/[^a-zA-Z0-9_\-]+/", '_', $data['id']);

		// look for any configured options
		$options = $this->get('options');

		if ($options && is_array($options))
		{
			if (!isset($data['options']))
			{
				// use the provided options
				$data['options'] = $options;
			}
			else
			{
				// merge to the existing options
				$data['options'] = array_merge($data['options'], $options);
			}
		}

		return $data;
	}

	/**
	 * Returns an array of field settings.
	 *
	 * @return  array
	 */
	protected function getSettings()
	{
		return (array) json_decode($this->get('choose', '{}'), true);
	}
}
