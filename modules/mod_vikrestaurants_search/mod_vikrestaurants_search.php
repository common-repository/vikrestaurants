<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_search
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
	'version' => '1.6',
];

$vik = VREApplication::getInstance();

$vik->addStyleSheet(VREMODULES_URI . 'mod_vikrestaurants_search/assets/mod_vikrestaurants_search.css', $options);
$vik->addStyleSheet(VREASSETS_URI . 'css/jquery-ui.min.css');

// since jQuery is a required dependency, the framework should be 
// invoked even if jQuery is disabled
$vik->loadFramework('jquery.framework');
$vik->addScript(VREASSETS_URI . 'js/jquery-ui.min.js');

/**
 * Load CSS environment.
 * 
 * @since 1.6
 */
JHtml::fetch('vrehtml.assets.environment');

/**
 * Use FontAwesome to display the icons.
 *
 * @since 1.5
 */
JHtml::fetch('vrehtml.assets.fontawesome');

/**
 * Use default style for <select> defined by VikRestaurants.
 *
 * @since 1.5
 */
$vik->addStyleSheet(VREASSETS_URI . 'css/input-select.css');

/**
 * Load VikRestaurants utils.
 *
 * @since 1.5.1
 */
JHtml::fetch('vrehtml.assets.utils');
$vik->addScript(VREASSETS_URI . 'js/vikrestaurants.js');

// get query string values

$last_values = VikRestaurantsSearchHelper::getViewHtmlReferences();

/**
 * Use language texts defined by the component instead of
 * creating duplicates translations.
 *
 * @since 1.5
 */
VikRestaurants::loadLanguage(JFactory::getLanguage()->getTag());

/**
 * Use the module ID instead the module_id parameters, which
 * is no longer available within the module settings.
 *
 * @since 1.6
 */
$module_id = VikRestaurantsSearchHelper::getID($module);

// load tmpl/default.php

require JModuleHelper::getLayoutPath('mod_vikrestaurants_search', $params->get('layout'));
