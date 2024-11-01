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
 * VikRestaurants menu details view model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMenudetails extends JModelVRE
{
	/**
	 * Fetch the details of the provided menu.
	 * 
	 * @param   int          $id       The menu ID.
	 * @param   array        $options  A configuration array.
	 * 
	 * @return  object|bool  The menu details on success, false otherwise.
	 */
	public function getMenu(int $id, array $options = [])
	{
		// if not specified, always take only published items
		if (!isset($options['strict']))
		{
			$options['strict'] = true;
		}

		/** @var stdClass|null */
		$menu = JModelVRE::getInstance('menu')->getItem($id);

		if (!$menu || ($options['strict'] && !$menu->published))
		{
			// menu not found or unpublished
			$this->setError(new Exception(JText::translate('JERROR_PAGE_NOT_FOUND'), 404));
			return false;
		}

		$menu->sections = [];

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('s.*');
		$query->select($db->qn('p.id', 'pid'));
		$query->select($db->qn('p.name', 'pname'));
		$query->select($db->qn('p.image', 'pimage'));
		$query->select($db->qn('p.description', 'pdesc'));
		$query->select(sprintf('(%s + %s) AS %s', $db->qn('p.price'), $db->qn('a.charge'), $db->qn('pcharge')));
		$query->select($db->qn('a.id', 'aid'));
		$query->select($db->qn('o.id', 'oid'));
		$query->select($db->qn('o.name', 'oname'));
		$query->select($db->qn('o.inc_price', 'oprice'));

		$query->from($db->qn('#__vikrestaurants_menus_section', 's'));
		$query->leftjoin($db->qn('#__vikrestaurants_section_product_assoc', 'a') . ' ON ' . $db->qn('s.id') . ' = ' . $db->qn('a.id_section'));
		$query->leftjoin($db->qn('#__vikrestaurants_section_product', 'p') . ' ON ' . $db->qn('p.id') . ' = ' . $db->qn('a.id_product') . ($options['strict'] ? ' AND ' . $db->qn('p.published') . ' = 1' : ''));
		$query->leftjoin($db->qn('#__vikrestaurants_section_product_option', 'o') . ' ON ' . $db->qn('p.id') . ' = ' . $db->qn('o.id_product'));
		
		$query->where($db->qn('s.id_menu') . ' = ' . $menu->id);

		if ($options['strict'])
		{
			if (!empty($options['orderdishes']))
			{
				// take only the sections that have been published or that are available for the purchase
				$query->andWhere([
					$db->qn('s.published') . ' = 1',
					$db->qn('s.orderdishes') . ' = 1',
				], 'OR');
			}
			else
			{
				// take only published sections
				$query->where($db->qn('s.published') . ' = 1');
			}
		}

		$query->order($db->qn('s.ordering') . ' ASC');
		$query->order($db->qn('a.ordering') . ' ASC');
		$query->order($db->qn('o.ordering') . ' ASC');

		$db->setQuery($query);
		
		foreach ($db->loadObjectList() as $r)
		{	
			if ($r->id && !isset($menu->sections[$r->id]))
			{
				$section = new stdClass;
				$section->id          = $r->id;
				$section->name        = $r->name;
				$section->description = $r->description;
				$section->image       = $r->image;
				$section->highlight   = $r->highlight;
				$section->products    = [];

				$menu->sections[$r->id] = $section;
			}
			
			if ($r->pid && !isset($menu->sections[$r->id]->products[$r->pid]))
			{
				$prod = new stdClass;
				$prod->id          = $r->pid;
				$prod->idAssoc     = $r->aid;
				$prod->name        = $r->pname;
				$prod->description = $r->pdesc;
				$prod->image       = $r->pimage;
				$prod->price       = $r->pcharge;
				$prod->options     = [];

				$menu->sections[$r->id]->products[$r->pid] = $prod;
			}
			
			if ($r->oid)
			{
				$opt = new stdClass;
				$opt->id    = $r->oid;
				$opt->name  = $r->oname;
				$opt->price = $r->oprice;
				
				$menu->sections[$r->id]->products[$r->pid]->options[$r->oid] = $opt;
			}
		}

		if ($menu->sections)
		{
			$this->translate($menu);
		}

		return $menu;
	}

	/**
	 * Translates the menu details.
	 *
	 * @param   object  $menu  The menu to translate.
	 *
	 * @return  void
	 */
	protected function translate($menu)
	{
		// make sure multi-language is supported
		if (!VikRestaurants::isMultilanguage())
		{
			return;
		}

		$lookup = [
			'menusection'   => [],
			'menusproduct'  => [],
			'productoption' => [],
		];

		// iterate menu sections
		foreach ($menu->sections as $section)
		{
			$lookup['menusection'][] = $section->id;

			foreach ($section->products as $product)
			{
				$lookup['menusproduct'][] = $product->id;

				foreach ($product->options as $option)
				{
					$lookup['productoption'][] = $option->id;
				}
			}
		}

		// get language tage
		$langtag = JFactory::getLanguage()->getTag();

		// get translator
		$translator = VREFactory::getTranslator();

		// get menu translation
		$menu_tx = $translator->translate('menu', $menu->id, $langtag);

		if ($menu_tx)
		{
			$menu->name        = $menu_tx->name;
			$menu->description = $menu_tx->description;
		}
		
		// preload translations
		foreach ($lookup as $table => $ids)
		{
			// preload translations for current table
			$lookup[$table] = $translator->load($table, $ids, $langtag);
		}

		// iterate menu sections
		foreach ($menu->sections as $section)
		{
			// get translation of current section
			$section_tx = $lookup['menusection']->getTranslation($section->id, $langtag);

			if ($section_tx)
			{
				$section->name        = $section_tx->name;
				$section->description = $section_tx->description;
			}

			// iterate section products
			foreach ($section->products as $product)
			{
				// get translation of current product
				$prod_tx = $lookup['menusproduct']->getTranslation($product->id, $langtag);

				if ($prod_tx)
				{
					$product->name        = $prod_tx->name;
					$product->description = $prod_tx->description;
				}

				// iterate product options
				foreach ($product->options as $option)
				{
					// get translation of current option
					$opt_tx = $lookup['productoption']->getTranslation($option->id, $langtag);

					if ($opt_tx)
					{
						$option->name = $opt_tx->name;
					}
				}
				// end option
			}
			// end product
		}
		// end section
	}
}
