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
 * VikRestaurants restaurant product model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMenusproduct extends JModelVRE
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
		$product = parent::getItem($pk, $new);

		if (!$product)
		{
			return null;
		}

		$product->options = [];

		if ($product->id)
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__vikrestaurants_section_product_option'))
				->where($db->qn('id_product') . ' = ' . (int) $product->id)
				->order($db->qn('ordering') . ' ASC');

			$db->setQuery($query);
			$product->options = $db->loadObjectList();
		}

		return $product;
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

		if (isset($data['tags']))
		{
			// commit tags
			$data['tags'] = JModelVRE::getInstance('tag')->writeTags($data['tags'], 'products', 'name');
		}

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		if (isset($data['options']))
		{
			$model = JModelVRE::getInstance('productoption');

			// iterate all the provided options
			foreach ($data['options'] as $i => $option)
			{
				if (is_string($option))
				{
					// JSON given, decode it
					$option = json_decode($option, true);
				}

				if ($isNew)
				{
					// unset ID to create a copy
					$option['id'] = 0;
				}

				// update ordering
				$option['ordering'] = $i + 1;
				// attach option to this product
				$option['id_product'] = $id;

				// save option
				$model->save($option);
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

		// load any relation with the sections
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_section_product_assoc'))
			->where($db->qn('id_product') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc = $db->loadColumn())
		{
			// delete all the relations between the sections and the removed products
			JModelVRE::getInstance('sectionproduct')->delete($assoc);
		}

		// load any assigned option
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_section_product_option'))
			->where($db->qn('id_product') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($options = $db->loadColumn())
		{
			// delete all the options that belong to the removed products
			JModelVRE::getInstance('productoption')->delete($options);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_section_product'))
			->where($db->qn('id_product') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed products
			JModelVRE::getInstance('langmenusproduct')->delete($languages);
		}

		return true;
	}
}
