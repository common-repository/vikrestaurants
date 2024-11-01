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
 * Backup SQL INSERT import rule.
 * 
 * @since 1.9
 */
class InsertImportRule extends Rule
{
	/**
	 * @inheritDoc
	 * 
	 * @param  string  $data  The query to execute.
	 */
	public function execute($data)
	{
		$db = \JFactory::getDbo();

		$db->setQuery((string) $data);
		$db->execute();
	}
}
