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
 * VikRestaurants take-away menu item toppings group model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkentrygroup extends JModelVRE
{
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

		$isNew = empty($data['id']);

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		if (isset($data['toppings']))
		{
			$model = JModelVRE::getInstance('tkgrouptopping');

			// iterate all the provided toppings
			foreach ($data['toppings'] as $i => $topping)
			{
				if (is_string($topping))
				{
					// JSON given, decode it
					$topping = json_decode($topping, true);
				}

				if ($isNew)
				{
					// unset ID to create a copy
					$topping['id'] = 0;
				}

				// update ordering
				$topping['ordering'] = $i + 1;
				// attach topping to this menu
				$topping['id_group'] = $id;

				// save topping
				$model->save($topping);
			}

			if (!$isNew)
			{
				// take only the topping ID
				$toppings = array_map(function($topping)
				{
					return (int) $topping['id_topping'];
				}, $data['toppings']);

				// only in case of update, synchronize the toppings by deleting the detached ones
				$model->deleteDetachedToppings($id, $toppings);
			}
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

		// load any assigned toppings
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_group_topping_assoc'))
			->where($db->qn('id_group') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the toppings that belong to the removed groups
			JModelVRE::getInstance('tkgrouptopping')->delete($languages);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_takeaway_menus_entry_topping_group'))
			->where($db->qn('id_group') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed groups
			JModelVRE::getInstance('langtkentrygroup')->delete($languages);
		}

		return true;
	}
}
