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
 * @inheritDoc
 * 
 * This abstraction simplify the possibility to cache the data obtained
 * through a dataset provider.
 */
abstract class DatasetCacheProvider implements DatasetProvider
{
	/** @var DatasetProvider */
	protected $provider;

	/**
	 * Class constructor.
	 * 
	 * @param  DatasetProvider  $provider  The provider to aggregate.
	 */
	public function __construct(DatasetProvider $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		// obtain cached items, if any
		$items = $this->getCached();

		if (!$items)
		{
			// invoke aggregated provider to obtain the dataset
			$items = $this->provider->getData();
			// cache the obtained data for later use
			$this->cache($items);
		}

		return $items;
	}

	/**
	 * Obtains the cached dataset, if any.
	 * 
	 * @return  Item[]|null  An array of cached objects, NULL otherwise.
	 */
	abstract protected function getCached();

	/**
	 * Registers the specified dataset into the cache.
	 * 
	 * @param   Item[]  $data
	 * 
	 * @return  void
	 */
	abstract protected function cache(array $data);
}
