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
 * VikRestaurants take-away menu item model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkentry extends JModelVRE
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

		if ($product->id)
		{
			$product->attributes = $this->getAttributes($product->id);
			$product->img_extra  = $product->img_extra ? (array) json_decode($product->img_extra) : [];
		}
		else
		{
			$product->attributes = [];
			$product->img_extra  = [];
		}

		$product->images = array_merge($product->img_path ? [$product->img_path] : [], $product->img_extra);

		return $product;
	}

	/**
	 * Returns a list of attributes attached to the specified product.
	 * 
	 * @param   int    $idProduct  The ID of the product.
	 * 
	 * @return  int[]  An array of attribute IDs.
	 */
	public function getAttributes(int $idProduct)
	{
		if (!$idProduct)
		{
			// save a query in case of missing ID
			return [];
		}

		$db = JFactory::getDbo();

		$q = $db->getQuery(true)
			->select($db->qn('id_attribute'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_attr_assoc'))
			->where($db->qn('id_menuentry') . ' = ' . $idProduct);

		$db->setQuery($q);

		// get rid of any duplicates
		return array_values(array_unique($db->loadColumn()));
	}

	/**
	 * Returns a list of variations attached to the specified product.
	 * 
	 * @param   int       $idProduct  The ID of the product.
	 * 
	 * @return  object[]  An array of variations.
	 */
	public function getVariations(int $idProduct)
	{
		if (!$idProduct)
		{
			// save a query in case of missing ID
			return [];
		}

		$db = JFactory::getDbo();

		$q = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__vikrestaurants_takeaway_menus_entry_option'))
			->where($db->qn('id_takeaway_menu_entry') . ' = ' . $idProduct)
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($q);
		return $db->loadObjectList();
	}

	/**
	 * Returns a list of toppings groups attached to the specified product.
	 * 
	 * @param   int       $idProduct  The ID of the product.
	 * 
	 * @return  object[]  An array of toppings groups.
	 */
	public function getToppingsGroups(int $idProduct)
	{
		if (!$idProduct)
		{
			// save a query in case of missing ID
			return [];
		}
		
		$groups = [];

		$db = JFactory::getDbo();

		$q = $db->getQuery(true)
			->select('g.*')
			->select($db->qn('t.name', 'topping_name'))
			->select($db->qn('t.ordering', 'topping_ord'))
			->select([
				$db->qn('a.id_topping'),
				$db->qn('a.id', 'topping_group_assoc_id'),
				$db->qn('a.rate', 'topping_rate'),
			])
			->from($db->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_group_topping_assoc', 'a') . ' ON ' . $db->qn('a.id_group') . ' = ' . $db->qn('g.id'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_topping', 't') . ' ON ' . $db->qn('a.id_topping') . ' = ' . $db->qn('t.id'))
			->where($db->qn('g.id_entry') . ' = ' . $idProduct)
			->order($db->qn('g.ordering') . ' ASC')
			->order($db->qn('a.ordering') . ' ASC');

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $group)
		{
			if (!isset($groups[$group->id]))
			{
				$group->toppings = [];
				$groups[$group->id] = $group;
			}
			
			if (!empty($group->topping_group_assoc_id))
			{
				$topping = new stdClass;
				$topping->id         = $group->topping_group_assoc_id;
				$topping->id_topping = $group->id_topping;
				$topping->name       = $group->topping_name;
				$topping->rate       = $group->topping_rate;
				$topping->ordering   = $group->topping_ord;

				$groups[$group->id]->toppings[] = $topping;
			}
		}

		// do not use array keys
		return array_values($groups);
	}

	/**
	 * Binds the provided product with the stocks information.
	 * 
	 * @param   object|int  $product  Either a product object or an ID.
	 * 
	 * @return  object      The product object.
	 */
	public function getStocks($product)
	{
		if (is_numeric($product))
		{
			// we have a product, load all the information needed 
			$product = $this->getItem((int) $product);

			if (!$product)
			{
				throw new RuntimeException('Product not found', 404);
			}
		}

		$product = (object) $product;

		if (!isset($product->options))
		{
			// no variations, load them now
			$product->options = $this->getVariations($product->id);
		}

		$config = VREFactory::getConfig();

		// check if the stock is enabled
		if ($config->getBool('tkenablestock'))
		{
			// calculate remaining units for this product
			$product->stock = VikRestaurants::getTakeawayItemRemainingInStock($product->id);
		}
		else
		{
			// stock disabled
			$product->stock = null;
		}

		foreach ($product->options as $option)
		{
			if ($option->stock_enabled && $config->getBool('tkenablestock'))
			{
				// calculate remaining units for this product variation
				$option->stock = VikRestaurants::getTakeawayItemRemainingInStock($product->id, $option->id);
			}
			else
			{
				// otherwise use parent stock
				$option->stock = $product->stock;
			}
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

		// attempt to save the record
		$id = parent::save($data);

		if (!$id)
		{
			return false;
		}

		if (isset($data['attributes']))
		{
			JModelVRE::getInstance('tkentryattribute')->setRelation($id, $data['attributes']);
		}

		if (isset($data['options']))
		{
			$model = JModelVRE::getInstance('tkentryoption');

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
				// attach option to this menu
				$option['id_takeaway_menu_entry'] = $id;

				// save option
				$model->save($option);
			}
		}

		if (isset($data['groups']))
		{
			$model = JModelVRE::getInstance('tkentrygroup');

			// iterate all the provided toppings groups
			foreach ($data['groups'] as $i => $group)
			{
				if (is_string($group))
				{
					// JSON given, decode it
					$group = json_decode($group, true);

					if (isset($group['description']))
					{
						// sanitize group description only if we have a JSON, meaning that the
						// group has been probably submitted to the controller
						$group['description'] = JComponentHelper::filterText($group['description']);
					}
				}

				if ($isNew)
				{
					// unset ID to create a copy
					$group['id'] = 0;
				}

				// update ordering
				$group['ordering'] = $i + 1;
				// attach group to this menu
				$group['id_entry'] = $id;

				// save group
				$model->save($group);
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

		// load any assigned variations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_entry_option'))
			->where($db->qn('id_takeaway_menu_entry') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($variations = $db->loadColumn())
		{
			// delete all the variations that belong to the removed products
			JModelVRE::getInstance('tkentryoption')->delete($variations);
		}

		// load any assigned attributes
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_attr_assoc'))
			->where($db->qn('id_menuentry') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($attributes = $db->loadColumn())
		{
			// delete all the attributes that belong to the removed products
			JModelVRE::getInstance('tkentryattribute')->delete($attributes);
		}

		// load any assigned toppings groups
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_entry_group_assoc'))
			->where($db->qn('id_entry') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($groups = $db->loadColumn())
		{
			// delete all the toppings groups that belong to the removed products
			JModelVRE::getInstance('tkentrygroup')->delete($groups);
		}

		// load any assigned stock overrides
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_stock_override'))
			->where($db->qn('id_takeaway_entry') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($overrides = $db->loadColumn())
		{
			// delete all the stock overrides that belong to the removed products
			JModelVRE::getInstance('tkstock')->delete($overrides);
		}

		// load any assigned translation
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_takeaway_menus_entry'))
			->where($db->qn('id_entry') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($languages = $db->loadColumn())
		{
			// delete all the translations that belong to the removed products
			JModelVRE::getInstance('langtkentry')->delete($languages);
		}

		return true;
	}
}
