<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Import\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Backup\Import\Rule;

/**
 * Backup SQL import rule.
 * 
 * @since 1.9
 */
class SQLImportRule extends Rule
{
	/**
	 * @inheritDoc
	 * 
	 * @param  array  $data  An array of queries to execute.
	 */
	public function execute($data)
	{
		$db = \JFactory::getDbo();

		// iterate all specified queries
		foreach ((array) $data as $q)
		{
			$db->setQuery($q);
			$db->execute();
		}
	}
}
