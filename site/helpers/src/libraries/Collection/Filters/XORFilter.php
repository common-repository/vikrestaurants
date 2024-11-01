<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Abstract filter to support the XOR operator.
 * 
 * --------------------------------------
 * | Condition | Should ignore | Result |
 * --------------------------------------
 * | False     | False         | False  |
 * | False     | True          | True   |
 * | True      | False         | True   |
 * | True      | True          | False  |
 * --------------------------------------
 * 
 * @since 1.9
 */
abstract class XORFilter implements CollectionFilter
{
	/** @var bool */
	protected $ignore;

	/**
	 * Class constructor.
	 * 
	 * @param  bool  $ignore  False to take the items that satisfies the condition,
	 *                        otherwise true to exclude them.
	 */
	public function __construct(bool $ignore = false)
	{
		$this->ignore = $ignore;
	}

	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		// get matching result from the implemented condition
		$result = (bool) $this->isSatisfied($item);

		// commutate (XOR) result depending on the ignore flag.
		return $result ^ $this->ignore;
	}

	/**
	 * Implements the real condition, which will then be commutated with a XOR.
	 * 
	 * @param   Item  $item  The item to check.
	 * 
	 * @return  bool  True if satisfied, false otherwise.
	 */
	abstract protected function isSatisfied(Item $item);
}
