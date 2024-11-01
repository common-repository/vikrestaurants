<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\DatasetCacheProvider;
use E4J\VikRestaurants\Collection\DatasetCacheTrait;

/**
 * Stores the deals into a static cache.
 * 
 * @since 1.9
 */
class DealsDatabaseCache extends DatasetCacheProvider
{
    use DatasetCacheTrait;
}
