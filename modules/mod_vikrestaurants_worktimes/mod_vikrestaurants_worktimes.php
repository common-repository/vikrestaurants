<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_worktimes
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

// backward compatibility

$options = [
	'version' => '1.3',
];

$vik = VREApplication::getInstance();

$vik->addStyleSheet(VREMODULES_URI . 'mod_vikrestaurants_worktimes/assets/mod_vikrestaurants_worktimes.css', $options);
$vik->addStyleSheet(VREASSETS_URI . 'css/jquery-ui.min.css');

// since jQuery is a required dependency, the framework should be 
// invoked even if jQuery is disabled
$vik->loadFramework('jquery.framework');
$vik->addScript(VREASSETS_URI . 'js/jquery-ui.min.js');

/**
 * Load CSS environment.
 * 
 * @since 1.3
 */
JHtml::fetch('vrehtml.assets.environment');

/**
 * Get the working days for the specified group.
 *
 * @since 1.2
 */
$days = VikRestaurantsWorktimesHelper::getDaysWorkTimes($params);

/**
 * Use the module ID instead the module_id parameters, which
 * is no longer available within the module settings.
 *
 * @since 1.3
 */
$module_id = VikRestaurantsWorktimesHelper::getID($module);

// load tmpl/default.php

require JModuleHelper::getLayoutPath('mod_vikrestaurants_worktimes', $params->get('layout'));
