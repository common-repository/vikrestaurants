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

// require only once the file containing all the defines
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'defines.php';

// if VRELoader does not exist, include it
if (!class_exists('VRELoader'))
{
	include VRELIB . DIRECTORY_SEPARATOR . 'loader' . DIRECTORY_SEPARATOR . 'loader.php';
	
	// append helpers folder to the base path
	VRELoader::$base .= DIRECTORY_SEPARATOR . 'helpers';
}

// register custom namespace for this component
JLoader::registerNamespace('E4J\\VikRestaurants', dirname(__FILE__) . '/libraries', false, false, 'psr4');

// fix filenames with dots
VRELoader::registerAlias('lib.vikrestaurants', 'lib_vikrestaurants');
VRELoader::registerAlias('pdf.constraints', 'constraints'); // this will be loaded specifically

// load factory
VRELoader::import('library.system.error');
VRELoader::import('library.system.factory');

// load adapters
VRELoader::import('library.adapter.version.listener');
VRELoader::import('library.adapter.application');
VRELoader::import('library.adapter.bc');

// load mvc
VRELoader::import('library.mvc.controller');
VRELoader::import('library.mvc.table');
VRELoader::import('library.mvc.view');
VRELoader::import('library.mvc.model');

// load helpers
VRELoader::import('library.availability.search');
VRELoader::import('library.availability.takeaway');
VRELoader::import('library.order.factory');
VRELoader::import('library.specialdays.manager');

/**
 * @deprecated 1.10  Use E4J\VikRestaurants\CustomFields\FieldsLoader instead.
 */
VRELoader::import('library.custfields.fields');

// load component helper
VRELoader::import('lib_vikrestaurants');

$app = JFactory::getApplication();

// configure HTML helpers
if ($app->isClient('administrator'))
{
	JHtml::addIncludePath(VREADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'html');
}

if ($app->isClient('site') || $app->input->get('option') !== 'com_vikrestaurants')
{
	// load admin models for front-end client or if we are outside the component
	JModelLegacy::addIncludePath(VREADMIN . DIRECTORY_SEPARATOR . 'models', 'VikRestaurantsModel');
}

JTable::addIncludePath(VREADMIN . DIRECTORY_SEPARATOR . 'tables');
JHtml::addIncludePath(VRELIB . DIRECTORY_SEPARATOR . 'html');

/**
 * Classes autoloader.
 *
 * The following class "VREFooBarBaz" will be
 * loaded from "site/helpers/library/foo/bar/baz.php".
 * 
 * @since 1.9
 * @deprecated 1.11 Use namespace autoloading.
 */
spl_autoload_register(function($class)
{
	$prefix = 'VRE';

	if (strpos($class, $prefix) !== 0)
	{
		// ignore if we are loading an outsider
		return false;
	}

	// remove prefix from class
	$tmp = preg_replace("/^{$prefix}/", '', $class);
	// separate camel-case intersections
	$tmp = preg_replace("/([a-z0-9])([A-Z])/", addslashes('$1' . DIRECTORY_SEPARATOR . '$2'), $tmp);

	// build path from which the class should be loaded
	$path = VRELIB . DIRECTORY_SEPARATOR . strtolower($tmp) . '.php';

	// make sure the file exists
	if (is_file($path))
	{
		// include file and check if the class is now available
		if ((include_once $path) && (class_exists($class) || interface_exists($class) || trait_exists($class)))
		{
			return true;
		}
	}

	return false;
});

/**
 * Composer autoloader.
 * 
 * @since 1.9
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

