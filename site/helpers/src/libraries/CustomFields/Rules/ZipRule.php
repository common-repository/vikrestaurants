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
use E4J\VikRestaurants\CustomFields\Helpers\DeliveryTrait;

/**
 * VikRestaurants custom field ZIP code rule dispatcher.
 *
 * @since 1.9
 */
class ZipRule extends FieldRule
{
	use DeliveryTrait;

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE6');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		// in case of multiple fields with zip rule, use only
		// the first specified one
		if (empty($args['billing_zip']))
		{
			// fill zip column with field value
			$args['billing_zip'] = $value;
		}

		// extend the delivery address with the specified value
		$this->extendAddress($value, $args, $field);
	}
}
