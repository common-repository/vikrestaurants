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
 * Template file used to display the button to go back to the
 * profile page of the logged-in user.
 *
 * @since 1.9
 */

$data = [
    /**
     * Display the back button only for registered users.
     * Force false in case you want to always disable the back button.
     * 
     * @var bool
     */
    'display' => JFactory::getUser()->guest == false,

    /**
     * The text to use for the button ("View All Orders" by default).
     * 
     * @var string
     */
    // 'text' => null,

    /**
     * An optional Item ID to use for URL rewriting.
     * 
     * @var int
     */
    'itemid' => $this->itemid,
];

/**
 * The login form is displayed from the layout below:
 * /components/com_vikrestaurants/layouts/blocks/order/backbutton.php (joomla)
 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/order/backbutton.php (wordpress)
 *
 * @since 1.9
 */
echo JLayoutHelper::render('blocks.order.backbutton', $data);
