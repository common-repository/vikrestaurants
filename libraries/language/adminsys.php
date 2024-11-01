<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  language
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.language.handler');

/**
 * Switcher class to translate the VikRestaurants plugin common languages.
 *
 * @since 	1.0
 */
class VikRestaurantsLanguageAdminSys implements JLanguageHandler
{
	/**
	 * Checks if exists a translation for the given string.
	 *
	 * @param 	string 	$string  The string to translate.
	 *
	 * @return 	string 	The translated string, otherwise null.
	 */
	public function translate($string)
	{
		$result = null;

		/**
		 * Translations go here.
		 * @tip Use 'TRANSLATORS:' comment to attach a description of the language.
		 */

		switch ($string)
		{
			/**
			 * VikRestaurants core platform.
			 */

			case 'COM_VIKRESTAURANTS':
			case 'COM_VIKRESTAURANTS_MENU':
			case 'COM_VIKRESTAURANTS_MENU_RESTAURANTS':
				$result = __('VikRestaurants', 'vikrestaurants');
				break;

			/**
			 * Restaurant search form view.
			 */

			case 'COM_VIKRESTAURANTS_RESTAURANTS_VIEW_DEFAULT_TITLE':
				$result = __('Reservation Form', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_RESTAURANTS_VIEW_DEFAULT_DESC':
				$result = __('Shows the form to start reserving a table of the restaurant.', 'vikrestaurants');
				break;

			/**
			 * Take-away order details view.
			 */

			case 'COM_VIKRESTAURANTS_ORDER_VIEW_DEFAULT_TITLE':
				$result = __('Order', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_ORDER_VIEW_DEFAULT_DESC':
				$result = __('View used to access the details of a take-away order.', 'vikrestaurants');
				break;

			/**
			 * Restaurant menus list view.
			 */

			case 'COM_VIKRESTAURANTS_MENUSLIST_VIEW_DEFAULT_TITLE':
				$result = __('Menus List', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_VIEW_DEFAULT_DESC':
				$result = __('Shows the form to view the available menus of the restaurant.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_FIELD_SHOW_BAR':
				$result = __('Show Search Bar', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_FIELD_SHOW_BAR_DESC':
				$result = __('Enable this option in case you want to allow the users to filter the menus by date.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_FIELD_SELECT_ITEMS':
				$result = __('Select Menus', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_FIELD_SELECT_ITEMS_DESC':
				$result = __('Select all the menus you want to display. Leave this field empty to display all the available menus.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_FIELD_PRINTABLE_MENUS':
				$result = __('Printable Menus', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUSLIST_FIELD_PRINTABLE_MENUS_DESC':
				$result = __('Turn on this option whether the customers should be able to print the menus.', 'vikrestaurants');
				break;

			/**
			 * Take-away menus list view.
			 */

			case 'COM_VIKRESTAURANTS_TAKEAWAY_VIEW_DEFAULT_TITLE':
				$result = __('Take-Away Menus List', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_TAKEAWAY_VIEW_DEFAULT_DESC':
				$result = __('Shows the list of the take-away menus to order your products.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_TKMENU_FIELD_SELECT_TITLE':
				$result = __('Filter menu', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_TKMENU_FIELD_SELECT_TITLE_DESC':
				$result = __('Leave this field blank to show all the menus.', 'vikrestaurants');
				break;

			/**
			 * Restaurants menu details view.
			 */

			case 'COM_VIKRESTAURANTS_MENUDETAILS_VIEW_DEFAULT_TITLE':
				$result = __('Menu Details', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENUDETAILS_VIEW_DEFAULT_DESC':
				$result = __('Shows the sections and the products of the selected menu.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENU_FIELD_SELECT_TITLE':
				$result = __('Menu', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENU_FIELD_SELECT_TITLE_DESC':
				$result = __('Choose the details of the menu to display.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENU_FIELD_PRINTABLE_MENU':
				$result = __('Printable Menu', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_MENU_FIELD_PRINTABLE_MENU_DESC':
				$result = __('Turn on this option whether the customers should be able to print this menu.', 'vikrestaurants');
				break;

			/**
			 * Operators area view.
			 */

			case 'COM_VIKRESTAURANTS_OVERSIGHT_VIEW_DEFAULT_TITLE':
				$result = __('Operators Area', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_OVERSIGHT_VIEW_DEFAULT_DESC':
				$result = __('Shows the management area in the front-end for the operators of the restaurant.', 'vikrestaurants');
				break;

			/**
			 * Customer orders history view.
			 */

			case 'COM_VIKRESTAURANTS_ALLORDERS_VIEW_DEFAULT_TITLE':
				$result = __('All Orders', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_ALLORDERS_VIEW_DEFAULT_DESC':
				$result = __('Shows the page containing all the orders and reservations made by a logged in user.', 'vikrestaurants');
				break;

			/**
			 * Take-away item details view.
			 */

			case 'COM_VIKRESTAURANTS_TAKEAWAYITEM_VIEW_DEFAULT_TITLE':
				$result = __('Take-Away Item Details', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_TAKEAWAYITEM_VIEW_DEFAULT_DESC':
				$result = __('Shows the details page of a single Take-Away item.', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_TKITEM_FIELD_SELECT_TITLE':
				$result = __('Product', 'vikrestaurants');
				break;

			/**
			 * Table reservation confirmation view.
			 */

			case 'COM_VIKRESTAURANTS_CONFIRMRES_VIEW_DEFAULT_TITLE':
				$result = __('Confirm Reservation', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_CONFIRMRES_VIEW_DEFAULT_DESC':
				$result = __('Shows the confirmation page for the table bookings (useful for SEO).', 'vikrestaurants');
				break;

			/**
			 * take-away order confirmation view.
			 */

			case 'COM_VIKRESTAURANTS_TAKEAWAYCONFIRM_VIEW_DEFAULT_TITLE':
				$result = __('Confirm Take-Away Order', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_TAKEAWAYCONFIRM_VIEW_DEFAULT_DESC':
				$result = __('Shows the confirmation page for the take-away orders (useful for SEO).', 'vikrestaurants');
				break;

			/**
			 * Restaurant reservation details view.
			 */

			case 'COM_VIKRESTAURANTS_RESERVATION_VIEW_DEFAULT_TITLE':
				$result = __('Reservation Details', 'vikrestaurants');
				break;

			case 'COM_VIKRESTAURANTS_RESERVATION_VIEW_DEFAULT_DESC':
				$result = __('View used to access the details of a restaurant reservation.', 'vikrestaurants');
				break;

			/**
			 * ACL rules (access.xml)
			 */
			
			case 'VR_ACCESS_ROOMS':
				$result = __('Rooms View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_ROOMS_DESC':
				$result = __('This rule allows the users to access to the rooms view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TABLES':
				$result = __('Tables View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TABLES_DESC':
				$result = __('This rule allows the users to access to the tables view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_MAPS':
				$result = __('Tables Maps View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_MAPS_DESC':
				$result = __('This rule allows the users to access to the tables maps view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_OPERATORS':
				$result = __('Operators View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_OPERATORS_DESC':
				$result = __('This rule allows the users to access to the operators view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_SHIFTS':
				$result = __('Working Shifts View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_SHIFTS_DESC':
				$result = __('This rule allows the users to access to the working shifts view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_PRODUCTS':
				$result = __('Products View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_PRODUCTS_DESC':
				$result = __('This rule allows the users to access to the products view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_MENUS':
				$result = __('Restaurant Menus View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_MENUS_DESC':
				$result = __('This rule allows the users to access to the restaurant menus view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_SPECIALDAYS':
				$result = __('Special Days View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_SPECIALDAYS_DESC':
				$result = __('This rule allows the users to access to the special days view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_RESERVATIONS':
				$result = __('Reservations View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_RESERVATIONS_DESC':
				$result = __('This rule allows the users to access to the reservations view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_CUSTOMERS':
				$result = __('Customers View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_CUSTOMERS_DESC':
				$result = __('This rule allows the users to access to the customers view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_REVIEWS':
				$result = __('Reviews View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_REVIEWS_DESC':
				$result = __('This rule allows the users to access to the reviews view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_COUPONS':
				$result = __('Coupons View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_COUPONS_DESC':
				$result = __('This rule allows the users to access to the coupons view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_INVOICES':
				$result = __('Invoices View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_INVOICES_DESC':
				$result = __('This rule allows the users to access to the invoices view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_DASHBOARD':
				$result = __('Dashboard View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_DASHBOARD_DESC':
				$result = __('This rule allows the users to access to the Dashboard view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKMENUS':
				$result = __('Take-Away Menus View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKMENUS_DESC':
				$result = __('This rule allows the users to access to the take-away menus view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKTOPPINGS':
				$result = __('Take-Away Toppings View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKTOPPINGS_DESC':
				$result = __('This rule allows the users to access to the take-away toppings view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKDEALS':
				$result = __('Take-Away Deals View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKDEALS_DESC':
				$result = __('This rule allows the users to access to the take-away deals view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKAREAS':
				$result = __('Take-Away Delivery Areas View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKAREAS_DESC':
				$result = __('This rule allows the users to access to the take-away delivery areas view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKORDERS':
				$result = __('Take-Away Orders View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TKORDERS_DESC':
				$result = __('This rule allows the users to access to the take-away orders view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_CUSTFIELDS':
				$result = __('Custom Fields View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_CUSTFIELDS_DESC':
				$result = __('This rule allows the users to access to the custom fields view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_PAYMENTS':
				$result = __('Payments View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_PAYMENTS_DESC':
				$result = __('This rule allows the users to access to the payments view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_STATUSCODES':
				$result = __('Status Codes View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_STATUSCODES_DESC':
				$result = __('This rule allows the users to access to the status codes view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_MEDIA':
				$result = __('Media Manager View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_MEDIA_DESC':
				$result = __('This rule allows the users to access to the media manager view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TAXES':
				$result = __('Taxes View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_TAXES_DESC':
				$result = __('This rule allows the users to access to the taxes view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_RESCODES':
				$result = __('Reservation Codes View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_RESCODES_DESC':
				$result = __('This rule allows the users to access to the reservation codes view.', 'vikrestaurants');
				break;

			case 'VR_ACCESS_CONFIG':
				$result = __('Configuration View', 'vikrestaurants');
				break;

			case 'VR_ACCESS_CONFIG_DESC':
				$result = __('This rule allows the users to access to the configuration view.', 'vikrestaurants');
				break;
		}

		return $result;
	}
}
