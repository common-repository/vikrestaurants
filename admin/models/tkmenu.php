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
 * VikRestaurants take-away menu model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkmenu extends JModelVRE
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
		$menu = parent::getItem($pk, $new);

		if (!$menu)
		{
			return null;
		}

		if ($menu->id)
		{
			$menu->start_publishing = $menu->start_publishing && $menu->start_publishing != -1 ? date('Y-m-d H:i', $menu->start_publishing) : '';
			$menu->end_publishing   =   $menu->end_publishing &&   $menu->end_publishing != -1 ? date('Y-m-d H:i',   $menu->end_publishing) : '';
		}

		return $menu;
	}

	/**
	 * Returns a list of products assigned to the specified menu.
	 * 
	 * @param   int       $idMenu  The ID of the menu.
	 * 
	 * @return  object[]  A list of products.
	 */
	public function getProducts(int $idMenu)
	{
		if ($idMenu <= 0)
		{
			// avoid query in case the menu doesn't exist
			return [];
		}

		$productModel = JModelVRE::getInstance('tkentry');

		$products = [];

		$db = JFactory::getDbo();

		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_entry'))
			->where($db->qn('id_takeaway_menu') . ' = ' . $idMenu)
			->order($db->qn('ordering') . ' ASC');
		
		$db->setQuery($q);

		foreach ($db->loadColumn() as $idProduct)
		{
			// fetch item details
			$product = $productModel->getItem($idProduct);
			// fetch item variations
			$product->options = $productModel->getVariations($idProduct);

			$products[] = $product;
		}

		// do not use array keys
		return array_values($products);
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

		if (isset($data['products']))
		{
			$model = JModelVRE::getInstance('tkentry');

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
				// attach product to this menu
				$product['id_takeaway_menu'] = $id;

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

		// load any assigned product
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_entry'))
			->where($db->qn('id_takeaway_menu') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($products = $db->loadColumn())
		{
			// delete all the products that belong to the removed menus
			JModelVRE::getInstance('tkentry')->delete($products);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_takeaway_menus'))
			->where($db->qn('id_menu') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed menus
			JModelVRE::getInstance('langtkmenu')->delete($languages);
		}

		return true;
	}
}
