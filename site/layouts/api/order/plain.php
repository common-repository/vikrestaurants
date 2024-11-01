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
 * @var  mixed      $order    The order details.
 * @var  JRegistry  $args     The event arguments.
 * @var  boolean    $logo     True to show the logo, false otherwise.
 * @var  boolean    $company  True to show the restaurant name, false otherwise.
 * @var  boolean    $details  True to show the order details, false otherwise.
 * @var  boolean    $items    True to show the ordered items, false otherwise.
 * @var  boolean    $total    True to show the total lines, false otherwise.
 * @var  boolean    $billing  True to show the billing details, false otherwise.
 */

$displayData['logo']    = isset($displayData['logo'])    ? $displayData['logo']    : true;
$displayData['company'] = isset($displayData['company']) ? $displayData['company'] : true;
$displayData['details'] = isset($displayData['details']) ? $displayData['details'] : true;
$displayData['items']   = isset($displayData['items'])   ? $displayData['items']   : true;
$displayData['total']   = isset($displayData['total'])   ? $displayData['total']   : true;
$displayData['billing'] = isset($displayData['billing']) ? $displayData['billing'] : true;

// dispatch the sublayout able to handle the given group
echo $this->sublayout($displayData['args']->get('type') == 0 ? 'restaurant' : 'takeaway', $displayData);
