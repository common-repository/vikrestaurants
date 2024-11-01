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
 * Layout variables
 * -----------------
 * @var  mixed      $order  The order details.
 * @var  JRegistry  $args   The event arguments.
 */
extract($displayData);

VRELoader::import('library.mail.factory');

// get notification e-mail for admin
$adminMail = VREMailFactory::getInstance(
	$args->get('type') == 0 ? 'restaurant' : 'takeaway', 'admin',
	$order->id,
	$args->get('langtag')
);

// display e-mail template
echo $adminMail->getHtml();
