<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Filters\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper used to compare 2 numbers depending on the specified operator.
 * 
 * @since 1.9
 */
trait NumberComparator
{ 
	/**
	 * Compares the provided numbers.
	 * 
	 * @param   mixed    $a
	 * @param   mixed    $b
	 * @param   $string  $comparator
	 * 
	 * @return  bool     True in case the comparison is satisfied, false otherwise.
	 */
	public function compare($a, $b, string $comparator)
	{
		switch ($comparator)
		{
			case  '=': return $a == $b;
			case  '!': return $a != $b;
			case  '<': return $a <  $b;
			case  '>': return $a >  $b;
			case '<=': return $a <= $b;
			case '>=': return $a >= $b;
		}

		return false;
	}
}
