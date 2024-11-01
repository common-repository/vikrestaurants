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
 * VikRestaurants custom field e-mail rule dispatcher.
 *
 * @since 1.9
 */
class EmailRule extends FieldRule
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE2');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		// in case of multiple fields with e-mail rule, use only
		// the first specified one
		if (empty($args['purchaser_mail']))
		{
			// fill e-mail column with field value
			$args['purchaser_mail'] = $value;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function render(array &$data, Field $field)
	{
		// use a different type for input with e-mail rule
		$data['type'] = 'email';

		// inject class name
		$data['class'] = (empty($data['class']) ? '' : $data['class'] . ' ') . 'mail-field';
	}
}
