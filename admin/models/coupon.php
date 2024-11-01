<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants coupon model.
 *
 * @since 1.9
 */
class VikRestaurantsModelCoupon extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed   $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                        If not set the instance property value is used.
	 * @param   bool    $new  True to return an empty object if missing.
	 *
	 * @return  mixed   The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		// load item through parent
		$coupon = parent::getItem($pk, $new);

		if (!$coupon)
		{
			return null;
		}

		if (!$coupon->id)
		{
			// use random code
			$coupon->code = VikRestaurants::generateSerialCode(12, 'coupon');

			// since this method might be used from the front-end, we should auto-load the admin helper
			if (!class_exists('VikRestaurantsHelper'))
			{
				VRELoader::import('helpers.vikrestaurants', VREADMIN);
			}

			// pre-select the default group
			$coupon->group = VikRestaurantsHelper::getDefaultGroup();
		}

		return $coupon;
	}

	/**
	 * Marks the specified coupon as used.
	 * In addition, removes the coupon if it should be deleted once
	 * the maximum number of usages is reached.
	 * 
	 * @param   mixed  $coupon  Either a coupon code or an array/object.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function redeem($coupon)
	{
		if (is_string($coupon))
		{
			// coupon code given, recover details
			$coupon = $this->getItem(['code' => $coupon]);
		}
		else
		{
			// cast to object
			$coupon = (object) $coupon;
		}

		if (!$coupon || empty($coupon->id))
		{
			// invalid coupon
			return false;
		}

		// increase total usages
		$coupon->usages++;

		// check whether we reached the maximum number of usages, the coupon
		// is a GIFT and it should be removed from the system
		if ($coupon->maxusages - $coupon->usages <= 0 && $coupon->remove_gift && $coupon->type == 2)
		{
			// delete coupon ID
			$result = $this->delete($coupon->id);
		}
		else
		{
			// prepare save data
			$data = [
				'id'     => $coupon->id,
				'usages' => $coupon->usages,
			];

			// commit changes
			$result = (bool) $this->save($data);
		}

		return $result;
	}

	/**
	 * Restores the number of usages by one.
	 * 
	 * @param   mixed  $coupon  Either a coupon code or an array/object.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function unredeem($coupon)
	{
		if (is_string($coupon))
		{
			// coupon code given, recover details
			$coupon = $this->getItem(['code' => $coupon]);
		}
		else
		{
			// cast to object
			$coupon = (object) $coupon;
		}

		if (!$coupon || empty($coupon->id))
		{
			// invalid coupon
			return false;
		}

		// decrease total usages
		$coupon->usages--;


		// prepare save data
		$data = [
			'id'     => $coupon->id,
			'usages' => max(0, $coupon->usages),
		];

		// commit changes
		return (bool) $this->save($data);
	}
}
