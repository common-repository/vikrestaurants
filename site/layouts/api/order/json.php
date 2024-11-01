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

// clone the order because we don't want to keep
// the changes applied here
$order = clone $order;
// do not display templates
unset($order->template);

if (defined('JSON_PRETTY_PRINT'))
{
	// use pretty print to display the JSON in a
	// readable format
	$json = json_encode($order, JSON_PRETTY_PRINT);
}
else
{
	// pretty print not supported, it will be hard to
	// read the response without using an external parser
	$json = json_encode($order);
}
?>

<pre><?php echo htmlentities($json); ?></pre>
