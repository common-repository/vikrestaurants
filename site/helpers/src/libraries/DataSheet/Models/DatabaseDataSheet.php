<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Models;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DataSheet\DataSheet;

/**
 * This abstraction can be used to scan the records from a database and create 
 * a datasheet accordingly.
 * 
 * @since 1.9
 */
abstract class DatabaseDataSheet implements DataSheet
{
	/** @var \JDatabaseDriver */
	protected $db;

	/**
	 * Class constructor.
	 * 
	 * @param  JDatabaseDriver  $db
	 */
	public function __construct($db = null)
	{
		if ($db)
		{
			$this->db = $db;	
		}
		else
		{
			$this->db = \JFactory::getDbo();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getBody()
	{
		// create a database body iterator to avoid exceeding the memory limit
		return new Iterators\DatabaseBodyIterator($this);
	}

	/**
	 * @inheritDoc
	 */
	public function getFooter()
	{
		// no footer supported by default
		return [];
	}

	/**
	 * Fetches the records according to the provided pagination.
	 * 
	 * Children classes can overwrite this method in case the items should
	 * be manipulated after the query.
	 * 
	 * @param   int  $offset  The limit offset.
	 * @param   int  $limit   The limit size. Use null to ignore the pagination.
	 * 
	 * @return  object[]
	 */
	public function fetchRecords(int $offset = 0, int $limit = null)
	{
		// register query
		$this->db->setQuery($this->getListQuery(), $offset, $limit);

		// fetch matching records
		return $this->db->loadObjectList();
	}

	/**
	 * Counts the total number of available records.
	 * It should be equals to the length of the array returned by `fetchRecords`
	 * with offset equals to 0 and a NULL limit.
	 * 
	 * @return  int
	 */
	public function countRecords()
	{
		$query = $this->getCountQuery();

		// register query
		$this->db->setQuery($query);

		if (is_string($query))
		{
			// String provided, count the records by executing the query.
			// Hope that the total result won't exceed the memory limit.
			return count($this->db->loadColumn());
		}

		// count records
		return (int) $this->db->loadResult();
	}

	/**
	 * Returns the query that should be used to count the records.
	 * 
	 * Children classes can overwrite this method in case the query to count the
	 * records should, in example, aggregate the records.
	 * 
	 * @return  mixed  Either a query string or a query builder object.
	 */
	protected function getCountQuery()
	{
		// get default list query without limits
		$query = $this->getListQuery();

		if (!is_string($query))
		{
			// clear default statements to obtain all the records
			$query->clear('select')->clear('order')->clear('offset')->clear('limit');
			// count the rows instead of returning them all
			$query->select('COUNT(1)');
		}

		return $query;
	}

	/**
	 * Converts the plain record received by the database into a datasheet row.
	 * 
	 * @param   object  $record  The database record.
	 * 
	 * @return  array   The datasheet row.
	 */
	public function formatRow(object $record)
	{
		return array_values((array) $record);
	}

	/**
	 * Returns the query that should be used to fetch the matching records.
	 * 
	 * @return  mixed  Either a query string or a query builder object.
	 */
	abstract protected function getListQuery();
}
