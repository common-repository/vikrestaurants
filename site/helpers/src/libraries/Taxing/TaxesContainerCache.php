<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Taxing;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Database\DatabaseCacheContainer;
use E4J\VikRestaurants\Database\DatabaseCacheTrait;

/**
 * Stores the taxes into a static cache.
 * 
 * @since 1.9
 */
class TaxesContainerCache extends DatabaseCacheContainer
{
    use DatabaseCacheTrait;
}
