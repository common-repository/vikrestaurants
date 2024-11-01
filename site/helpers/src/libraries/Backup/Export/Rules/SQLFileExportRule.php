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

use E4J\VikRestaurants\Backup\Export\Archive;

/**
 * SQL Backup file export rule, apposite for huge queries.
 * 
 * @since 1.9
 */
class SQLFileExportRule extends SQLExportRule
{
	/**
	 * The path of the file containing the export queries.
	 * 
	 * @var string
	 */
	private $path;

	/**
	 * The handler used to construct the archive.
	 * 
	 * @var Archive
	 */
	private $archive;

	/**
	 * An alias to be used for the name of the SQL file.
	 * It is usually equals to the name of the database table.
	 * 
	 * @var string
	 */
	private $alias;

	/**
	 * Class constructor.
	 * 
	 * @param  Archive  $archive  The handler used to construct the archive.
	 * @param  string   $alias    The name of the file.
	 * @param  array    $queries  An optional list of queries.
	 */
	public function __construct(Archive $archive, string $alias, array $queries = [])
	{
		$this->archive = $archive;
		$this->alias   = $alias;

		// init parent only after initializing the archive
		parent::__construct($queries);
	}

	/**
	 * @inheritDoc
	 */
	public function getRule()
	{
		return 'sqlfile';
	}

	/**
	 * Returns the rules instructions.
	 * 
	 * @return 	mixed
	 */
	public function getData()
	{
		// check whether the file has been saved
		if ($this->path)
		{
			// return an associative array specifying the file path
			// that contains all the export queries
			return [
				'path' => $this->path,
			];
		}

		// do not import empty files
		return null;
	}

	/**
	 * @inheritDoc
	 * 
	 * Overwrite the parent method to store the queries within a file instead
	 * of the local memory.
	 */
	public function registerQuery(string $query)
	{
		// register query through parent
		parent::registerQuery($query);

		if (!$this->queries)
		{
			// nothing to export
			return;
		}
		
		if (!$this->path)
		{
			// build file path only once
			$this->path = 'database/' . $this->alias . '.sql';
		}

		// create buffer to save
		$buffer = trim(implode("\n\n", $this->queries));
		$buffer = preg_replace("/\)\s*,\s*\(/", "),\n(", $buffer);
			
		// register SQL buffer into the archive
		$saved = $this->archive->addBuffer($buffer . "\n\n", $this->path);

		if (!$saved)
		{
			// an error occurred while writing the dump files
			throw new \Exception(sprintf('Unable to write dump into: %s', $this->path), 500);
		}

		// reset queries list to avoid duplicates
		$this->queries = [];
	}
}
