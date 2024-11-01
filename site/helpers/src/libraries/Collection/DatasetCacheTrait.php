<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Trait used to quickly implement caching methods.
 * 
 * @see DatasetCacheProvider
 * 
 * @since 1.9
 */
trait DatasetCacheTrait
{
	/** @var Item[]|null */
	private static $items = null;

	/**
	 * Obtains the cached dataset, if any.
	 * 
	 * @return  Item[]|null  An array of cached objects, NULL otherwise.
	 */
	protected function getCached()
	{
		return static::$items;
	}

	/**
	 * Registers the specified dataset into the cache.
	 * 
	 * @param   Item[]  $data
	 * 
	 * @return  void
	 */
	protected function cache(array $data)
	{
		static::$items = $data;
	}
}
