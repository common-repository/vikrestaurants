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
 * This class wraps an array and converts it into a datasheet.
 * 
 * @since 1.9
 */
class ArrayDataSheet implements DataSheet
{
	/**
	 * An array of arrays/objects, where the attributes/properties
	 * are used as table head.
	 * 
	 * @var array
	 */
	protected $records;

	/**
	 * The datasheet title.
	 * 
	 * @var string
	 */
	protected $title;

	/**
	 * Class constructor.
	 * 
	 * @param  array   $records  An array of arrays/objects.
	 * @param  string  $title    An optional title for the datasheet.
	 */
	public function __construct(array $records, string $title = 'Datasheet 1')
	{
		$this->records = $records;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		// fetch the first row of the table and obtain only the array keys
		return array_keys($this->records ? (array) $this->records[0] : []);
	}

	/**
	 * @inheritDoc
	 */
	public function getBody()
	{
		$body = [];

		foreach ($this->records as $record)
		{
			// extract only the values from each row in the array
			$body[] = array_values($record);
		}

		return $body;
	}

	/**
	 * @inheritDoc
	 */
	public function getFooter()
	{
		// no footer supported by default
		return [];
	}
}
