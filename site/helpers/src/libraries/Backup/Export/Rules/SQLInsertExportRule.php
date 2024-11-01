<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Export\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Backup\Export\Rule;

/**
 * SQL Backup mass insert export rule.
 * 
 * @since 1.9
 */
class SQLInsertExportRule extends Rule
{
	/**
	 * The SQL rule that will be used to collect the export statements.
	 * 
	 * @var SQLRule
	 */
	protected $sqlRule;

	/**
	 * The database table.
	 * 
	 * @var string
	 */
	protected $table;

	/**
	 * The information of the database columns.
	 * 
	 * @var array
	 */
	private $columns;

	/**
	 * Indicates the maximum number of rows under the same INSERT.
	 * 
	 * @var int
	 */
	private $maxRowsPerInsert = 100;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $table
	 */
	public function __construct(SQLExportRule $sqlRule, string $table)
	{
		$this->sqlRule = $sqlRule;

		$db = \JFactory::getDbo();

		// register table
		$this->table = $table;

		// get all the columns of the table to export
		$this->columns = $db->getTableColumns($this->table, $typeOnly = false);

		// get total count of records
		$count = $this->getCount();

		if (!$count)
		{
			// nothing to export
			return;
		}

		// get current database prefix
		$prefix = $db->getPrefix();

		// shows the table CREATE statement that creates the given table
		$createLookup = $db->getTableCreate($this->table);

		// check whether the current drivers supports a tool to return the statement that
		// was used to create the database table
		if (isset($createLookup[$this->table]))
		{
			// extract statement from create lookup and replace prefix
			$create = preg_replace("/`{$prefix}(vikrestaurants_(?:[a-z0-9_]+))`/i", '`#__$1`', $createLookup[$this->table]);

			// register query to recreate the table from scratch
			$this->sqlRule->registerQuery("DROP TABLE IF EXISTS `{$this->table}`");
			$this->sqlRule->registerQuery($create);

			// we don't need to alter the auto increment because it is already included
			// within the create table statement
			$alter_auto_increment = false;
		}
		else
		{
			// cannot fetch create table statment, truncate the table and assume (or "hope")
			// that the database structure is the same
			$this->sqlRule->registerQuery("TRUNCATE TABLE `$this->table`");

			// update auto increment after copying all the records
			$alter_auto_increment = true;
		}

		$insertQuery = $db->getQuery(true);

		// prepare INSERT query
		$insertQuery->insert($db->qn($this->table));

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		/**
		 * Trigger event to allow third party plugins to choose what are the columns to dump
		 * and whether the table should be skipped or not.
		 * 
		 * Fires while attaching a rule to dump some SQL statements.
		 * 
		 * @param   array   &$columns  An associative array of supported database table columns,
		 *                             where the key is the column name and the value is a nested
		 *                             array holding the column information.
		 * @param   string  $table     The name of the database table.
		 * 
		 * @return  bool    False to avoid including the table into the backup.
		 * 
		 * @since   1.9
		 */
		$result = $dispatcher->filter('onBeforeBackupDumpSqlVikRestaurants', [&$this->columns, $this->table]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		if ($result->isFalse())
		{
			// a third-party plugin decided to skip the table
			return;
		}

		// iterate the columns
		foreach ($this->columns as $column => $type)
		{
			$insertQuery->columns($db->qn($column));
		}

		// create SELECT query
		$selectQuery = $db->getQuery(true)->select('*')->from($db->qn($this->table));

		$offset = 0;

		while ($offset < $count)
		{
			$db->setQuery($selectQuery, $offset, $this->maxRowsPerInsert);
			$rows = $db->loadObjectList();

			// clear previous values
			$insertQuery->clear('values');

			foreach ($rows as $row)
			{
				$values = [];

				foreach ($this->columns as $k => $type)
				{
					if (!isset($row->{$k}))
					{
						// use NULL operator
						$values[] = 'NULL';
					}
					else
					{
						// escape the value
						$values[] = $db->q($row->{$k});
					}
				}

				$insertQuery->values(implode(',', $values));
			}

			// register query
			$this->sqlRule->registerQuery((string) $insertQuery);

			// increase offset
			$offset += $this->maxRowsPerInsert;

			// free space
			unset($rows);
		}

		// fetch table auto increment
		if ($alter_auto_increment && ($ai = $this->getAutoIncrement($this->table)))
		{
			$this->sqlRule->registerQuery("ALTER TABLE `{$this->table}` AUTO_INCREMENT = {$ai}");
		}
	}

	/**
	 * @inheritDoc
	 * 
	 * Preserve the same rule identifier provided by the passed SQL export rule.
	 */
	public function getRule()
	{
		return $this->sqlRule->getRule();
	}

	/**
	 * @inheritDoc
	 * 
	 * Preserve the same rule data provided by the passed SQL export rule.
	 */
	public function getData()
	{
		return $this->sqlRule->getData();
	}

	/**
	 * Counts the total number of rows inside the table.
	 * 
	 * @return  int
	 */
	private function getCount()
	{
		$db = \JFactory::getDbo();

		// count rows
		$q = $db->getQuery(true)->select('COUNT(1)')->from($db->qn($this->table));

		$db->setQuery($q);
		$db->execute();

		return (int) $db->loadResult();
	}

	/**
	 * Fetches the correct auto increment to set for the given table.
	 * 
	 * @return  mixed  The auto increment on success, null otherwise.
	 */
	private function getAutoIncrement()
	{
		$db = \JFactory::getDbo();

		$pk = null;

		// look for the primary key
		foreach ($this->columns as $column => $info)
		{
			if ($info->Extra === 'auto_increment')
			{
				$pk = $column;
			}
		}

		if (!$pk)
		{
			// no primary keys with auto increment
			return null;
		}

		// fetch highest ID
		$q = $db->getQuery(true)->select('MAX(' . $db->qn($pk) . ')')->from($db->qn($this->table));

		$db->setQuery($q);
		$db->execute();

		return (int) $db->loadResult() + 1;
	}
}
