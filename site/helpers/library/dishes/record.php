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
 * Used to handle the restaurant dish into the database.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\OrderDishes\ItemRecord instead.
 */
class_alias('E4J\\VikRestaurants\\OrderDishes\\ItemRecord', 'VREDishesRecord');
