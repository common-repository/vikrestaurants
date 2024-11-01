<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\Field;

/**
 * VikRestaurants custom field delivery helper.
 *
 * @since 1.9
 */
trait DeliveryTrait
{
	/**
	 * Extends the delivery address with the provided value.
	 *
	 * @param   mixed  $value  The value of the field set in request.
	 * @param   array  &$args  The array data to fill-in in case of
	 *                         specific rules (name, e-mail, etc...).
	 * @param   Field  $field  The custom field object.
	 *
	 * @return  void
	 */
	protected function extendAddress($value, array &$args, Field $field)
	{
		if (!empty($args['purchaser_address']))
		{
			$args['purchaser_address'] .= ', ';
		}
		else
		{
			$args['purchaser_address'] = '';
		}

		// append purchaser address
		$args['purchaser_address'] .= $value;
	}
}
