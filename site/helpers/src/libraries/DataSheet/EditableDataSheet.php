<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * This aggregator takes in input a datasheet to allow the customization of the
 * columns to hide/show.
 * 
 * @since 1.9
 */
class EditableDataSheet implements DataSheet
{
	/**
	 * The datasheet to aggregate.
	 * 
	 * @var DataSheet
	 */
	protected $dataSheet;

	/**
	 * The index of the columns that should be displayed.
	 * An empty list will take all the columns.
	 * 
	 * @var int[]
	 */
	protected $columns;

	/**
	 * A custom title to use for the datasheet.
	 * If not specified, the default one will be used.
	 * 
	 * @var string
	 */
	protected $title;

	/**
	 * Class constructor.
	 * 
	 * @param  DataSheet  $dataSheet
	 * @param  array      $columns
	 */
	public function __construct(DataSheet $dataSheet, array $columns = [])
	{
		$this->dataSheet = $dataSheet;

		if ($columns)
		{
			// take the provided columns
			$this->columns = array_map('intval', $columns);
		}
		else
		{
			// accept any column
			$this->columns = array_keys($this->dataSheet->getHead());
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return $this->title ?: $this->dataSheet->getTitle();
	}

	/**
	 * Changes the datasheet title.
	 * 
	 * @param   string  $title  The datasheet title.
	 * 
	 * @return  void
	 */
	public function setTitle(string $title)
	{
		$this->title = $title;
	}

	/**
	 * Shows the column at the specified index.
	 * 
	 * @param   int   $index  The index of the column to show.
	 * 
	 * @return  bool  True if shown, false in case the columns was already visible.
	 */
	public function show(int $index)
	{
		// make sure the array does not contain it yet
		if (!in_array($index, $this->columns))
		{
			// register column index
			$this->columns[] = $index;

			return true;
		}

		// index already available
		return false;
	}

	/**
	 * Hides the column at the specified index.
	 * 
	 * @param   int   $index  The index of the column to hide.
	 * 
	 * @return  bool  True if hidden, false in case the columns was already not visible.
	 */
	public function hide(int $index)
	{
		// make sure the the index already exists
		$pos = array_search($index, $this->columns);

		if ($pos !== false)
		{
			// remove the index from the array
			array_splice($this->columns, $pos, 1);

			return true;
		}

		// nothing to remove
		return false;
	}

	/**
	 * Changes the visibility of the column at the specified index.
	 * 
	 * @param   int   $index  The index of the column to toggle.
	 * 
	 * @return  void
	 */
	public function toggle(int $index)
	{
		// attempt to show the column
		$added = $this->show($index);

		if (!$added)
		{
			// not added because the index was already in the list,
			// therefore we should toggle the column by hiding it
			$this->hide($index);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		$head = [];

		// scan the head of the aggregated datasheet
		foreach ($this->dataSheet->getHead() as $index => $column)
		{
			// copy the column only in case the index is explicitly supported
			if (in_array($index, $this->columns))
			{
				$head[] = $column;
			}
		}

		return $head;
	}

	/**
	 * @inheritDoc
	 */
	public function getBody()
	{
		$bodyIterator = $this->dataSheet->getBody();

		if (!$bodyIterator instanceof \Iterator)
		{
			// obtain iterator by aggregating the array into a default itearator
			$bodyIterator = (new \ArrayIterator($bodyIterator))->getIterator();
		}

		// create a runtime iterator to preserve the default architecture
		return new class ($bodyIterator, $this->columns) implements \Iterator
		{
			/** @var Iterator */
			protected $bodyIterator;

			/** @var array */
			protected $columns;

			/**
			 * Class constructor.
			 */
			public function __construct(\Iterator $bodyIterator, array $columns = [])
			{
				$this->bodyIterator = $bodyIterator;
				$this->columns       = $columns;
			}

			/**
			 * @inheritDoc
			 * 
			 * @see \Iterator
			 */
			public function rewind(): void
			{
				$this->bodyIterator->rewind();
			}

			/**
			 * @inheritDoc
			 * 
			 * @see \Iterator
			 */
			#[\ReturnTypeWillChange]
			public function current()
			{
				$bodyRow = $this->bodyIterator->current();

				$row = [];

				// scan the body row of the aggregated datasheet
				foreach ($bodyRow as $index => $column)
				{
					// copy the column only in case the index is explicitly supported
					if (in_array($index, $this->columns))
					{
						$row[] = $column;
					}
				}

				return $row;
			}

			/**
			 * @inheritDoc
			 * 
			 * @see \Iterator
			 */
			#[\ReturnTypeWillChange]
			public function key()
			{
				return $this->bodyIterator->key();
			}

			/**
			 * @inheritDoc
			 * 
			 * @see \Iterator
			 */
			public function next(): void
			{
				$this->bodyIterator->next();
			}

			/**
			 * @inheritDoc
			 * 
			 * @see \Iterator
			 */
			public function valid(): bool
			{
				return $this->bodyIterator->valid();
			}
		};
	}

	/**
	 * @inheritDoc
	 */
	public function getFooter()
	{
		$footer = [];

		// scan the footer of the aggregated datasheet
		foreach ($this->dataSheet->getFooter() as $index => $column)
		{
			// copy the column only in case the index is explicitly supported
			if (in_array($index, $this->columns))
			{
				$footer[] = $column;
			}
		}

		return $footer;
	}
}
