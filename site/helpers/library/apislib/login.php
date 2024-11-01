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
 * VikRestaurants APIs final user.
 * This class is used from the framework to connect the users and to authorise the events.
 *
 * @since 1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\Framework\BasicAuthUser instead.
 */
class_alias('E4J\\VikRestaurants\\API\\Framework\\BasicAuthUser', 'LoginAPIs');
