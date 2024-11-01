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
 * VikRestaurants room table model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTable extends JModelVRE
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
		$table = parent::getItem($pk, $new);

		if (!$table)
		{
			return null;
		}

		if ($table->id)
		{
			// fetch the table cluster
			$table->cluster = VREAvailabilitySearch::getTablesCluster($table->id);
		}
		else
		{
			// set default values for the new table
			$table->min_capacity = 2;
			$table->max_capacity = 4;
			$table->cluster      = [];
		}

		return $table;
	}

	/**
	 * Basic save implementation.
	 *
	 * @param   mixed  $data  Either an array or an object of data to save.
	 *
	 * @return  mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		// attempt to save the table
		$id = parent::save($data);

		if (!$id)
		{
			// an error occurred, do not go ahead
			return false;
		}

		if (isset($data['cluster']))
		{
			// update table cluster
			$this->setCluster($id, $data['cluster']);
		}

		return $id;
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

		// delete any cluster that belongs to the removed tables
		$q = $db->getQuery(true)
			->delete($db->qn('#__vikrestaurants_table_cluster'))
			->where($db->qn('id_table_1') . ' IN (' . implode(',', $ids) . ')' )
			->orWhere($db->qn('id_table_2') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);
		$db->execute();

		return true;
	}

	/**
	 * Creates the cluster of tables.
	 *
	 * Note it is needed to bind the table first in order to have the
	 * record ID accessible.
	 *
	 * @param   integer  $id      The table ID.
	 * @param   array    $tables  A list of tables to attach.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function setCluster($id, array $tables = array())
	{
		$db = JFactory::getDbo();

		// get existing records

		$existing = VREAvailabilitySearch::getTablesCluster($id);

		// insert new records

		$has = $aff = false;

		$q = $db->getQuery(true)
			->insert($db->qn('#__vikrestaurants_table_cluster'))
			->columns($db->qn(array('id_table_1', 'id_table_2')));

		foreach ($tables as $r)
		{
			// make sure the record to push doesn't exist yet
			if (!in_array($r, $existing))
			{
				$q->values((int) $id . ', ' . (int) $r);
				$has = true;
			}
		}

		if ($has)
		{
			$db->setQuery($q);
			$db->execute();

			$aff = (bool) $db->getAffectedRows();
		}

		// delete records

		foreach ($existing as $r)
		{
			// make sure the records to delete is not contained in the selected records
			if (!in_array($r, $tables))
			{
				$q = $db->getQuery(true)
					->delete($db->qn('#__vikrestaurants_table_cluster'))
					->where(array(
						$db->qn('id_table_1') . ' = ' . (int) $id,
						$db->qn('id_table_2') . ' = ' . $r,
					))
					->orWhere(array(
						$db->qn('id_table_2') . ' = ' . (int) $id,
						$db->qn('id_table_1') . ' = ' . $r,
					));

				$db->setQuery($q);
				$db->execute();

				$aff = $aff || $db->getAffectedRows();
			}
		}

		return $aff;
	}
}
