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
 * VikRestaurants restaurant menu section model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMenusection extends JModelVRE
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

		if (isset($data['products']))
		{
			$model = JModelVRE::getInstance('sectionproduct');

			// iterate all the provided products
			foreach ($data['products'] as $i => $product)
			{
				if (is_string($product))
				{
					// JSON given, decode it
					$product = json_decode($product, true);
				}

				if ($isNew)
				{
					// unset ID to create a copy
					$product['id'] = 0;
				}

				// update ordering
				$product['ordering'] = $i + 1;
				// attach product to this section
				$product['id_section'] = $id;

				if (empty($product['id_product']) && !empty($product['idProduct']))
				{
					$product['id_product'] = (int) $product['idProduct'];
				}

				// save product
				$model->save($product);
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
			->where($db->qn('id_section') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc = $db->loadColumn())
		{
			// delete all the relations between the products and the removed sections
			JModelVRE::getInstance('sectionproduct')->delete($assoc);
		}

		// load any translations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_menus_section'))
			->where($db->qn('id_section') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations made for the removed sections
			JModelVRE::getInstance('langmenusection')->delete($languages);
		}

		return true;
	}
}
