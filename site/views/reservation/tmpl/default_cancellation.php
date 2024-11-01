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
 * Template file used to display the button used to request the
 * cancellation of a restaurant reservation or take-away order.
 *
 * @since 1.9
 */

$data = [
   /**
    * The object holding the reservation/order we want to cancel.
    * 
    * @var VREOrderWrapper
    */
   'order' => $this->reservation,

    /**
     * The task to reach to request the cancellation.
     * 
     * @var string
     */
    'task' => 'reservation.cancel',

     /**
     * Whether the cancellation reason is enabled or not (0: disabled, 1: optional, 2: mandatory).
     * 
     * @var int
     */
    'reason' => VREFactory::getConfig()->getUint('cancreason'),

    /**
     * An optional Item ID to use for URL rewriting.
     * 
     * @var int
     */
    'itemid' => $this->itemid,
];

/**
 * The login form is displayed from the layout below:
 * /components/com_vikrestaurants/layouts/blocks/order/cancellation.php (joomla)
 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/order/cancellation.php (wordpress)
 *
 * @since 1.9
 */
echo JLayoutHelper::render('blocks.order.cancellation', $data);
