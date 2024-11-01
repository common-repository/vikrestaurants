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
 * VikRestaurants custom field nominative rule dispatcher.
 *
 * @since 1.9
 */
class NominativeRule extends FieldRule
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE1');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		if (!empty($args['purchaser_nominative']))
		{
			$args['purchaser_nominative'] .= ' ';
		}
		else
		{
			$args['purchaser_nominative'] = '';
		}

		// append value to nominative column
		$args['purchaser_nominative'] .= $value;
	}
}
