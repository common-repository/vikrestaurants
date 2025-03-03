<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\Field;
use E4J\VikRestaurants\CustomFields\FieldRule;

/**
 * VikRestaurants custom field phone number rule dispatcher.
 *
 * @since 1.9
 */
class PhoneRule extends FieldRule
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE3');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		// in case of multiple fields with phone rule, use only
		// the first specified one
		if (empty($args['purchaser_phone']))
		{
			// fill phone column with field value
			$args['purchaser_phone'] = $value;

			$input = \JFactory::getApplication()->input;

			// get dial code
			$dial = $input->get($field->getID() . '_dialcode', null, 'string');

			if ($dial)
			{
				// register dial code
				$args['purchaser_prefix'] = $dial;
			}

			// get country code
			$country = $input->get($field->getID() . '_country', null, 'string');

			if ($country)
			{
				// register country code
				$args['purchaser_country'] = $country;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function render(array &$data, Field $field)
	{
		// use a different layout for fields with phone number rule
		$field->set('layout', 'tel');

		// inject class name
		$data['class'] = (empty($data['class']) ? '' : $data['class'] . ' ') . 'phone-field';
	}
}
