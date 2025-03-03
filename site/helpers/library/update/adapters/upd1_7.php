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
 * Update adapter for com_vikrestaurants 1.7 version.
 *
 * This class can include update() and finalise().
 *
 * NOTE. do not call exit() or die() because the update won't be finalised correctly.
 * Return false instead to stop in anytime the flow without errors.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_7 instead.
 */
abstract class VikRestaurantsUpdateAdapter1_7 extends E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_7
{

}
