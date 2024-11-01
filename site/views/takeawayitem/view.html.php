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
 * VikRestaurants take-away item details view.
 * It is possible to purchase the selected product from here.
 *
 * @since 1.7
 */
class VikRestaurantsViewtakeawayitem extends JViewVRE
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

		$config = VREFactory::getConfig();
		
		$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();
		
		$id_item   = $app->input->get('takeaway_item', 0, 'uint');
		$date      = $app->input->get('takeaway_date', null, 'string');
		$id_option = $app->input->get('id_option', 0, 'uint');

		/**
		 * Change check-in date if specified.
		 *
		 * @since 1.8
		 */
		if (!empty($date) && $config->getBool('tkallowdate'))
		{
			$checkin_ts = VikRestaurants::createTimestamp($date, 0, 0);

			if ($checkin_ts != $cart->getCheckinTimestamp())
			{
				// update check-in date
				$cart->setCheckinTimestamp($checkin_ts);
				$cart->setCheckinTime(null);

				// check for deals
				VikRestaurants::resetDealsInCart($cart, $cart->getCheckinTime(true));
				VikRestaurants::checkForDeals($cart);

				// commit changes
				$cart->store();
			}
		}

		// compose request
		$request = new stdClass;
		$request->idEntry  = $id_item;
		$request->idOption = $id_option;
		$request->quantity = $app->input->get('quantity', 1, 'uint');
		$request->notes    = $app->input->get('notes', '', 'string');
		$request->toppings = $app->input->get('topping', [], 'array');
		$request->units    = $app->input->get('topping_units', [], 'array');

		$filters = [];
		$filters['date'] = date($config->get('dateformat'), $cart->getCheckinTimestamp());

		// get all attributes
		$attributes = JHtml::fetch('vikrestaurants.takeawayattributes');

		/**
		 * The deals handler.
		 * 
		 * @var E4J\VikRestaurants\Deals\DealsHandler
		 * @since 1.9
		 */
		$this->dealsHandler = new E4J\VikRestaurants\Deals\DealsHandler($cart);

		/**
		 * A list of deals related to the products discounts.
		 *
		 * @var Deal[]
		 */
		$discountDeals = $this->dealsHandler->getAvailableDeals()
			->filter(new E4J\VikRestaurants\Deals\Filters\RuleFilter('discountitem'));

		// build item object
		$item = $this->buildTakeawayProduct($request, $attributes, $discountDeals);

		if ($cart->getCheckinTime())
		{
			// append check-in time for a correct usage
			$date_time = $filters['date'] . ' ' . $cart->getCheckinTime();
		}
		else
		{
			$date_time = $cart->getCheckinTimestamp();
		}

		// fetch status of the menu
		VikRestaurants::fetchMenusStatus($item->menu, $date_time);

		// check if the form should be submitted when
		// the variation changes, as there might be
		// other toppings groups available
		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikrestaurants_takeaway_entry_group_assoc'))
			->where($dbo->qn('id_entry') . ' = ' . $item->id)
			->where($dbo->qn('id_variation') . ' > 0');
		
		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$to_submit = (bool) $dbo->getNumRows();

		// get reviews
		VRELoader::import('library.reviews.handler');

		$reviewsHandler = new ReviewsHandler();

		$reviews = $reviewsHandler->takeaway()
			->setOrdering('rating', 2)
			->addOrdering('timestamp', 2)
			->getReviews($item->id);

		$reviews_stats = $reviewsHandler->takeaway()->getAverageRatio($item->id);
		
		/**
		 * An object containing the details of
		 * the selected product.
		 *
		 * @var object
		 */
		$this->item = $item;

		/**
		 * The current cart instance.
		 *
		 * @var E4J\VikRestaurants\TakeAway\Cart
		 */
		$this->cart = $cart;

		/**
		 * A list of published food attributes.
		 *
		 * @var array
		 */
		$this->attributes = $attributes;

		/**
		 * A list of reviews made for the selected product.
		 *
		 * @var array
		 */
		$this->reviews = $reviews;

		/**
		 * An object containing a statistics summary of the
		 * reviews left for the selected product.
		 *
		 * @var object
		 */
		$this->reviewsStats = $reviews_stats;

		/**
		 * An object containing the requested information.
		 *
		 * @var object
		 */
		$this->request = $request;

		/**
		 * Flag used to check whether the form should be
		 * submitted every time the variation changes.
		 *
		 * @var boolean
		 */
		$this->isToSubmit = $to_submit;

		/**
		 * The current menu item ID.
		 * 
		 * @var int|null
		 * @since 1.9
		 */
		$this->itemid = $app->input->get('Itemid', null, 'uint');

		// prepare page content
		VikRestaurants::prepareContent($this);

		$this->extendPathway($app);
		
		// display the template
		parent::display($tpl);
	}
	
	/**
	 * Builds the take-away item object.
	 *
	 * @param 	object  $request 	 An object containing the request details.
	 * @param 	array 	$attributes  A list of attributes.
	 * @param 	Deal[] 	$deals       A list of discounts.
	 *
	 * @return 	array 	The resulting tree.
	 *
	 * @since 	1.8
	 */
	private function buildTakeawayProduct($request, array $attributes, $deals)
	{
		$id_entry  = $request->idEntry;
		$id_option = $request->idOption;
		$toppings  = $request->toppings;
		$units     = $request->units;

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select('e.*');

		$q->select($dbo->qn('o.id', 'oid'));
		$q->select($dbo->qn('o.name', 'oname'));
		$q->select($dbo->qn('o.inc_price', 'oprice'));

		$q->select($dbo->qn('m.id', 'mid'));
		$q->select($dbo->qn('m.title', 'mtitle'));
		$q->select($dbo->qn('m.description', 'mdesc'));

		$q->from($dbo->qn('#__vikrestaurants_takeaway_menus', 'm'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $dbo->qn('m.id') . ' = ' . $dbo->qn('e.id_takeaway_menu') . ' AND ' . $dbo->qn('e.published') . ' = 1');
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $dbo->qn('e.id') . ' = ' . $dbo->qn('o.id_takeaway_menu_entry'));
		
		$q->where($dbo->qn('m.published') . ' = 1');
		$q->where($dbo->qn('e.id') . ' = ' . (int) $id_entry);

		$q->order($dbo->qn('o.ordering') . ' ASC');		
		
		$dbo->setQuery($q);
		$rows = $dbo->loadObjectList();

		if (!$rows)
		{
			// product not found, raise error
			throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
		}

		$item = new stdClass;
		$item->id          = $rows[0]->id;
		$item->name        = $rows[0]->name;
		$item->description = $rows[0]->description;
		$item->price       = $rows[0]->price;
		$item->image       = $rows[0]->img_path;
		$item->totalPrice  = $item->price;

		/**
		 * Build images gallery.
		 * 
		 * @since 1.8.2
		 */
		$item->images = [];

		/**
		 * Add image to list only if specified.
		 *
		 * @since 1.8.3
		 */
		if ($item->image)
		{
			$item->images[] = $item->image;
		}

		if ($rows[0]->img_extra)
		{
			// merge main image with extra images
			$item->images = array_merge($item->images, json_decode($rows[0]->img_extra));
		}

		// add URI to images
		$item->images = array_map(function($image)
		{
			return VREMEDIA_URI . $image;
		}, $item->images);

		// checks whether this product is discounted
		$item->totalPrice = $this->dealsHandler->discountItem([
			'id'        => $item->id,
			'price'     => $item->totalPrice,
		], $deals);

		$item->totalBasePrice = $item->totalPrice;

		// build menu object
		$item->menu = new stdClass;
		$item->menu->id          = $rows[0]->mid;
		$item->menu->title       = $rows[0]->mtitle;
		$item->menu->description = $rows[0]->mdesc;

		// build options

		$item->options = [];

		foreach ($rows as $row)
		{
			if ($row->oid)
			{
				$option = new stdClass;
				$option->id    = $row->oid;
				$option->name  = $row->oname;
				$option->price = $row->oprice;

				$option->totalPrice = $item->price + $option->price;

				// checks whether this product is discounted
				$option->totalPrice = $this->dealsHandler->discountItem([
					'id'         => $item->id,
					'id_option'  => $option->id,
					'price'      => $option->totalPrice,
				], $deals);

				if ($option->id == $id_option)
				{
					// increase total cost
					$item->totalPrice = $option->totalPrice;
				}

				$item->options[] = $option;
			}
		}

		// apply menu translation
		VikRestaurants::translateTakeawayMenus($item->menu);

		// apply product translation
		VikRestaurants::translateTakeawayProducts($item);

		// apply variation translation
		VikRestaurants::translateTakeawayProductOptions($item->options);

		// get all products attributes

		$item->attributes = [];

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id_attribute'))
			->from($dbo->qn('#__vikrestaurants_takeaway_menus_attr_assoc'))
			->where($dbo->qn('id_menuentry') . ' = ' . (int) $item->id);

		$dbo->setQuery($q);
		$tmp = $dbo->loadColumn();

		// iterate all attributes
		foreach ($attributes as $attr)
		{
			// check if the product is assigned to this attribute
			if (in_array($attr->id, $tmp))
			{
				// copy attribute details
				$item->attributes[] = $attr;
			}
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

		$q->andWhere(array(
			$dbo->qn('g.id_variation') . ' <= 0',
			$dbo->qn('g.id_variation') . ' = ' . (int) $id_option,
		), 'OR');
		
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
				$topping->checked     = isset($toppings[$group->id]) && in_array($topping->assoc_id, $toppings[$group->id]);
				$topping->units       = 0;

				if ($topping->checked)
				{
					/**
					 * Check whether the customer specified the units
					 * for this topping.
					 *
					 * @since 1.8.2
					 */
					if (isset($units[$group->id][$topping->assoc_id]))
					{
						$topping->units = $units[$group->id][$topping->assoc_id];
					}
					else
					{
						$topping->units = 1;
					}

					// increase total price if topping was checked
					$item->totalPrice += $topping->rate * $topping->units;
				}

				$item->toppings[$group->id]->list[] = $topping;
			}
		}
		
		// apply toppings and groups translation
		VikRestaurants::translateTakeawayToppingsGroups($item->toppings);

		return $item;
	}

	/**
	 * Extends the pathway for breadcrumbs module.
	 *
	 * @param   mixed  $app  The application instance.
	 *
	 * @return  void
	 *
	 * @since   1.9
	 */
	protected function extendPathway($app)
	{
		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		$name = $this->item->name;
		$id   = $this->item->id;

		// Make sure this menu is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Item > Item
		if ($last && strpos($last->link, '&takeaway_item=' . $id) === false)
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=takeawayitem&takeaway_item=' . $id;
			$pathway->addItem($name, $link);
		}

		if ($this->request->idOption > 0)
		{
			$optionName = '';

			// find the name of the selected variation
			foreach ($this->item->options as $opt)
			{
				if ($opt->id == $this->request->idOption)
				{
					$optionName = $opt->name;
				}
			}

			// register variation link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=takeawayitem&takeaway_item=' . $id . '&id_option=' . $this->request->idOption;
			$pathway->addItem($optionName, $link);
		}
	}
}
