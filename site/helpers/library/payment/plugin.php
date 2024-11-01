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
 * Payment adpater used to allow third-party plugins to perform the payment process.
 *
 * The I/O of this class MUST be the same for all the E4J programs that support
 * extendable payment methods.
 *
 * @note  The class prefix is equals to the 3-letter name of the program,
 *        "VRE" in this case.
 *
 * @since 1.8.5
 * 
 * @deprecated 1.10  Use E4J\VikRestaurants\Payment\PaymentPlugin instead.
 */
class_alias('E4J\\VikRestaurants\\Payment\\PaymentPlugin', 'VREPaymentPlugin');
