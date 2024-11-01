<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Services;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\Field;
use E4J\VikRestaurants\CustomFields\FieldService;

/**
 * VikRestaurants custom field pick up service implementation.
 *
 * @since 1.9
 */
class PickupService implements FieldService
{
	/**
	 * Flag used to check whether the value should be unset or not.
	 * 
	 * @var bool
	 */
	protected $clearValue;

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKORDERPICKUPOPTION');
	}

	/**
	 * @inheritDoc
	 */
	public function preflight(Field $field)
	{
		// check if the field is required only in case of delivery
		if ($field->get('service', '') === 'delivery')
		{
			// mark the field as optional in case of pick up
			$field->set('required', 0);

			// clear value because it is not needed to collect
			// a delivery field in case of pick up service
			$this->clearValue = true;
		}
		else
		{
			// reset clear value
			$this->clearValue = false;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function postflight(Field $field, &$value)
	{
		if ($this->clearValue)
		{
			// overwrite the value fetched by the input
			$value = '';
		}
	}
}
