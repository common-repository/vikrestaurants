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
 * This interfaces represents the encapsulation of an abstract datasheet, which
 * is usually a table containing a heading, a body and a footer.
 * 
 * @since 1.9
 */
interface DataSheet
{
	/**
	 * Returns the datasheet title.
	 * 
	 * @return  string
	 */
	public function getTitle();

	/**
	 * Returns the table heading.
	 * 
	 * @return  string[]
	 */
	public function getHead();

	/**
	 * Returns the table body.
	 * Since the number of records may be a huge load, it is possible to 
	 * return an iterator to scan the rows one by one.
	 * 
	 * @return  Iterator|array
	 */
	public function getBody();

	/**
	 * Returns the table footer.
	 * 
	 * @return  string[]
	 */
	public function getFooter();
}
