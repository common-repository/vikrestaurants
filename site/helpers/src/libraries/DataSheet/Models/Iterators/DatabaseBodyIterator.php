<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Models\Iterators;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DataSheet\Models\DatabaseDataSheet;

/**
 * This iterator can be used to scan the records in a database without
 * exhausting the memory limit, as the rows are scanned as a sort of
 * pagination (50 items per time by default).
 * 
 * @since 1.9
 */
class DatabaseBodyIterator implements \Iterator, \Countable
{
	/**
	 * The database data sheet instance used to fetch the records.
	 * 
	 * @var DatabaseDataSheet
	 */
	protected $dbDataSheet;

	/**
	 * The current query offset.
	 * 
	 * @var int
	 */
	private $offset = 0;

	/**
	 * The number of elements to pull per query.
	 * 
	 * @var int
	 */
	private $limit;

	/**
	 * The total number of elements to pull.
	 * 
	 * @var int
	 */
	private $total;

	/**
	 * A temporary list containing the pagination elements.
	 * 
	 * @var array
	 */
	private $queue = [];

	/**
	 * The current index of the iterator.
	 */
	private $index = 0;

	/**
	 * Class constructor.
	 * 
	 * @param  DatabaseDataSheet  $dbDataSheet
	 * @param  int                $limit
	 */
	public function __construct(DatabaseDataSheet $dbDataSheet, int $limit = 50)
	{
		$this->dbDataSheet = $dbDataSheet;

		$this->limit = $limit;

		// immediately register the total number of available records
		$this->total = $this->dbDataSheet->countRecords();
	}

	/**
	 * Helper method used to populate the queue to pull.
	 * Every time the queue has been exhausted, the system will
	 * automatically populate it with the items on the next page.
	 * 
	 * @var array
	 */
	protected function pull()
	{
		// in case the queue is empty and the current page didn't reach the limit,
		// fetch the next page records
		if (!$this->queue && $this->offset < $this->total)
		{
			// set up the queue
			$this->queue = $this->dbDataSheet->fetchRecords($this->offset, $this->limit);

			// increase the offset
			$this->offset += $this->limit;
		}

		return $this->queue;
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Iterator
	 */
	public function rewind(): void
	{
		// restart from the beginning
		$this->index  = 0;
		$this->offset = 0;
		$this->queue  = [];
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Iterator
	 */
	#[\ReturnTypeWillChange]
	public function current()
	{
		// fetch the first item in the queue, if any
		$row = $this->pull()[0] ?? null;

		// format the row
		return $row ? $this->dbDataSheet->formatRow($row) : null;
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Iterator
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		// return the current iterator index
		return $this->index;
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Iterator
	 */
	public function next(): void
	{
		// increase iterator
		$this->index++;
		// remove the first element of the queue to move on
		array_shift($this->queue);
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Iterator
	 */
	public function valid(): bool
	{
		// make sure there are still other items to scan
		return (bool) $this->pull();
	}

	/**
	 * @inheritDoc
	 * 
	 * @see \Countable
	 */
	#[\ReturnTypeWillChange]
	public function count()
	{
		return $this->total;
	}
}
