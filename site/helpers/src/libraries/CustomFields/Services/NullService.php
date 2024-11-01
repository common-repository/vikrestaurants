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
 * Null-Pointer pattern for custom field service interface.
 *
 * @since 1.9
 */
class NullService implements FieldService
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function preflight(Field $field)
	{
		// do nothing
	}

	/**
	 * @inheritDoc
	 */
	public function postflight(Field $field, &$value)
	{
		// do nothing
	}
}
