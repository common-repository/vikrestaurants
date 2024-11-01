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
 * VikRestaurants custom fields renderer class.
 *
 * @since 1.9
 */
class FieldsRenderer
{
	/**
	 * The list of custom fields to render.
	 * 
	 * @var FieldsCollection
	 */
	protected $fields;

	/**
	 * Instance used to rewrite the style of the fields wrapper.
	 *
	 * @var FieldControl|null
	 */
	protected $controlWrapper = null;

	/**
	 * Registers an additional path in which the layout files
	 * of the fields might be stored.
	 *
	 * @var string|null
	 */
	protected $fieldsLayoutPath = null;

	/**
	 * Class constructor
	 * 
	 * @param  FieldsCollection  $fields
	 */
	public function __construct(FieldsCollection $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * Returns the form HTML of the specified custom fields.
	 *
	 * @param   mixed   $data     Either an array or an object containing the values to bind.
	 * @param   array   $options  An array of display options.
	 *                            - strict  bool    True to take care of the "required" flag of the fields.
	 *                                              Use false to make all the fields optional.
	 *                            - suffix  string  An optional suffix to append to the fields ID.
	 *
	 * @return  string  The resulting HTML.
	 */
	public function display($data = [], array $options = [])
	{
		if (!isset($options['strict']))
		{
			// strict mode not specified, use default one
			$options['strict'] = true;
		}

		$form = '';

		// treat data to bind as array
		$data = (array) $data;

		foreach ($this->fields as $field)
		{
			// clone field to prevent updates on the global object
			$field = clone $field;

			if (isset($options['suffix']))
			{
				// include ID suffix
				$field->set('idsuffix', $options['suffix']);
			}

			// overwrite control wrapper
			$field->setControl($this->controlWrapper);

			// include a custom layout path
			$field->setLayoutPath($this->fieldsLayoutPath);

			// prepare display data
			$args = [];

			// obtain field value
			if (($value = $this->getValue($field, $data)) !== '')
			{
				// inject value only if not empty
				$args['value'] = $value;
			}

			if (!$options['strict'])
			{
				// ignore required status
				$args['required'] = false;
			}

			// Prevent the customers from editing a read-only custom field, which.
			// can be filled in only once. Ignore if we are not in strict mode.
			if ($options['strict'] && $field->get('readonly', false))
			{
				if (!$field->isCollectable())
				{
					// avoid displaying a non-collectable field more than once
					if (array_filter($data))
					{
						continue;
					}
				}
				else if (isset($args['value']))
				{
					// force hidden type to prevent the manipulation of the custom field
					$field->set('type', 'hidden');
					$field->set('layout', 'hidden');
					$field->set('rule', '');
				}
			}

			// render field
			$form .= $field->render($args);
		}

		return $form;
	}

	/**
	 * Checks whether there's at least an editable custom field.
	 *
	 * @param   mixed  $data  Either an array or an object containing the values to bind.
	 *
	 * @return  bool   True if available, false otherwise.
	 */
	public function hasEditableFields($data = [])
	{
		foreach ($this->fields as $field)
		{
			if ($field->get('readonly'))
			{
				// not marked as read-only, therefore there's at least
				// an editable custom field
				return true;
			}

			if (!$field->isCollectable())
			{
				// ignore fields that are not used to collect data
				continue;
			}

			// obtain field value
			$value = $this->getValue($field, $data);
			
			if ($value === '')
			{
				// this field haven't specified yet a value, therefore
				// the user is allowed to use it for the first time
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieves the value types for the specified custom field.
	 * 
	 * @param   Field   $field  The field instance.
	 * @param   mixed   $data   Either an array or an object containing the typed values.
	 * 
	 * @return  string  The typed value, if any.
	 */
	public function getValue(Field $field, $data = [])
	{
		if (!$field->isCollectable())
		{
			// ignore fields that are not used to collect data
			return '';
		}

		$data = (array) $data;

		$name = $field->getName();
		
		return isset($data[$name]) ? $data[$name] : '';
	}

	/**
	 * Sets the control handler to allow the style rewriting.
	 *
	 * @param   FieldControl|string|null  $control  The control handler, a layout string or null.
	 * @param   array  $options  An optional array to be passed in case $control is a string (layout file).
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setControl($control = null, array $options = [])
	{
		if (is_string($control))
		{
			// in case a custom layout path has been configured, pass it to the field control
			// for a correct rendering
			$base = $this->fieldsLayoutPath;

			// string received, create an anonymous class to render the control according
			// to the provided layout path
			$control = new class ($control, $options, $base) implements FieldControl {
				/** @var string */
				private $path;

				/** @var array */
				private $options;

				/** @var string|null */
				private $base;

				/**
				 * Class constructor.
				 * 
				 * @param  string       $path     The control layout path.
				 * @param  array        $options  An array of default display data.
				 * @param  string|null  $base     An optional base path for the layout.
				 */
				public function __construct(string $path, array $options = [], string $base = null)
				{
					$this->path    = $path;
					$this->options = $options;
					$this->base    = $base;
				}

				/**
				 * @inheritDoc
				 */
				public function render(array $data, string $input = '')
				{
					// inject input within the display data
					$data['input'] = $input;
					// render the control of the specified path
					return \JLayoutHelper::render($this->path, array_merge($this->options, $data), $this->base, [
						'component' => 'com_vikrestaurants',
					]);
				}
			};
		}

		$this->controlWrapper = $control;

		return $this;
	}

	/**
	 * Sets a custom path in which the system should search for
	 * the layout files to display.
	 *
	 * @param   string  $path  The folder path.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setLayoutPath(string $path = null)
	{
		$this->fieldsLayoutPath = $path;

		return $this;
	}
}
