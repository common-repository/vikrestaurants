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
 * VikRestaurants special day model.
 *
 * @since 1.9
 */
class VikRestaurantsModelSpecialday extends JModelVRE
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
		$specialday = parent::getItem($pk, $new);

		if (!$specialday)
		{
			return null;
		}

		// decode encoded columns
		$specialday->delivery_areas = $specialday->delivery_areas ? (array) json_decode($specialday->delivery_areas) : [];
		$specialday->images = $specialday->images ? array_filter(explode(';;', $specialday->images)) : [];
		$specialday->working_shifts = $specialday->working_shifts ? array_filter(preg_split("/,\s*/", $specialday->working_shifts)) : [];
		$specialday->custom_shifts = $specialday->custom_shifts ? (array) json_decode($specialday->custom_shifts) : [];
		$specialday->days_filter = strlen((string) $specialday->days_filter) ? array_filter(preg_split("/,\s*/", $specialday->days_filter), 'strlen') : [];

		$specialday->menus = [];

		if ($specialday->id)
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select($db->qn('id_menu'))
				->from($db->qn('#__vikrestaurants_sd_menus'))
				->where($db->qn('id_spday') . ' = ' . (int) $specialday->id);

			$db->setQuery($query);
			$specialday->menus = $db->loadColumn();
		}
		else
		{
			$config = VREFactory::getConfig();
			
			$specialday->askdeposit    = $config->getUint('askdeposit');
			$specialday->depositcost   = $config->getFloat('resdeposit');
			$specialday->perpersoncost = $config->getUint('costperperson');
		}

		return $specialday;
	}

	/**
	 * Basic save implementation.
	 *
	 * @param 	mixed  $data  Either an array or an object of data to save.
	 *
	 * @return 	mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		// attempt to save the relation
		$id = parent::save($data);

		if (!$id)
		{
			// an error occurred, do not go ahead
			return false;
		}

		if (isset($data['menus']))
		{
			// get special day-menu model
			$model = JModelVRE::getInstance('specialdaymenu');
			// define relations
			$model->setRelation($id, $data['menus']);
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

		// load any menu relations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_sd_menus'))
			->where($db->qn('id_spday') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($menus = $db->loadColumn())
		{
			// delete all the menus that belong to the removed special days
			JModelVRE::getInstance('specialdaymenu')->delete($menus);
		}

		return true;
	}
}
