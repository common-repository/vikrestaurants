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
 * VikRestaurants take-away deal model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkdeal extends JModelVRE
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
		$deal = parent::getItem($pk, $new);

		if (!$deal)
		{
			return null;
		}

		if ($deal->id > 0)
		{
			// decode saved parameters
			$deal->params = $deal->params ? (object) json_decode($deal->params) : new stdClass;

			// decode saved shifts
			$deal->shifts = $deal->shifts ? (array) json_decode($deal->shifts) : [];

			$db = JFactory::getDbo();

			// get working days
			$query = $db->getQuery(true)
				->select($db->qn('id_weekday'))
				->from($db->qn('#__vikrestaurants_takeaway_deal_day_assoc'))
				->where($db->qn('id_deal') . ' = ' . $deal->id)
				->order($db->qn('id_weekday') . ' ASC');

			$db->setQuery($query);
			$deal->days = $db->loadColumn();

			// get target products
			$query = $db->getQuery(true)
				->select('d.*')
				->select($db->qn('m.title', 'menu_title'))
				->select($db->qn('e.name', 'product_name'))
				->select($db->qn('o.name', 'option_name'))
				->from($db->qn('#__vikrestaurants_takeaway_deal_product_assoc', 'd'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('d.id_product'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('o.id') . ' = ' . $db->qn('d.id_option'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('e.id_takeaway_menu'))
				->where($db->qn('id_deal') . ' = ' . $deal->id);

			$db->setQuery($query);
			$deal->products = array_map(function($p)
			{
				// decode saved parameters
				$p->params = $p->params ? (object) json_decode($p->params) : new stdClass;
				return $p;
			}, $db->loadObjectList());

			// get gift products
			$query = $db->getQuery(true)
				->select('d.*')
				->select($db->qn('m.title', 'menu_title'))
				->select($db->qn('e.name', 'product_name'))
				->select($db->qn('o.name', 'option_name'))
				->from($db->qn('#__vikrestaurants_takeaway_deal_free_assoc', 'd'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('d.id_product'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('o.id') . ' = ' . $db->qn('d.id_option'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('e.id_takeaway_menu'))
				->where($db->qn('id_deal') . ' = ' . $deal->id);

			$db->setQuery($query);
			$deal->gifts = array_map(function($p)
			{
				// decode saved parameters
				$p->params = $p->params ? (object) json_decode($p->params) : new stdClass;
				return $p;
			}, $db->loadObjectList());
		}
		else
		{
			$deal->params   = new stdClass;
			$deal->shifts   = [];
			$deal->days     = [];
			$deal->products = [];
			$deal->gifts    = [];
		}

		return $deal;
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

		$isNew = empty($data['id']);

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		if (isset($data['days']))
		{
			// define relations between the deal and the working days
			JModelVRE::getInstance('tkdealday')->setRelation($id, $data['days']);
		}

		if (isset($data['products']))
		{
			$model = JModelVRE::getInstance('tkdealproduct');

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

				// attach product to this deal
				$product['id_deal'] = $id;

				// save target product
				$model->save($product);
			}
		}

		if (isset($data['gifts']))
		{
			$model = JModelVRE::getInstance('tkdealfree');

			// iterate all the provided gifts
			foreach ($data['gifts'] as $i => $product)
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

				// attach product to this deal
				$product['id_deal'] = $id;

				// save target product
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

		// load any assigned target products
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_deal_product_assoc'))
			->where($db->qn('id_deal') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($products = $db->loadColumn())
		{
			// delete all the target products that belong to the removed deals
			JModelVRE::getInstance('tkdealproduct')->delete($products);
		}

		// load any assigned gift products
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_deal_free_assoc'))
			->where($db->qn('id_deal') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($products = $db->loadColumn())
		{
			// delete all the gift products that belong to the removed deals
			JModelVRE::getInstance('tkdealfree')->delete($products);
		}

		// load any assigned working days
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_deal_day_assoc'))
			->where($db->qn('id_deal') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($days = $db->loadColumn())
		{
			// delete all the working days that belong to the removed deals
			JModelVRE::getInstance('tkdealday')->delete($days);
		}

		// load any assigned translations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_takeaway_deal'))
			->where($db->qn('id_deal') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations made for the removed deals
			JModelVRE::getInstance('langtkdeal')->delete($languages);
		}

		return true;
	}
}
