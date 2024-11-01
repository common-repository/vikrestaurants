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
 * SQL backup export rule.
 * 
 * @since 1.0
 */
class SQLExportRule extends Rule
{
	/**
	 * An array of SQL statements.
	 * 
	 * @var string[]
	 */
	protected $queries = [];

	/**
	 * Class constructor.
	 * 
	 * @param  array  $queries
	 */
	public function __construct(array $queries)
	{
		// reset all the registered query
		$this->queries = [];
		
		foreach ($queries as $query)
		{
			// register query within the buffer
			$this->registerQuery($query);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getRule()
	{
		return 'sql';
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		return $this->queries;
	}

	/**
	 * Helper method used to register the query inside the buffer.
	 * 
	 * @param   string  $query  The query to register.
	 * 
	 * @return  void
	 */
	public function registerQuery(string $query)
	{
		/**
		 * @wponly
		 * 
		 * While escaping a SQL string, WordPress replaces any "%" character with a random hash.
		 * Normally that hash is automatically unescaped while executing the query, but in our case,
		 * since we are dumping the query for later use, we have to manually unescape it, otherwise
		 * the WordPress website that is going to import the dump won't be able to revert the hash
		 * back, because the latter changes at every page loading.
		 */
		if (\VersionListener::isWordpress())
		{
			$query = \JFactory::getDbo()->remove_placeholder_escape($query);
		}

		if (!preg_match("/;$/", $query))
		{
			$query .= ';';
		}

		$this->queries[] = $query;
	}
}
