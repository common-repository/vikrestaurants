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
 * VikRestaurants APIs base framework.
 * This class is used to run all the installed plugins in a given directory.
 * The classname of the plugins must follow the standard below:
 * e.g. File = plugin.php   		Class = Plugin
 * e.g. File = plugin_name.php   	Class = PluginName
 *
 * All the events are runnable only if the user is correctly authenticated.
 *
 * @since 1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\API instead.
 */
class_alias('E4J\\VikRestaurants\\API\\API', 'APIs');
