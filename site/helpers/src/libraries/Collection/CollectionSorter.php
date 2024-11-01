<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Interface used to rearrange the items contained in a collection.
 * 
 * @since 1.9
 */
interface CollectionSorter
{
	/**
	 * Compares the provided items.
	 * 
	 * @param   Item  $a
	 * @param   Item  $b
	 * 
	 * @return  int   Returns "1" in case $a > $b, "-1" in case $a < $b, "0" if equals.
	 */
	public function compare(Item $a, Item $b);
}
