<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_cart
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class used by the Cart module.
 *
 * @since 1.6
 */
class VikRestaurantsCartHelper
{
	/**
	 * Use methods defined by modules trait for a better reusability.
	 *
	 * @see E4J\VikRestaurants\Module\ModuleHelper
	 */
	use E4J\VikRestaurants\Module\ModuleHelper;

	/**
	 * Returns the current cart instance.
	 *
	 * @return E4J\VikRestaurants\TakeAway\Cart
	 */
	public static function getCart()
	{
		// get cart model
		JModelLegacy::addIncludePath(VREBASE . DIRECTORY_SEPARATOR . 'models');
		$model = JModelVRE::getInstance('tkcart');

		// access cart instance
		return $model->getCart();
	}
}
