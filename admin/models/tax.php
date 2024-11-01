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
 * VikRestaurants tax model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTax extends JModelVRE
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
		$tax = parent::getItem($pk, $new);

		if (!$tax)
		{
			return null;
		}

		$tax->rules = [];

		if ($tax->id)
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__vikrestaurants_tax_rule'))
				->where($db->qn('id_tax') . ' = ' . (int) $tax->id)
				->order($db->qn('ordering') . ' ASC');

			$db->setQuery($query);
			
			foreach ($db->loadObjectList() as $rule)
			{
				// decode breakdown list
				$rule->breakdown = $rule->breakdown ? json_decode($rule->breakdown) : [];

				$tax->rules[] = $rule;
			}
		}

		return $tax;
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

		$isNew = empty($data['id']);

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		if (isset($data['rules']))
		{
			$model = JModelVRE::getInstance('taxrule');

			// iterate all the provided rules
			foreach ($data['rules'] as $i => $rule)
			{
				if (is_string($rule))
				{
					// JSON given, decode it
					$rule = json_decode($rule, true);
				}

				if ($isNew)
				{
					// unset ID to create a copy
					$rule['id'] = 0;
				}

				// update ordering
				$rule['ordering'] = $i + 1;
				// attach rule to this tax
				$rule['id_tax'] = $id;

				// save tax rule
				$model->save($rule);
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

		$db = JFactory::getDbo();

		// load any assigned translation
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_tax'))
			->where($db->qn('id_tax') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($query);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations made for the removed taxes
			JModelVRE::getInstance('langtax')->delete($languages);
		}

		// load any children rules
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_tax_rule'))
			->where($db->qn('id_tax') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($query);

		if ($rules = $db->loadColumn())
		{
			// delete all the rules assigned to the removed taxes
			JModelVRE::getInstance('taxrule')->delete($rules);
		}

		return true;
	}
}
