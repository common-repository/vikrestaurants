<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Database;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Trait used to quickly implement caching methods.
 * 
 * @see DatabaseCacheContainer
 * 
 * @since 1.9
 */
trait DatabaseCacheTrait
{
    /**
     * Lookup used to cache the entries pulled from the database, where the
     * key is equals to the entry identifier and the value is the entry itself.
     * 
     * @var array
     */
    private static $cache = [];

    /**
     * Obtains the cached entry, if any.
     * 
     * @param   mixed  $id  The entry identifier.
     * 
     * @return  mixed  The cached entry if exists, NULL otherwise.
     */
    protected function getCached(string $id)
    {
        return isset(static::$cache[$id]) ? static::$cache[$id] : null;
    }

    /**
     * Registers the specified entry into the cache.
     * 
     * @param   mixed  $id     The entry identifier.
     * @param   mixed  $entry  The entry to cache.
     * 
     * @return  void
     */
    protected function cache(string $id, $entry)
    {
        static::$cache[$id] = $entry;
    }
}
