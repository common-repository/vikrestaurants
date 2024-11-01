<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_cart
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

// require autoloader
if (defined('JPATH_SITE') && JPATH_SITE !== 'JPATH_SITE')
{
	require_once implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, 'components', 'com_vikrestaurants', 'helpers', 'library', 'autoload.php'));
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'helper.php';

$input = JFactory::getApplication()->input;

// backward compatibility

$options = [
	'version' => '1.6',
];

$vik = VREApplication::getInstance();

$vik->addStyleSheet(VREMODULES_URI . 'mod_vikrestaurants_takeaway_cart/assets/mod_vikrestaurants_takeaway_cart.css', $options);

// since jQuery is a required dependency, the framework should be 
// invoked even if jQuery is disabled
$vik->loadFramework('jquery.framework');
// load VikRestaurants utils
$vik->addScript(VREASSETS_URI . 'js/vikrestaurants.js');

/**
 * Auto set CSRF token to ajaxSetup so all jQuery ajax call will contain CSRF token.
 *
 * @since 1.6
 */
JHtml::fetch('vrehtml.sitescripts.ajaxcsrf');

/**
 * Load CSS environment.
 * 
 * @since 1.6
 */
JHtml::fetch('vrehtml.assets.environment');

// load JS currency helper
JHtml::fetch('vrehtml.assets.currency');

/**
 * Use FontAwesome to display the icons.
 *
 * @since 1.5
 */
JHtml::fetch('vrehtml.assets.fontawesome');

$itemid = $params->get('itemid', 0);

// setup environment vars
$TAKEAWAY_CONFIRM_URL = JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayconfirm' . ($itemid ? '&Itemid=' . $itemid : ''), false);

$_TAKEAWAY_ = 0;
$_TAKEAWAY_CONFIRM_ = 0;

if (in_array($input->get('view'), ['takeaway', 'takeawayitem']))
{
	$_TAKEAWAY_ = 1;
	// make cart scrollable for takeaway menus and takeaway item pages
	$vik->addScript(VREMODULES_URI . 'mod_vikrestaurants_takeaway_cart/assets/mod_vikrestaurants_takeaway_cart.js', $options);
}
else if ($input->get('view') == 'takeawayconfirm')
{
	$_TAKEAWAY_CONFIRM_ = 1;
}

/**
 * Use an helper method to calculate the minimum cost 
 * needed to proceed with the purchase.
 *
 * @since 1.5.2
 */
$minCostPerOrder = Vikrestaurants::getTakeAwayMinimumCostPerOrder();

/**
 * Obtain here the cart instance.
 * 
 * @since 1.6
 */
$cart = VikRestaurantsCartHelper::getCart();

/**
 * Get module ID.
 * 
 * @since 1.6
 */
$module_id = VikRestaurantsCartHelper::getID($module);

// load tmpl/default.php
require JModuleHelper::getLayoutPath('mod_vikrestaurants_takeaway_cart', $params->get('layout'));
