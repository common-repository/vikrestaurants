<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants tax translation model.
 *
 * @since 1.9
 */
class VikRestaurantsModelLangtax extends JModelVRE
{
	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function delete($ids)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		// invoke parent first
		if (!parent::delete($ids))
		{
			// nothing to delete
			return false;
		}

		$dbo = JFactory::getDbo();

		// load any children rules
		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->from($dbo->qn('#__vikrestaurants_lang_tax_rule'))
			->where($dbo->qn('id_parent') . ' IN (' . implode(',', $ids) . ')' );

		$dbo->setQuery($q);

		if ($rules = $dbo->loadColumn())
		{
			// delete all the rules assigned to the removed taxes
			JModelVRE::getInstance('langtaxrule')->delete($rules);
		}

		return true;
	}
}
