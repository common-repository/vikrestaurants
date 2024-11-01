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
 * VikRestaurants APIs base user.
 * This class is used from the framework to connect the users.
 *
 * @since 1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\User instead. 
 */
class_alias('E4J\\VikRestaurants\\API\\User', 'UserAPIs');
