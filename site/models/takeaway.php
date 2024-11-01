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
 * VikRestaurants take-away menus list view model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTakeaway extends JModelVRE
{
	/**
	 * Fetch a list of available take-away menus.
	 * 
	 * @param   array     $options  A configuration array.
	 * 
	 * @return  object[]  The menus list.
	 */
	public function getItems(array $options = [])
	{
		// if not specified, always take only published items
		if (!isset($options['strict']))
		{
			$options['strict'] = true;
		}

		$db = JFactory::getDbo();

		// fetch menus
		$query = $db->getQuery(true);

		$query->select($db->qn('e.id', 'eid'));
		$query->select($db->qn('e.name', 'ename'));
		$query->select($db->qn('e.description', 'edesc'));
		$query->select($db->qn('e.price', 'eprice'));
		$query->select($db->qn('e.ready', 'eready'));
		$query->select($db->qn('e.img_path', 'eimg'));
		$query->select($db->qn('e.img_extra', 'eimgextra'));

		$query->select($db->qn('o.id', 'oid'));
		$query->select($db->qn('o.name', 'oname'));
		$query->select($db->qn('o.inc_price', 'oprice'));

		$query->select($db->qn('m.id', 'mid'));
		$query->select($db->qn('m.title', 'mtitle'));
		$query->select($db->qn('m.description', 'mdesc'));
		$query->select($db->qn('m.layout', 'mlayout'));

		$query->from($db->qn('#__vikrestaurants_takeaway_menus', 'm'));
		$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('e.id_takeaway_menu') . ' AND ' . $db->qn('e.published') . ' = 1');
		$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('o.id_takeaway_menu_entry') . ' AND ' . $db->qn('o.published') . ' = 1');

		if ($options['strict'])
		{
			// take only the published menus
			$query->where($db->qn('m.published') . ' = 1');

			if (!empty($options['date']))
			{
				/**
				 * Take all the menus with a valid/empty start publishing.
				 *
				 * @since 1.8.3
				 */
				$query->andWhere([
					$db->qn('m.start_publishing') . ' = -1',
					$db->qn('m.start_publishing') . ' IS NULL',
					$db->qn('m.start_publishing') . ' <= ' . $options['date'],
				], 'OR');

				/**
				 * Take all the menus with a valid/empty finish publishing.
				 *
				 * @since 1.8.3
				 */
				$query->andWhere([
					$db->qn('m.end_publishing') . ' = -1',
					$db->qn('m.end_publishing') . ' IS NULL',
					$db->qn('m.end_publishing') . ' >= ' . $options['date'], 
				], 'OR');
			}
		}

		if (!empty($options['menu']))
		{
			// take only the selected menu
			$query->where($db->qn('m.id') . ' = ' . (int) $options['menu']);
		}

		$query->order($db->qn('m.ordering') . ' ASC');
		$query->order($db->qn('e.ordering') . ' ASC');
		$query->order($db->qn('o.ordering') . ' ASC');

		$db->setQuery($query);

		// construct tree
		$menus = $this->parseItems($db->loadObjectList());

		// translate menus
		$this->translate($menus);

		return $menus;
	}

	/**
	 * Builds the take-away menus tree.
	 *
	 * @param   array  $rows  A list of records.
	 *
	 * @return  array  The resulting tree.
	 */
	private function parseItems($rows)
	{
		// get all attributes
		$attributes = JHtml::fetch('vikrestaurants.takeawayattributes');

		$db = JFactory::getDbo();

		$attrLookup = [];

		// get all products attributes
		$q = $db->getQuery(true)
			->select($db->qn('id_menuentry'))
			->select($db->qn('id_attribute'))
			->from($db->qn('#__vikrestaurants_takeaway_menus_attr_assoc'));

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $attr)
		{
			if (!isset($attrLookup[$attr->id_menuentry]))
			{
				$attrLookup[$attr->id_menuentry] = [];
			}

			$attrLookup[$attr->id_menuentry][] = $attr->id_attribute;
		}

		$menus = [];
		
		foreach ($rows as $r)
		{
			if (!isset($menus[$r->mid]))
			{
				$menu = new stdClass;
				$menu->id          = $r->mid;
				$menu->title       = $r->mtitle;
				$menu->description = $r->mdesc;
				$menu->layout      = $r->mlayout ?: 'list';
				$menu->products    = [];

				$menus[$r->mid] = $menu;
			}

			if ($r->eid && !isset($menus[$r->mid]->products[$r->eid]))
			{
				$prod = new stdClass;
				$prod->id          = $r->eid;
				$prod->name        = $r->ename;
				$prod->description = $r->edesc;
				$prod->price       = $r->eprice;
				$prod->ready       = $r->eready;
				$prod->image       = $r->eimg;
				$prod->options     = [];
				$prod->attributes  = [];

				/**
				 * Build images gallery.
				 * 
				 * @since 1.8.2
				 */
				$prod->images = [];

				/**
				 * Add image to list only if specified.
				 *
				 * @since 1.8.3
				 */
				if ($prod->image)
				{
					$prod->images[] = $prod->image;
				}

				if ($r->eimgextra)
				{
					// merge main image with extra images
					$prod->images = array_merge($prod->images, json_decode($r->eimgextra));
				}

				// search for product attributes
				if (isset($attrLookup[$r->eid]))
				{
					// iterate all attributes
					foreach ($attributes as $attr)
					{
						// check if the product is assigned to this attribute
						if (in_array($attr->id, $attrLookup[$r->eid]))
						{
							// copy attribute details
							$prod->attributes[] = $attr;
						}
					}
				}

				$menus[$r->mid]->products[$r->eid] = $prod;
			}
			
			if ($r->oid)
			{
				$opt = new stdClass;
				$opt->id    = $r->oid;
				$opt->name  = $r->oname;
				$opt->price = $r->oprice;

				$menus[$r->mid]->products[$r->eid]->options[$r->oid] = $opt;
			}
		}
		
		return $menus;
	}

	/**
	 * Translates the menus.
	 *
	 * @param   object[]  $menus  The menus to translate.
	 *
	 * @return  void
	 */
	protected function translate(array $menus)
	{
		// make sure multi-language is supported
		if (!VikRestaurants::isMultilanguage())
		{
			return;
		}

		$lookup = [
			'tkmenu'        => [],
			'tkentry'       => [],
			'tkentryoption' => [],
		];

		// iterate menu sections
		foreach ($menus as $menu)
		{
			$lookup['tkmenu'][] = $menu->id;

			foreach ($menu->products as $product)
			{
				$lookup['tkentry'][] = $product->id;

				foreach ($product->options as $option)
				{
					$lookup['tkentryoption'][] = $option->id;
				}
			}
		}

		// get language tage
		$langtag = JFactory::getLanguage()->getTag();

		// get translator
		$translator = VREFactory::getTranslator();
		
		// preload translations
		foreach ($lookup as $table => $ids)
		{
			// preload translations for current table
			$lookup[$table] = $translator->load($table, $ids, $langtag);
		}

		// iterate menus
		foreach ($menus as $menu)
		{
			// get translation of current menu
			$menu_tx = $lookup['tkmenu']->getTranslation($menu->id, $langtag);

			if ($menu_tx)
			{
				$menu->title       = $menu_tx->title;
				$menu->description = $menu_tx->description;
			}

			// iterate menu products
			foreach ($menu->products as $product)
			{
				// get translation of current product
				$prod_tx = $lookup['tkentry']->getTranslation($product->id, $langtag);

				if ($prod_tx)
				{
					$product->name        = $prod_tx->name;
					$product->description = $prod_tx->description;
				}

				// iterate product options
				foreach ($product->options as $option)
				{
					// get translation of current option
					$opt_tx = $lookup['tkentryoption']->getTranslation($option->id, $langtag);

					if ($opt_tx)
					{
						$option->name = $opt_tx->name;
					}
				}
				// end option
			}
			// end product
		}
		// end menu
	}
}
