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
 * VikRestaurants API user model.
 *
 * @since 1.9
 */
class VikRestaurantsModelApiuser extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return 	mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		// load item through parent
		$item = parent::getItem($pk, $new);

		if ($item)
		{
			$item->ips    = $item->ips    ? (array) json_decode($item->ips, true)    : [];
			$item->denied = $item->denied ? (array) json_decode($item->denied, true) : [];
		}

		return $item;
	}

	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return 	boolean  True on success, false otherwise.
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

		// get user-log model
		$model = JModelVRE::getInstance('apilog');

		foreach ($ids as $id_login)
		{
			// delete all the logs assigned to the specified user
			$model->truncate($id_login);	
		}

		$db = JFactory::getDbo();

		// load any user-event config relation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_api_login_event_options'))
			->where($db->qn('id_login') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc_ids = $db->loadColumn())
		{
			// delete the user configurations
			$model = JModelVRE::getInstance('apiuseroptions')->delete($assoc_ids);
		}

		return true;
	}
}
