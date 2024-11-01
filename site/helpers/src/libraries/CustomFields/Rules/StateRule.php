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
 * VikRestaurants custom field state/province rule dispatcher.
 *
 * @since 1.9
 */
class StateRule extends FieldRule
{
	use DeliveryTrait;

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE8');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		// in case of multiple fields with state rule, use only
		// the first specified one
		if (empty($args['billing_state']))
		{
			// fill state column with field value
			$args['billing_state'] = $value;
		}

		// extend the delivery address with the specified value
		$this->extendAddress($value, $args, $field);
	}
}
