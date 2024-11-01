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

use E4J\VikRestaurants\CustomFields\Services\NullService;

/**
 * VikRestaurants custom fields requestor class.
 *
 * @since 1.9
 */
class FieldsRequestor
{
	/**
	 * The list of custom fields for which the data should be retrieved.
	 * 
	 * @var FieldsCollection
	 */
	protected $fields;

	/**
	 * The selected type of service.
	 * 
	 * @var FieldService
	 */
	protected $service;

	/**
	 * Class contructor.
	 * 
	 * @param  FieldsCollection   $fields
	 * @param  FieldService|null  $service
	 */
	public function __construct(FieldsCollection $fields, FieldService $service = null)
	{
		$this->fields = $fields;

		if ($service)
		{
			$this->service = $service;
		}
		else
		{
			// fallback to Null-Pointer pattern to preserve the same workflow
			$this->service = new NullService;
		}
	}

	/**
	 * Returns the values of the custom fields specified in the REQUEST.
	 *
	 * @param   array  &$args   The array data to fill-in in case of specific rules (name, e-mail, etc...).
	 * @param   bool   $strict  True to raise an error when a mandatory field is missing.
	 *
	 * @return  array  The lookup array containing the values of the custom fields.
	 *
	 * @throws  \Exception
	 */
	public function loadForm(array &$args = null, bool $strict = true)
	{
		$lookup = [];

		// return an empty list in case there are no published fields
		if (!count($this->fields))
		{
			return $lookup;
		}

		if (is_null($args))
		{
			$args = [];
		}

		foreach ($this->fields as $field)
		{
			// clone the field to avoid altering the global instance
			$field = clone $field;

			// get form field name
			$name = $field->getName();

			try
			{
				// invoke service preflight before validating and retrieving the input
				$this->service->preflight($field);

				// extract custom field from request
				$lookup[$name] = $field->save();

				// invoke service postflight to allow the manipulation of the received value
				$this->service->postflight($field, $lookup[$name]);

				// dispatch rule assigned to the custom field, if any
				Factory::dispatchRule($field, $lookup[$name], $args);
			}
			catch (\Exception $e)
			{
				if ($strict)
				{
					// propagate exception
					throw $e;
				}

				if (!isset($lookup[$name]))
				{
					// register field with an empty string in order
					// to have it filled in within the resulting array
					$lookup[$name] = '';
				}
			}
		}

		return $lookup;
	}
}
