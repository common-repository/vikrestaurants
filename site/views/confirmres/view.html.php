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
 * VikRestaurants restaurant reservation confirmation view.
 * Displayed only once the search results have been confirmed.
 * We are now able to see the selected table and, if supported,
 * the menus.
 *
 * @since 1.0
 */
class VikRestaurantsViewconfirmres extends JViewVRE
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
		
		$args = [];
		$args['date']    = $app->input->getString('date');
		$args['hourmin'] = $app->input->getString('hourmin');
		$args['people']  = $app->input->getUint('people');
		$args['table']   = $app->input->getUint('table', 0);

		// instantiate availability search
		$search = new VREAvailabilitySearch($args['date'], $args['hourmin'], $args['people']);

		if (!$args['table'])
		{
			// no specified tables, fetch all the available ones
			$availableTables = $search->getAvailableTables();

			if ($availableTables)
			{
				// pick the first available one
				$args['table'] = $availableTables[0]->id;
			}
		}

		// inject hours and minutes within $args
		$args['hour'] = $search->get('hour');
		$args['min']  = $search->get('min');

		// create time object based on check-in time
		$checkin = JHtml::fetch('vikrestaurants.min2time', $args['hour'] * 60 + $args['min'], $string = false);
		// include timestamp
		$checkin->ts = VikRestaurants::createTimestamp($args['date'], $args['hour'], $args['min']);

		// get selected table
		$table = $search->getTable($args['table']);

		if (!$table)
		{
			// the table must be specified (only in case the table/room selection is NOT optional)
			throw new Exception(sprintf('Invalid [%d] table', $args['table']), 500);
		}

		$table->room = new stdClass;
		$table->room->id   = $table->id_room;
		$table->room->name = $table->room_name;

		// translate room in case multi-lingual is supported
		VikRestaurants::translateRooms($table->room);

		// get total deposit to leave
		$deposit = JModelVRE::getInstance('rescart')->getTotalDeposit($args);

		/** @var E4J\VikRestaurants\Collection\Item[] */
		$payments = E4J\VikRestaurants\Payment\PaymentsCollection::getInstance()
			->filter(new E4J\VikRestaurants\Payment\Filters\RestaurantGroupFilter)
			->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter)
			->filter(new E4J\VikRestaurants\Payment\Filters\TotalCostFilter($deposit))
			->filter(new E4J\VikRestaurants\Payment\Filters\TrustedCustomerFilter('restaurant'))
			->filter(new E4J\VikRestaurants\Payment\Filters\PluginAvailabilityFilter('restaurant'));
		
		/**
		 * Retrieve custom fields for the restaurant section by using the related helper.
		 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter);

		// check if the system uses the coupon codes for the restaurant (0)
		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikrestaurants_coupons'))
			->where($dbo->qn('group') . ' = 0');

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();
		$any_coupon = (bool) $dbo->getNumRows();
		
		// get current customer details, if any
		$user = VikRestaurants::getCustomer();

		/**
		 * An associative array containing the check-in details,
		 * such as: date, hourmin, people and table.
		 * 
		 * @var array
		 */
		$this->args = $args;

		/**
		 * The time object for the selected check-in time.
		 *
		 * @var object
		 */
		$this->checkinTime = $checkin;

		/**
		 * An object containined the details of the table
		 * that have been selected in the previous step.
		 *
		 * @var object|null
		 * 
		 * @since 1.9 The table can be null in case the table selection is optional.
		 */
		$this->table = $table;
		
		/**
		 * An array of custom fields to use for collecting
		 * the billing details of the customer.
		 *
		 * @var array
		 */
		$this->customFields = $customFields;

		/**
		 * A list of payments available for the purchase.
		 *
		 * @var object[]
		 */
		$this->payments = $payments;

		/**
		 * Flag used to check whether the restaurant
		 * section of the websites uses the coupons.
		 *
		 * @var bool
		 */
		$this->anyCoupon = $any_coupon;

		/**
		 * The billing details of the logged-in user.
		 *
		 * @var object|null
		 */
		$this->user = $user;

		/**
		 * The total deposit to leave to confirm the
		 * restaurant reservation.
		 *
		 * @var float
		 */
		$this->totalDeposit = $deposit;

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

		// Make sure this menu is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Item > Item
		if ($last && strpos($last->link, '&view=confirmres') === false)
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=confirmres&' . http_build_query($this->args);
			$pathway->addItem(JText::translate('VRSTEPTHREESUBTITLE'), $link);
		}
	}
}
