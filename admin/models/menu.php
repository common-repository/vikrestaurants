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
 * VikRestaurants restaurant menu model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMenu extends JModelVRE
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

		// decode encoded columns
		$menu->working_shifts = $menu->working_shifts ? array_filter(preg_split("/,\s*/", $menu->working_shifts)) : [];
		$menu->days_filter = strlen((string) $menu->days_filter) ? array_filter(preg_split("/,\s*/", $menu->days_filter), 'strlen') : [];

		return $menu;
	}

	/**
	 * Returns an array of sections assigned to the specified menu.
	 * 
	 * @param   integer  $id  The menu ID.
	 * 
	 * @return  array    An array of sections.
	 */
	public function getSections($id)
	{
		$sections = [];

		if ($id <= 0)
		{
			// save a query in case of no menu
			return $sections;
		}

		$db = JFactory::getDbo();

		$q = $db->getQuery(true)
			->select($db->qn([
				's.id', 's.name', 's.description', 's.published', 's.highlight', 's.orderdishes', 's.image',
			]))
			->select([
				$db->qn('p.id', 'id_product'),
				$db->qn('p.name', 'prod_name'),
				$db->qn('p.image', 'prod_image'),
				$db->qn('p.published', 'prod_published'),
				$db->qn('p.price', 'prod_price'),
				$db->qn('a.charge', 'prod_charge'),
				$db->qn('a.id', 'id_assoc'),
			])
			->from($db->qn('#__vikrestaurants_menus_section', 's'))
			->leftjoin($db->qn('#__vikrestaurants_section_product_assoc', 'a') . ' ON ' . $db->qn('s.id') . ' = ' . $db->qn('a.id_section'))
			->leftjoin($db->qn('#__vikrestaurants_section_product', 'p') . ' ON ' . $db->qn('p.id') . ' = ' . $db->qn('a.id_product'))
			->where($db->qn('s.id_menu') . ' = ' . (int) $id)
			->order([
				$db->qn('s.ordering') . ' ASC',
				$db->qn('a.ordering') . ' ASC',
			]);
		
		$db->setQuery($q);

		foreach ($db->loadObjectList() as $tmp)
		{
			if (!isset($sections[$tmp->id]))
			{
				$section = new stdClass;
				$section->id          = $tmp->id;
				$section->name        = $tmp->name;
				$section->image       = $tmp->image;
				$section->description = $tmp->description;
				$section->published   = $tmp->published;
				$section->highlight   = $tmp->highlight;
				$section->orderdishes = $tmp->orderdishes;
				$section->products    = [];

				$sections[$tmp->id] = $section;
			}

			if ($tmp->id_product)
			{
				$prod = new stdClass;
				$prod->id        = $tmp->id_assoc;
				$prod->name      = $tmp->prod_name;
				$prod->image     = $tmp->prod_image;
				$prod->published = $tmp->prod_published;
				$prod->price     = $tmp->prod_price;
				$prod->charge    = $tmp->prod_charge;
				$prod->idProduct = $tmp->id_product;
				$prod->idSection = $tmp->id;

				$sections[$tmp->id]->products[] = $prod;
			}
		}
		
		return array_values($sections);
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

		if (isset($data['sections']))
		{
			$model = JModelVRE::getInstance('menusection');

			// iterate all the provided sections
			foreach ($data['sections'] as $i => $section)
			{
				if (is_string($section))
				{
					// JSON given, decode it
					$section = json_decode($section, true);
				}

				if ($isNew)
				{
					// unset ID to create a copy
					$section['id'] = 0;
				}

				// update ordering
				$section['ordering'] = $i + 1;
				// attach section to this menu
				$section['id_menu'] = $id;

				// save section
				$model->save($section);
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
			->from($db->qn('#__vikrestaurants_menus_section'))
			->where($db->qn('id_menu') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc = $db->loadColumn())
		{
			// delete all the relations between the sections and the removed menus
			JModelVRE::getInstance('menusection')->delete($assoc);
		}

		// load any translations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_menus'))
			->where($db->qn('id_menu') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations made for the removed menus
			JModelVRE::getInstance('langmenu')->delete($languages);
		}

		// load any relation with the special days (restaurant only)
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_sd_menus'))
			->where($db->qn('group') . ' = 1')
			->where($db->qn('id_menu') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc = $db->loadColumn())
		{
			// delete all the relations between the special days and the removed menus
			JModelVRE::getInstance('specialdaymenu')->delete($assoc);
		}

		// load any relation with the restaurant reservations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_res_menus_assoc'))
			->where($db->qn('id_menu') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($assoc = $db->loadColumn())
		{
			// delete all the relations between the reservations and the removed menus
			JModelVRE::getInstance('resmenu')->delete($assoc);
		}

		return true;
	}
}
