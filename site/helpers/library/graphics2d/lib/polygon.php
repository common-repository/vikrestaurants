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
 * Helper class to handle Polygon shapes.
 * 
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\Graphics2D\Polygon2D instead.
 */
class_alias('E4J\\VikRestaurants\\Graphics2D\\Polygon2D', 'Polygon');
