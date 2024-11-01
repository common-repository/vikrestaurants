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
 * VikRestaurants take-away add item view.
 * This view is displayed within the overlay
 * that opens after clicking the add button.
 *
 * @since 1.6
 */
class VikRestaurantsViewtkadditem extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();
		
		$id_entry  = $app->input->get('eid', 0, 'uint');
		$id_option = $app->input->get('oid', 0, 'uint');
		$index     = $app->input->get('index', -1, 'int');

		// get cart instance
		$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();

		/**
		 * The deals handler.
		 * 
		 * @var E4J\VikRestaurants\Deals\DealsHandler
		 * @since 1.9
		 */
		$dealsHandler = new E4J\VikRestaurants\Deals\DealsHandler($cart);

		/**
		 * A list of deals related to the products discounts.
		 *
		 * @var Deal[]
		 */
		$discountDeals = $dealsHandler->getAvailableDeals()
			->filter(new E4J\VikRestaurants\Deals\Filters\RuleFilter('discountitem'));
		
		$item = [];
		
		if ($index < 0)
		{
			// load product details as new entry
			$q = $dbo->getQuery(true);

			$q->select('e.*');
			$q->select($dbo->qn('o.id', 'oid'));
			$q->select($dbo->qn('o.name', 'oname'));
			$q->select($dbo->qn('o.inc_price', 'oprice'));

			$q->from($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'e'));
			$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $dbo->qn('o.id_takeaway_menu_entry') . ' = ' . $dbo->qn('e.id'));

			$q->where($dbo->qn('e.id') . ' = ' . (int) $id_entry);
			$q->where($dbo->qn('e.published') . ' = 1');

			if ((int) $id_option > 0)
			{
				$q->where($dbo->qn('o.id') . ' = ' . (int) $id_option);
				$q->where($dbo->qn('o.published') . ' = 1');
			}
			
			$dbo->setQuery($q, 0, 1);
			$item = $dbo->loadObject();

			if (!$item)
			{
				// product not found, raise error
				throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
			}

			if ($item->oid)
			{
				// build option object
				$item->option = new stdClass;
				$item->option->id   = $item->oid;
				$item->option->name = $item->oname;
			}
			else
			{
				$item->option = null;
			}

			// calculate total cost
			$item->price += (float) $item->oprice;

			// apply product translation
			VikRestaurants::translateTakeawayProducts($item);

			if ($item->option)
			{
				// apply variation translation
				VikRestaurants::translateTakeawayProductOptions($item->option);
			}

			// fetch full name (after translation)
			$item->fullName = $item->name . ($item->option ? ' - ' . $item->option->name : '');

			// set default quantity
			$item->quantity = 1;
			// set empty notes
			$item->notes = '';

			// no cart index
			$item->cartIndex = -1;

			$item->selectedToppings = [];

			// checks whether this product is discounted
			$item->price = $dealsHandler->discountItem([
				'id'        => $item->id,
				'id_option' => $item->oid,
				'price'     => $item->price,
			], $discountDeals);
		}
		else
		{
			// load product details stored within the cart
			$prod = $cart->getItemAt($index);

			if ($prod === null)
			{
				// product not found in cart, raise error
				throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
			}
			
			$sel_groups = $prod->getToppingsGroups();
			
			$item = new stdClass;
			$item->id       = $prod->getItemID();
			$item->name     = $prod->getItemName();
			$item->price    = $prod->getTotalCost() / $prod->getQuantity();
			$item->oid      = $prod->getOptionID();
			$item->oname    = $prod->getOptionName();
			$item->quantity = $prod->getQuantity();
			$item->notes    = $prod->getAdditionalNotes();

			// set cart index
			$item->cartIndex = $index;

			if ($item->oid)
			{
				// build option object
				$item->option = new stdClass;
				$item->option->id    = $item->oid;
				$item->option->name  = $item->oname;
			}
			else
			{
				$item->option = null;
			}

			$item->selectedToppings = [];
			
			foreach ($sel_groups as $g)
			{
				foreach ($g->getToppings() as $t)
				{
					$item->selectedToppings[$t->getAssocID()] = $t->getUnits();
				}
			}
			
			$id_entry  = $item->id;
			$id_option = $item->oid;
		}

		// fetch toppings groups

		$item->toppings = [];

		$q = $dbo->getQuery(true);

		$q->select('g.*');
		$q->select($dbo->qn('a.id', 'topping_group_assoc_id'));
		$q->select($dbo->qn('a.id_topping'));
		$q->select($dbo->qn('a.rate', 'topping_rate'));
		$q->select($dbo->qn('t.name', 'topping_name'));
		$q->select($dbo->qn('t.description', 'topping_desc'));

		$q->from($dbo->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_group_topping_assoc', 'a') . ' ON ' . $dbo->qn('a.id_group') . ' = ' . $dbo->qn('g.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_topping', 't') . ' ON ' . $dbo->qn('a.id_topping') . ' = ' . $dbo->qn('t.id'));

		$q->where($dbo->qn('g.id_entry') . ' = ' . $item->id);
		$q->where($dbo->qn('t.published') . ' = 1');

		if ($item->option)
		{
			$q->andWhere([
				$dbo->qn('g.id_variation') . ' <= 0',
				$dbo->qn('g.id_variation') . ' = ' . $item->option->id,
			], 'OR');
		}
		
		$q->order($dbo->qn('g.ordering') . ' ASC');
		$q->order($dbo->qn('a.ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $group)
		{
			if (!isset($item->toppings[$group->id]))
			{
				$tmp = new stdClass;
				$tmp->id           = $group->id;
				$tmp->title        = $group->title;
				$tmp->description  = $group->description ? $group->description : $group->title;
				$tmp->multiple     = $group->multiple;
				$tmp->min_toppings = $group->min_toppings;
				$tmp->max_toppings = $group->max_toppings;
				$tmp->use_quantity = $group->use_quantity;
				$tmp->list         = [];

				$item->toppings[$group->id] = $tmp;
			}
			
			if (!empty($group->topping_group_assoc_id))
			{
				$topping = new stdClass;
				$topping->id          = $group->id_topping;
				$topping->assoc_id    = $group->topping_group_assoc_id;
				$topping->name        = $group->topping_name;
				$topping->description = $group->topping_desc;
				$topping->rate        = $group->topping_rate;

				if (isset($item->selectedToppings[$group->topping_group_assoc_id]))
				{
					$topping->checked = true;
					$topping->units   = $item->selectedToppings[$group->topping_group_assoc_id];
				}
				else
				{
					$topping->checked = false;
					$topping->units   = 0;
				}

				$item->toppings[$group->id]->list[] = $topping;
			}
		}

		// apply toppings and groups translation
		VikRestaurants::translateTakeawayToppingsGroups($item->toppings);
		
		/**
		 * An object containing the product details and
		 * the available toppings.
		 *
		 * @var object
		 */
		$this->item = $item;
		
		// display the template
		parent::display($tpl);
	}
}
