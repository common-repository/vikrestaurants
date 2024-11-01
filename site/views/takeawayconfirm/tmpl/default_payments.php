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
 * Template file used to display a list of payment
 * gateways available to leave a deposit.
 *
 * @since 1.8
 */

$data = [
	/**
	 * An associative array containing a list of payment methods.
	 *
	 * @var object[]
	 */
	'payments' => $this->payments,

	/**
	 * Whether the list should include the description of the payments.
	 * The description is visible by default.
	 * 
	 * @var bool
	 */
	// $showdesc => true,

	/**
	 * Whether the list should pre-select the provided payment ID.
	 * The first available payment method is pre-selected by default.
	 * 
	 * @var int|null
	 */
	// 'id_payment' => null,
];

/**
 * This form is displayed from the layout below:
 * /components/com_vikrestaurants/layouts/blocks/paymentmethods.php (joomla)
 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/paymentmethods.php (wordpress)
 *
 * @since 1.9
 */
echo JLayoutHelper::render('blocks.paymentmethods', $data);
