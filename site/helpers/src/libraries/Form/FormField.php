<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Form;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Abstract form field wrapper.
 * 
 * @since 1.9
 */
abstract class FormField
{
	/**
	 * Static counter used to avoid elements with duplicate IDs.
	 * 
	 * @var int
	 */
	protected static $increment = 0;

	/** @var \JObject */
	protected $options;

	/** @var \JObject */
	protected $data;

	/**
	 * Class constructor.
	 * 
	 * @param  array|object  $options  The field configuration.
	 */
	public function __construct($options = [])
	{
		$this->options = new \JObject;

		// properly construct the attributes with the apposite setter
		foreach ($options as $key => $value)
		{
			$this->setAttribute($key, $value);
		}
		
		// inject data attributes within the correct repository
		$this->data = new \JObject();
	}

	/**
	 * Sets a field attribute.
	 * 
	 * @param   string  $key    The attribute name.
	 * @param   mixed   $value  The attribute value.
	 * 
	 * @return  self
	 */
	final public function setAttribute(string $key, $value)
	{
		if (preg_match("/^control\.([a-zA-Z0-9\-_]+)$/", $key, $match))
		{
			// set control attribute
			$this->setControlAttribute($match[1], $value);
		}
		else
		{
			// set field attribute
			$this->options->set($key, $value);
		}

		return $this;
	}

	/**
	 * Sets an attribute to be used for the specified control.
	 * 
	 * @param   string  $key    The attribute name.
	 * @param   string  $value  The attribute value.
	 * 
	 * @return  self
	 */
	final public function setControlAttribute(string $key, $value)
	{
		// obtain control attributes
		$attrs = $this->options->get('control', []);

		if ($value === null)
		{
			// unset attribute
			unset($attrs[$key]);
		}
		else if (is_scalar($value))
		{
			// set attribute
			$attrs[$key] = $value;
		}
		else
		{
			// unable to deal with the provided type
			throw new \InvalidArgumentException('Cannot set control attribute of type ' . gettype($value));
		}

		// update control attributes
		$this->options->set('control', $attrs);

		return $this;
	}

	/**
	 * Sets a data attribute to be used for the specified field.
	 * 
	 * @param   string  $key    The data attribute name.
	 * @param   string  $value  The data attribute value.
	 * 
	 * @return  self
	 */
	final public function setData(string $key, $value)
	{
		if (is_array($value) || is_object($value))
		{
			// encode the value in JSON format
			$value = json_encode($value);
		}

		// update data attributes
		$this->data->set($key, $value);

		return $this;
	}

	/**
	 * Magic method used to set the field attributes by using the name as method.
	 * 
	 * @param   string  $name  The attribute name.
	 * @param   array   $args  The provided arguments.
	 * 
	 * @return  self
	 */
	public function __call($name, $args)
	{
		return $this->setAttribute($name, $args[0] ?? null);
	}

	/**
	 * Renders the field.
	 * 
	 * It is possible to pass a callback to this method to render the input on-the-fly.
	 * The callback can either return the HTML or echo it directly. The field options
	 * are always passed to the callback as argument.
	 * 
	 * @param   \Callable  $callback  The callback to invoke to render the input.
	 *                                If not specified, the input will be rendered
	 *                                according to the default layout of the field type.
	 * 
	 * @return  string     The field HTML.
	 * 
	 * @throws  \RuntimeException
	 */
	public function render($callback = null)
	{
		$input = null;

		// prepare options before rendering
		$this->prepareOptions();

		if ($this->options->get('type'))
		{
			// render input by using its default layout
			$input = $this->getInput();
		}

		if (!is_null($callback))
		{
			// render input by launching the received callback
			$input = $this->getInputCallback($callback, $input);
		}
		
		if (!$input)
		{
			if ($this->options->get('type'))
			{
				throw new \RuntimeException('Field [' . $this->options->get('type') . '] type not found', 404);
			}
			else
			{
				throw new \RuntimeException('Missing field type', 400);
			}
		}

		if ($this->options->get('type') === 'hidden' || $this->options->get('hidden') == true)
		{
			// return only the plain input in case of hidden field
			return $input;
		}

		// render control
		return $this->renderControl($input);
	}

	/**
	 * Directly render the field when this object is casted to a string.
	 * 
	 * @return  string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Renders the input by executing the specified callback.
	 * 
	 * @param   \Callable  $callback  The callback to invoke to render the input.
	 * @param   string     $input     The default rendered input.
	 * 
	 * @return  string     The input HTML.
	 * 
	 * @throws  \InvalidArgumentException
	 */
	final protected function getInputCallback($callback, $input)
	{
		if ($callback instanceof FormFieldRenderer)
		{
			// invoke the render method of the provided instance
			$callback = [$callback, 'render'];
		}

		// make sure the callback can be executed
		if (!is_callable($callback))
		{
			throw new \InvalidArgumentException('Cannot launch the specified form field callback', 500);
		}

		ob_start();
		// execute callback and obtain response
		$input = call_user_func_array($callback, [$this->options, $input]);
		// since the callback might have echoed the HTML, we need to
		// append the output buffer to the input string
		$input = ($input ?: '') . ob_get_contents();
		ob_end_clean();

		return $input;
	}

	/**
	 * Renders the input through the default registered layout.
	 * 
	 * @return  string  The input HTML.
	 */
	protected function getInput()
	{
		return \JLayoutHelper::render('form.fields.' . $this->options->get('type'), $this->options->getProperties());
	}

	/**
	 * Helper method used to prepare the options before rendering.
	 * 
	 * @return  void
	 */
	protected function prepareOptions()
	{
		$name = $this->options->get('name');
		$id   = $this->options->get('id', null);

		if (($id === null || $id === '') && $name)
		{
			// auto-generate ID from name
			$id = preg_replace("/[^a-zA-Z0-9\-_]+/", '_', $name);

			if ($this->options->get('multiple') || preg_match("/\[\]$/", $name))
			{
				// in case of multiple field, append an incremental number to the ID
				$id .= '_' . (++static::$increment);
			}

			if ($id)
			{
				$this->options->set('id', 'vre_formfield_' . $id);
			}
		}

		if ($this->options->get('multiple') && $name && !preg_match("/\[\]$/", $name))
		{
			// in case we have a multiple field and the name does not end with "[]", prepend it
			$this->options->set('name', $name . '[]');
		}

		if ($this->options->get('required'))
		{
			$class = $this->options->get('class', '');

			// do not add class more than once
			if (!preg_match("/\brequired\b/", $class))
			{
				// append "required" class
				$this->options->set('class', trim($class . ' required'));
			}
		}

		// always take the full control space in case of separator
		if ($this->options->get('type') === 'separator')
		{
			$this->options->set('hiddenLabel', true);
		}

		// set up data attributes
		if ($data = $this->data->getProperties())
		{
			$str = [];

			foreach ($data as $k => $v)
			{
				if ($v === null || $v === false)
				{
					// ignore data attribute
					continue;
				}

				if ($v === true)
				{
					$str[] = 'data-' . $k;
				}
				else
				{
					$str[] = 'data-' . $k . '="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '"';
				}
			}

			$this->options->set('data', implode("\n", $str));
		}
		else
		{
			$this->options->set('data', '');
		}
	}

	/**
	 * Renders the field control.
	 * 
	 * @param   string  $input  The input HTML.
	 * 
	 * @return  string  The field HTML.
	 */
	abstract protected function renderControl(string $input);
}
