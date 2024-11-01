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
 * VikRestaurants take-away group-topping relational model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkgrouptopping extends JModelVRE
{
	/**
	 * Returns all the products that uses the specified topping.
	 * 
	 * @param   int     $idTopping  The topping ID to look for.
	 *
	 * @return  object[]
	 */
	public function getProducts(int $idTopping)
	{
		if ($idTopping <= 0)
		{
			return [];
		}

		$rows = [];

		$db = JFactory::getDbo();

		// update rate topping associations
		$q = $db->getQuery(true);

		// reverse search from topping ID to product ID
		$q->select($db->qn('gt.id', 'topping_assoc_id'));
		$q->select($db->qn('gt.rate', 'topping_rate'));
		$q->from($db->qn('#__vikrestaurants_takeaway_group_topping_assoc', 'gt'));
		$q->join('INNER', $db->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'pg') . ' ON ' . $db->qn('gt.id_group') . ' = ' . $db->qn('pg.id'));

		// select product details
		$q->select($db->qn('p.id', 'product_id'));
		$q->select($db->qn('p.name', 'product_name'));
		$q->join('INNER', $db->qn('#__vikrestaurants_takeaway_menus_entry', 'p') . ' ON ' . $db->qn('pg.id_entry') . ' = ' . $db->qn('p.id'));

		// select menu details
		$q->select($db->qn('m.id', 'menu_id'));
		$q->select($db->qn('m.title', 'menu_title'));
		$q->join('INNER', $db->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $db->qn('p.id_takeaway_menu') . ' = ' . $db->qn('m.id'));
		
		// select product variations
		$q->select($db->qn('o.id', 'option_id'));
		$q->select($db->qn('o.name', 'option_name'));
		$q->join('LEFT', $db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o')
			. ' ON ' . $db->qn('o.id_takeaway_menu_entry') . ' = ' . $db->qn('p.id')
			. ' AND ' . $db->qn('pg.id_variation') . ' = ' . $db->qn('o.id'));

		// filter by topping ID
		$q->where($db->qn('gt.id_topping') . ' = ' . (int) $idTopping);

		$q->order($db->qn('m.ordering') . ' ASC');
		$q->order($db->qn('p.ordering') . ' ASC');
		$q->order($db->qn('o.ordering') . ' ASC');

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $row)
		{
			if (!isset($rows[$row->menu_id]))
			{
				$menu = new stdClass;
				$menu->id       = $row->menu_id;
				$menu->title    = $row->menu_title;
				$menu->products = [];

				$rows[$row->menu_id] = $menu;
			}

			$product = new stdClass;
			$product->id = $row->product_id;
			$product->name = $row->product_name;

			if ($row->option_id)
			{
				$option = new stdClass;
				$option->id   = $row->option_id;
				$option->name = $row->option_name;
				$product->option = $option;
			}
			else
			{
				$product->option = null;
			}

			$product->topping = new stdClass;
			$product->topping->id   = $row->topping_assoc_id;
			$product->topping->rate = $row->topping_rate;

			$rows[$row->menu_id]->products[] = $product;
		}

		return array_values($rows);
	}

	/**
	 * Method to delete all the toppings that are not included
	 * within the specified list.
	 *
	 * @param   int    $group     The group entry ID.
	 * @param   array  $toppings  A list of existing IDs.
	 *
	 * @return  bool   True on success.
	 */
	public function deleteDetachedToppings(int $group, array $toppings = [])
	{
		$db = JFactory::getDbo();

		// delete all toppings assigned to the specified group
		// but there are not included within the toppings list
		$q = $db->getQuery(true)
			->delete($db->qn('#__vikrestaurants_takeaway_group_topping_assoc'))
			->where($db->qn('id_group') . ' = '. (int) $group);

		// delete select toppings only if the list if not empty,
		// otherwise delete all assigned toppings
		if ($toppings)
		{
			$q->where($db->qn('id_topping') . ' NOT IN (' . implode(',', array_map('intval', $toppings)) . ')');
		}

		$db->setQuery($q);
		$db->execute();

		return (bool) $db->getAffectedRows();
	}
}
