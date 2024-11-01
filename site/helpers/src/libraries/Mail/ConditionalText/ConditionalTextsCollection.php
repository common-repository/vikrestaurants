<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\ProviderCollection;

/**
 * @inheritDoc
 */
class ConditionalTextsCollection extends ProviderCollection
{
	/**
	 * Creates a new instance by loading the conditional texts from
	 * the database.
	 * 
	 * @param   \JDatabaseDriver|null  $db
	 * 
	 * @return  self  A new collection instance.
	 */
	public static function getInstance($db = null)
	{
		if (!$db)
		{
			$db = \JFactory::getDbo();
		}

		// create provider to load the conditional texts from the database
		$provider = new ConditionalTextsDatabaseProvider($db);

		// aggregate provider into a decorator to support records caching
		$provider = new ConditionalTextsDatabaseCache($provider);

		// construct new conditional texts collection
		return new static($provider);
	} 
}
