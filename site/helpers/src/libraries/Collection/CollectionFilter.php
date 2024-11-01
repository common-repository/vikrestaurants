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
 * Interface used to filter the collection items.
 * 
 * @since 1.9
 */
interface CollectionFilter
{
	/**
	 * Checks whether the provided item matches the search query
	 * defined by this filter.
	 * 
	 * @param   Item  $item
	 * 
	 * @return  bool  True if matching, false otherwise.
	 */
	public function match(Item $item);
}
