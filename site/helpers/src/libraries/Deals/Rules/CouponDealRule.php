<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Deals\DealRule;

/**
 * Class used to apply the "Coupon" deal type.
 *
 * @since 1.8
 * @since 1.9  Renamed from DealRuleCoupon.
 */
class CouponDealRule extends DealRule
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'coupon';
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRTKDEALTYPE5');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRTKDEALTYPEDESC5');
	}

	/**
	 * @inheritDoc
	 */
	public function serve($cart, $deal)
	{
		// always false, coupon codes can be added into the cart only manually
		return false;
	}
}
