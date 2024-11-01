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
 * VikRestaurants dishes ordering view.
 * Here a owner of a reservation is able to self-order
 * the dishes for the whole group.
 *
 * @since 1.8
 */
class VikRestaurantsVieworderdishes extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$dbo    = JFactory::getDbo();
		$config = VREFactory::getConfig();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		if (empty($oid) || empty($sid))
		{
			// missing required fields
			throw new Exception(JText::translate('VRORDERRESERVATIONERROR'), 400);
		}

		// Get reservation details.
		// In case the reservation doesn't exist, an exception will be thrown.
		$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

		if ($reservation->menus)
		{
			// use menus assigned to the reservation
			$menus = [];

			// extract IDs from menus list
			$menu_ids = array_map(function($m) {
				return (int) $m->id;
			}, $reservation->menus);

			// recover menu details from DB
			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikrestaurants_menus'))
				->where($dbo->qn('id') . ' IN (' . implode(',', $menu_ids) . ')')
				->order($dbo->qn('ordering') . ' ASC');

			$dbo->setQuery($q);
			$menus = $dbo->loadObjectList();
		}
		else
		{
			$args = [
				'date'    => date($config->get('dateformat'), $reservation->checkin_ts),
				'hourmin' => date('H:i', $reservation->checkin_ts),
			];

			// recover all the menus available for the reservation check-in
			$menus = VikRestaurants::getAllAvailableMenusOn($args);
		}

		// iterate all menus to obtain the related sections and products
		foreach ($menus as $menu)
		{
			// load all available sections and products
			$menu->sections = $this->loadSections($menu->id);
		}

		// filter the menu to exclude all the menus without sections
		$menus = array_values(array_filter($menus, function($m)
		{
			return count($m->sections) > 0;
		}));

		if (!$menus)
		{
			// no menus available, raise warning
			$app->enqueueMessage(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 'warning');
		}

		// build payment URL
		$pay_url = 'index.php?option=com_vikrestaurants&view=reservation&ordnum=' . $reservation->id . '&ordkey=' . $reservation->sid . '#payment';
		$pay_url = JRoute::rewrite($pay_url, false);

		// check if the user is allowed to reserve dishes
		$can_order = VikRestaurants::canUserOrderFood($reservation, $error);

		if (!$can_order && $error)
		{
			// display error as a system message
			$app->enqueueMessage($error, 'error');

			if ($reservation->bill_closed && $reservation->id_payment)
			{
				// back to summary page in case the payment method
				// has been already selected
				$app->redirect($pay_url);
				exit;
			}
		}

		// get cart instance
		$cart = E4J\VikRestaurants\OrderDishes\Cart::getInstance($reservation->id);

		/** @var E4J\VikRestaurants\Collection\Item[] */
		$payments = E4J\VikRestaurants\Payment\PaymentsCollection::getInstance()
			->filter(new E4J\VikRestaurants\Payment\Filters\RestaurantGroupFilter)
			->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter)
			->filter(new E4J\VikRestaurants\Payment\Filters\TrustedCustomerFilter('restaurant'))
			->filter(new E4J\VikRestaurants\Payment\Filters\PluginAvailabilityFilter('restaurant'));
		
		/**
		 * An object containing the details of the specified
		 * restaurant reservation.
		 * 
		 * @var VREOrderRestaurant
		 */
		$this->reservation = $reservation;

		/**
		 * The instance of the cart that contains all the
		 * dishes that have been selected.
		 *
		 * @var E4J\VikRestaurants\OrderDishes\Cart
		 */
		$this->cart = $cart;

		/**
		 * A list containing all the menus that can be accessed
		 * by the owner of this reservation.
		 * 
		 * @var array
		 */
		$this->menus = $menus;

		/**
		 * Flag used to check whether the customer is currently
		 * allowed to order the dishes for its reservation.
		 *
		 * @var bool
		 */
		$this->canOrder = $can_order;

		/**
		 * The URL needed to reach to complete the payment.
		 *
		 * @var string
		 * @since 1.8.1
		 */
		$this->paymentURL = $pay_url;

		/**
		 * A list of available payment gateways.
		 *
		 * @var array
		 * @since 1.8.1
		 */
		$this->payments = $payments;

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
	 * Loads all the published sections and products that belong
	 * to the specified menu ID.
	 *
	 * @param 	integer  $id  The menu ID.
	 *
	 * @return 	array
	 */
	protected function loadSections($id)
	{
		$menu = JModelVRE::getInstance('menudetails')->getMenu($id, [
			'orderdishes' => true,
		]);

		// do not take sections without products
		$sections = array_filter($menu->sections, function($s)
		{
			return count($s->products) > 0;
		});

		return array_values($sections);
	}

	/**
	 * Extends the pathway for breadcrumbs module.
	 *
	 * @param 	mixed 	$app  The application instance.
	 *
	 * @return 	void
	 *
	 * @since 	1.9
	 */
	protected function extendPathway($app)
	{
		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		// Make sure the reservation page is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Reservation > [ORDNUM]-[ORDKEY]
		if ($last && strpos($last->link, '&view=reservation') === false && !empty($this->reservation))
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=reservation&ordnum=' . $this->reservation->id . '&ordkey=' . $this->reservation->sid;
			$pathway->addItem($this->reservation->id . '-' . $this->reservation->sid, $link);
		}

		// register link into the Breadcrumb
		$link = 'index.php?option=com_vikrestaurants&view=orderdishes&ordnum=' . $this->reservation->id . '&ordkey=' . $this->reservation->sid;
		$pathway->addItem(JText::translate('VREORDERFOOD'), $link);
	}
}
