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
 * VikRestaurants custom field delivery notes rule dispatcher.
 *
 * @since 1.9
 */
class DeliverynotesRule extends NotesRule
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRCUSTFIELDRULE11');
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($value, array &$args, Field $field)
	{
		// always append delivery notes also to the reservation notes
		parent::dispatch($value, $args, $field);

		if (!empty($args['delivery_notes']))
		{
			$args['delivery_notes'] .= "\n";
		}
		else
		{
			$args['delivery_notes'] = '';
		}

		// append delivery notes
		$args['delivery_notes'] .= $value;
	}
}
