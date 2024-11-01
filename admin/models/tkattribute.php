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
 * VikRestaurants take-away menus attribute model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkattribute extends JModelVRE
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

		// load any relations between the products and the attributes
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_attr_assoc'))
			->where($db->qn('id_attribute') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc = $db->loadColumn())
		{
			// delete all the relations that belong to the removed attributes
			JModelVRE::getInstance('tkentryattribute')->delete($assoc);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_takeaway_menus_attribute'))
			->where($db->qn('id_attribute') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed attributes
			JModelVRE::getInstance('langtkattribute')->delete($languages);
		}

		return true;
	}
}
