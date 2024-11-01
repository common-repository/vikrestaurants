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

use E4J\VikRestaurants\Collection\ProviderCollection;

/**
 * @inheritDoc
 */
class DealsCollection extends ProviderCollection
{
	/**
	 * Creates a new instance by loading the deals from the database.
	 * 
	 * @param   JDatabaseDriver|null  $db
	 * 
	 * @return  self  A new collection instance.
	 */
	public static function getInstance($db = null)
	{
		if (!$db)
		{
			$db = \JFactory::getDbo();
		}

		// create provider to load the deals from the database
		$provider = new DealsDatabaseProvider($db);

		// aggregate provider into a decorator to support deals caching
		$provider = new DealsDatabaseCache($provider);

		// construct new deals collection
		return new static($provider);
	} 
}
