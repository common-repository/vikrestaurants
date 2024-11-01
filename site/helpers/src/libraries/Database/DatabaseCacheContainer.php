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

use E4J\VikRestaurants\Psr\Container\ContainerInterface;

/**
 * @inheritDoc
 * 
 * This abstraction simplify the possibility to cache the data obtained
 * from the database.
 */
abstract class DatabaseCacheContainer implements ContainerInterface
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * Class constructor.
     * 
     * @param  ContainerInterface  $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        // obtain cached entry, if any
        $item = $this->getCached($id);

        if (!$item)
        {
            // invoke aggregated container to obtain the entry
            $item = $this->container->get($id);
            // cache the obtained entry for later use
            $this->cache($id, $item);
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id)
    {
        return $this->getCached($id) || $this->container->has($id);
    }

    /**
     * Obtains the cached entry, if any.
     * 
     * @param   mixed  $id  The entry identifier.
     * 
     * @return  mixed  The cached entry if exists, NULL otherwise.
     */
    abstract protected function getCached(string $id);

    /**
     * Registers the specified entry into the cache.
     * 
     * @param   mixed  $id     The entry identifier.
     * @param   mixed  $entry  The entry to cache.
     * 
     * @return  void
     */
    abstract protected function cache(string $id, $entry);
}
