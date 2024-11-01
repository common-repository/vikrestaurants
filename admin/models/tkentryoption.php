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
 * VikRestaurants take-away menu item variation model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkentryoption extends JModelVRE
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

		$db = JFactory::getDbo();

		// load any assigned stock overrides
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_stock_override'))
			->where($db->qn('id_takeaway_option') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($overrides = $db->loadColumn())
		{
			// delete all the stock overrides that belong to the removed variations
			JModelVRE::getInstance('tkstock')->delete($overrides);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_takeaway_menus_entry_option'))
			->where($db->qn('id_option') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed variations
			JModelVRE::getInstance('langtkentryoption')->delete($languages);
		}

		return true;
	}
}
