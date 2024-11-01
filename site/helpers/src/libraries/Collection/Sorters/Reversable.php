<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Sorters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionSorter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Abstract class to support both ASC and DESC direction.
 * 
 * @since 1.9
 */
abstract class Reversable implements CollectionSorter
{
	/**
	 * True in case of DESC direction, false if ASC.
	 * 
	 * @var bool
	 */
	private $reverse;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $direction  ASC or DESC (case-insensitive).
	 */
	public function __construct(string $direction = 'ASC')
	{
		$this->reverse = strcasecmp($direction, 'DESC') === 0;
	}

	/**
	 * @inheritDoc
	 */
	final public function compare(Item $a, Item $b)
	{
		// let the implementor performs the comparison
		$factor = $this->doCompare($a, $b);

		if ($this->reverse)
		{
			// reverse comparison in case of DESC direction
			$factor *= -1;
		}

		return $factor;
	}

	/**
	 * Comparison implementation.
	 * 
	 * @param   Item  $a
	 * @param   Item  $b
	 * 
	 * @return  int   Returns "1" in case $a > $b, "-1" in case $a < $b, "0" if equals.
	 */
	abstract protected function doCompare(Item $a, Item $b);
}
