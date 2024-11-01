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
 * VikRestaurants take-away menus view.
 * This view displays a list of available menus
 * with all the related products. Here it is 
 * possible to start ordering some food.
 *
 * @since 1.2
 */
class VikRestaurantsViewtakeaway extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();

		$config = VREFactory::getConfig();
		
		$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();
		
		$filters = [];
		$filters['menu']    = $app->input->get('takeaway_menu', 0, 'int');
		$filters['date']    = $app->input->get('takeaway_date', '', 'string');
		$filters['hourmin'] = $app->input->get('takeaway_time', '', 'string');

		$reset_deals = false;

		// only if date is set and date can be changed
		if (!empty($filters['date']) && $config->getBool('tkallowdate'))
		{
			$checkin_ts = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($filters['date']);

			// update check-in date
			$cart->setCheckinTimestamp($checkin_ts);

			$reset_deals = true;
		}
		else
		{
			// use cart date
			$filters['date'] = date($config->get('dateformat'), $cart->getCheckinTimestamp());
		}

		// obtain all the available times for pick-up and delivery
		$times = JHtml::fetch('vikrestaurants.takeawaytimes', $filters['date'], $cart, ['show_asap' => false]);

		/**
		 * Make sure the selected time is supported.
		 *
		 * @since 1.8.3
		 */
		if ($filters['hourmin'] && !JHtml::fetch('vikrestaurants.hastime', $filters['hourmin'], $times))
		{
			// time not supported, unset it
			$filters['hourmin'] = null;
		}

		if ($filters['hourmin'])
		{
			// reset deals in case the time changed
			$reset_deals = true;
		}
		else if ($times)
		{
			// get time saved in cart
			$filters['hourmin'] = $cart->getCheckinTime();

			if (!$filters['hourmin'])
			{
				// use first time available in case there is no selected time
				$sh = reset($times);
				$filters['hourmin'] = $sh[0]->value;
			}
		}

		// validate the time against the available ones,
		// because the selected time might be not available
		// and the next one could be on a different shift
		if (!VikRestaurants::validateTakeAwayTime($filters['hourmin'], $times))
		{
			// invalid time, reset deals
			$reset_deals = true;
		}

		// always refresh check-in time
		$cart->setCheckinTime($filters['hourmin']);

		if ($reset_deals)
		{
			// check for deals
			VikRestaurants::resetDealsInCart($cart, $filters['hourmin']);
			VikRestaurants::checkForDeals($cart);
		}

		// save cart changes
		$cart->store();

		// get all take-away menus available for the specified date
		$available_menus = VikRestaurants::getAllTakeawayMenusOn($filters);
		
		// get menu items
		$menus = JModelVRE::getInstance('takeaway')->getItems([
			'menu' => $filters['menu'],
			'date' => $cart->getCheckinTimestamp(),
		]);

		// fetch status of the menus
		VikRestaurants::fetchMenusStatus($menus, $cart->getCheckinTimestamp(), $available_menus);

		/**
		 * A list containing all the published menus.
		 * Each menu contains a list of products.
		 *
		 * @var object[]
		 */
		$this->menus = $menus;
		
		/**
		 * A list of menus available for the selected day.
		 *
		 * @var int[]
		 */
		$this->availableMenus = $available_menus;

		/**
		 * A list of published food attributes.
		 *
		 * @var object[]
		 */
		$this->attributes = JHtml::fetch('vikrestaurants.takeawayattributes');

		/**
		 * An associative array containing a few
		 * search filters.
		 *
		 * @var array
		 */
		$this->filters = $filters;

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
		$this->discountDeals = $this->dealsHandler->getAvailableDeals()
			->filter(new E4J\VikRestaurants\Deals\Filters\RuleFilter('discountitem'));

		/**
		 * The user cart instance.
		 *
		 * @var E4J\VikRestaurants\TakeAway\Cart
		 */
		$this->cart = $cart;

		/**
		 * A list of available times.
		 *
		 * @var array
		 * @since 1.8
		 */
		$this->times = $times;

		/**
		 * The current menu item ID.
		 * 
		 * @var int|null
		 * @since 1.9
		 */
		$this->itemid = $app->input->get('Itemid', null, 'uint');

		// prepare page content
		VikRestaurants::prepareContent($this);

		// extend pathway for breadcrumbs module
		$this->extendPathway($app);
		
		// display the template
		parent::display($tpl);
	}

	/**
	 * Returns the data needed to setup a gallery of images.
	 *
	 * @return  object
	 *
	 * @since   1.8.2
	 */
	protected function getGalleryData()
	{
		$gallery = new stdClass;
		$gallery->groupBy = 'menu';
		$gallery->images  = [];

		$translator = VREFactory::getTranslator();

		foreach ($this->menus as $menu)
		{
			$gallery->images[$menu->id] = [];

			foreach ($menu->products as $prod)
			{
				// check if the product owns more than one image
				if (count($prod->images) > 1)
				{
					// we should group the images by product, so that
					// the gallery can show only the images of the
					// selected item
					$gallery->groupBy = 'product';
				}

				// iterate all images
				foreach ($prod->images as $i => $image)
				{
					// obtain details for the specified image
					$media = $translator->translate('media', $image);

					// fetch gallery data
					$data = new stdClass;
					$data->thumb   = VREMEDIA_SMALL_URI . $image;
					$data->uri     = VREMEDIA_URI . $image;
					$data->caption = $media && $media->caption ? $media->caption : $prod->name;
					$data->id      = $prod->id;
					$data->idMenu  = $menu->id;

					// group by menu
					$gallery->images[$menu->id][] = $data;
				}
			}
		}

		if ($gallery->groupBy == 'menu')
		{
			return $gallery;
		}

		// assign gallery to a temporary variable
		$list = $gallery->images;

		// reset gallery
		$gallery->images = [];

		// iterate all menus
		foreach ($list as $id_menu => $images)
		{
			// iterate menu images
			foreach ($images as $image)
			{
				// create repository for current product if doesn't exist
				if (!isset($gallery->images[$image->id]))
				{
					$gallery->images[$image->id] = [];
				}

				// add data to gallery
				$gallery->images[$image->id][] = $image;
			}
		}

		return $gallery;
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
		if (!$this->filters['menu'])
		{
			// ignore if we are not filtering by menu
			return;
		}

		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		// get first available menu
		$menus = array_values($this->menus);

		$name = $menus[0]->title;
		$id   = $menus[0]->id;

		// Make sure this menu is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Item > Item
		if ($last && strpos($last->link, '&takeaway_menu=' . $id) === false)
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=takeaway&takeaway_menu=' . $id;
			$pathway->addItem($name, $link);
		}
	}
}
