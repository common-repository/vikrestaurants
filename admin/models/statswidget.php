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
 * VikRestaurants statistics widget model.
 *
 * @since 1.9
 */
class VikRestaurantsModelStatswidget extends JModelVRE
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
			$item->params = $item->params ? (array) json_decode($item->params, true) : array();
		}

		return $item;
	}

	/**
	 * Returns the configuration parameters stored within the record of the specified widget ID.
	 *
	 * @param 	integer  $id  The widget ID.
	 *
	 * @return 	array    The configuration associative array.
	 */
	public function getParams($id)
	{
		// load widget details
		$item = $this->getItem($id);

		if (!$item)
		{
			// return an empty configuration
			return [];
		}

		return $item->params;
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

		if (!$ids)
		{
			// nothing to delete
			return false;
		}

		$dbo = JFactory::getDbo();

		// filter the selected widgets and exclude those ones that haven't
		// been assigned to any user, meaning they act as a demo
		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->from($dbo->qn('#__vikrestaurants_stats_widget'))
			->where($dbo->qn('id_user') . ' > 0')
			->where($dbo->qn('id') . ' IN (' . implode(',', $ids) . ')');

		$dbo->setQuery($q);
		$ids = $dbo->loadColumn();

		if (!$ids)
		{
			// nothing to delete
			return false;
		}

		// otherwise invoke parent to delete widgets
		return parent::delete($ids);
	}
}
