<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Providers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\DatasetProvider;
use E4J\VikRestaurants\Collection\Item;

/**
 * Loads the records from a database and converts them into collectable items.
 * 
 * @since 1.9
 */
abstract class DatabaseProvider implements DatasetProvider
{
	/** @var JDatabaseDriver */
	protected $db;

	/**
	 * Class constructor.
	 * 
	 * @param  JDatabaseDriver
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		$items = [];

		foreach ($this->loadRecords() as $item)
		{
			// convert the object into a collectable item
			$mapped = $this->map($item);

			if (!$mapped instanceof Item)
			{
				// wrap the mapped data into a collection item
				$mapped = new Item($mapped);
			}
			
			$items[] = $mapped;
		}

		return $items;
	}

	/**
	 * Performs the query used to load the records from the database.
	 * 
	 * @return  object[]
	 */
	protected function loadRecords()
	{
		// obtain either a query builder or a string
		$query = $this->getQuery();

		// execute the query
		$this->db->setQuery($query);

		// load records from database as objects
		return $this->db->loadObjectList();
	}

	/**
	 * Creates the query used to load the records from the database.
	 * 
	 * @return  mixed  Either a SQL string or a query builder.
	 */
	abstract protected function getQuery();

	/**
	 * Converts a plain database record into a collectable object.
	 * 
	 * @return  Item
	 */
	abstract protected function map(object $item);
}
