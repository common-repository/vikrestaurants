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
 * Template file used to display the form to let the
 * customers can redeem a coupon code.
 *
 * @since 1.8
 */

$data = [
	/**
	 * The controller to reach to redeem the coupon code. The specified controller must
	 * provide a task (method) called "redeemcoupon".
	 *
	 * @var string
	 */
	'controller' => 'confirmres',

	/**
	 * An associative array containing the search arguments that should be preserved 
	 * within the form as hidden input.
	 * 
	 * @var array
	 */
	'args' => $this->args,

	/**
	 * The Item ID that will be used to route the URL used for SEF.
	 *
	 * @var int|null
	 */
	'itemid' => $this->itemid,
];

/**
 * This form is displayed from the layout below:
 * /components/com_vikrestaurants/layouts/blocks/couponform.php (joomla)
 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/couponform.php (wordpress)
 *
 * @since 1.9
 */
echo JLayoutHelper::render('blocks.couponform', $data);
