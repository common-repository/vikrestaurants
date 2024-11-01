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
 * VikRestaurants custom field address rule dispatcher.
 *
 * @since 1.9
 */
class AddressRule extends FieldRule
{
	use DeliveryTrait;

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE4');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		// in case of multiple fields with address rule, use only
		// the first specified one
		if (empty($args['billing_address']))
		{
			// fill address column with field value
			$args['billing_address'] = $value;
		}
		else
		{
			if (!empty($args['billing_address_2']))
			{
				$args['billing_address_2'] .= ', ';
			}
			else
			{
				$args['billing_address_2'] = '';
			}

			// register any additional value into the secondary field
			$args['billing_address_2'] .= $value;
		}

		// extend the delivery address with the specified value
		$this->extendAddress($value, $args, $field);
	}
}
