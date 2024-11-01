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
 * VikRestaurants room model.
 *
 * @since 1.9
 */
class VikRestaurantsModelRoom extends JModelVRE
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

		// load any table child
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_table'))
			->where($db->qn('id_room') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($tables = $db->loadColumn())
		{
			// delete all the tables that belong to the removed rooms
			JModelVRE::getInstance('table')->delete($tables);
		}

		// load any room closure
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_room_closure'))
			->where($db->qn('id_room') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($closures = $db->loadColumn())
		{
			// delete all the closures that belong to the removed rooms
			JModelVRE::getInstance('roomclosure')->delete($closures);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_room'))
			->where($db->qn('id_room') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed rooms
			JModelVRE::getInstance('langroom')->delete($languages);
		}

		return true;
	}
}
