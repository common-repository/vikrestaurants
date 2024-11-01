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
 * VikRestaurants operator model.
 *
 * @since 1.9
 */
class VikRestaurantsModelOperator extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return  mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		$operator = parent::getItem($pk, $new);

		if (!$operator)
		{
			return null;
		}

		$operator->rooms    = $operator->rooms    ? explode(',', $operator->rooms)    : [];
		$operator->products = $operator->products ? explode(',', $operator->products) : [];

		return $operator;
	}

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

		// delete any logs assigned to the removed tables
		$q = $db->getQuery(true)
			->delete($db->qn('#__vikrestaurants_operator_log'))
			->where($db->qn('id_operator') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);
		$db->execute();

		return true;
	}

	/**
	 * Method to delete one or more logs.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return  boolean  True on success.
	 */
	public function deleteLogs($ids = null)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		if (!$ids)
		{
			return false;
		}

		$db = JFactory::getDbo();

		// delete operators logs
		$q = $db->getQuery(true)
			->delete($db->qn('#__vikrestaurants_operator_log'))
			->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');

		$db->setQuery($q);
		$db->execute();

		return (bool) $db->getAffectedRows();
	}

	/**
	 * Method to delete all the logs older than the specified limit.
	 *
	 * @param   string   $limit  A date string to be passed to `strtotime()`.
	 *
	 * @return  integer  The number of deleted records.
	 */
	public function flushLogs($limit)
	{
		// calculate timestamp limit
		$limit = strtotime('-' . preg_replace("/^[-+\s]+/", '', $limit));

		if (!$limit)
		{
			// invalid limit
			return 0;
		}

		$db = JFactory::getDbo();

		// delete operators logs
		$q = $db->getQuery(true)
			->delete($db->qn('#__vikrestaurants_operator_log'))
			->where($db->qn('createdon') . ' < ' . (int) $limit);

		$db->setQuery($q);
		$db->execute();

		return $db->getAffectedRows();
	}
}
