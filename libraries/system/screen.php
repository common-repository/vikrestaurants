<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  system
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class to setup the WordPress Screen.
 *
 * @since 1.0
 */
class VikRestaurantsScreen
{
	/**
	 * The type of options to display.
	 * This property can be edited externally.
	 *
	 * @var boolean
	 */
	public static $optionsType = null;

	/**
	 * Creates the option section within the WP Screen for VikRestaurants.
	 *
	 * @return 	void
	 */
	public static function options()
	{
		$app = JFactory::getApplication();

		// make sure we are in VikRestaurants (back-end)
		if (!$app->isAdmin() || $app->input->get('page') != 'vikrestaurants')
		{
			// abort
			return;
		}

		// check if we should display screen options for listing pages
		if (static::$optionsType == 'list')
		{
			// create pagination option
		    $args = array(
		        'label'   => __('Number of items per page:'),
		        'default' => 20,
		        'option'  => 'vikrestaurants_list_limit',
		    );
		 
		    add_screen_option('per_page', $args);
		}
	}

	/**
	 * Filters a screen option value before it is set.
	 *
	 * @param 	boolean  $skip    Whether to save or skip saving the screen option value. Default false.
	 * @param 	string   $option  The option name.
	 * @param 	mixed    $value   The option value.
	 *
	 * @return  mixed    Returning false to the filter will skip saving the current option.
	 */
	public static function saveOption($skip, $option, $value)
	{
		$lookup = array(
			'vikrestaurants_list_limit',
		);

		if (in_array($option, $lookup))
		{
			// return value to save it
			return $value;
		}

		// skip otherwise
		return $skip;
	}

	/**
	 * Creates the Help tabs within the WP Screen for VikRestaurants.
	 *
	 * @param 	WP_Screen  $screen  The current screen instance.
	 *
	 * @return 	void
	 */
	public static function help($screen = null)
	{
		$app = JFactory::getApplication();

		// make sure we are in VikRestaurants (back-end)
		if (!$app->isAdmin() || $app->input->get('page') != 'vikrestaurants')
		{
			// abort
			return;
		}

		// make sure $screen is a valid instance
		if (!class_exists('WP_Screen') || !$screen instanceof WP_Screen)
		{
			if (VIKRESTAURANTS_DEBUG)
			{
				// trigger warning in case debug is enabled
				trigger_error('Method ' . __METHOD__ . ' has been called too early', E_USER_WARNING);
			}
			// abort
			return;
		}

		// extract view from request
		$view = $app->input->get('view', null);

		if (empty($view))
		{
			// no view, try to check 'task'
			$view = $app->input->get('task', 'restaurant');
		}

		// add specific conditions
		switch ($view)
		{
			case 'exportres':
				// recover export data from user state
				$data = $app->getUserState('vre.exportres.data', array());
				
				// set type to avoid notices
				$data['type'] = isset($data['type']) ? $data['type'] : null;

				// append group to exportres view
				$view .= '.' . JHtml::fetch('vrehtml.admin.getgroup', $data['type'], array('restaurant', 'takeaway'));
		}

		// make sure the view is supported
		if (!isset(static::$lookup[$view]))
		{
			// view not supported
			return;
		}

		// check if we have a link to an existing item
		if (is_string(static::$lookup[$view]))
		{
			// use the linked element
			$view = static::$lookup[$view];
		}

		// check if the view documentation has been already cached
		$doc = get_transient('vikrestaurants_screen_' . $view);

		if (!$doc)
		{
			// evaluate if we should stop using HELP tabs after 3 failed attempts
			$fail = (int) get_option('vikrestaurants_screen_failed_attempts', 0);

			if ($fail >= 5)
			{
				// Do not proceed as we hit too many failure attempts contiguously.
				// Reset 'vikrestaurants_screen_failed_attempts' option to restart using HELP tabs.
				return;
			}

			// create POST arguments
			$args = array(
				'documentation_alias' => 'vik-restaurants',
				'lang'                => substr(JFactory::getLanguage()->getTag(), 0, 2),
			);

			// build headers
			$headers = array(
				/**
				 * Always bypass SSL validation while reaching our end-point.
				 *
				 * @since 1.2.3
				 */
				'sslverify' => false,
			);

			$args = array_merge($args, static::$lookup[$view]);

			$http = new JHttp();

			// make HTTP post
			$response = $http->post('https://vikwp.com/index.php?option=com_vikhelpdesk&format=json', $args, $headers);

			if ($response->code != 200)
			{
				// increase total number of failed attempts
				update_option('vikrestaurants_screen_failed_attempts', $fail + 1);

				return;
			}

			// try to decode JSON
			$doc = json_decode($response->body);

			if (!is_array($doc))
			{
				// increase total number of failed attempts
				update_option('vikrestaurants_screen_failed_attempts', $fail + 1);

				return;
			}

			// reset total number of failed attempts
			update_option('vikrestaurants_screen_failed_attempts', 0);

			// cache retrieved documentation (for one week only)
			set_transient('vikrestaurants_screen_' . $view, json_encode($doc), WEEK_IN_SECONDS);
		}
		else
		{
			// JSON decode the cached documentation
			$doc = json_decode($doc);
		}

		// iterate category sections
		foreach ($doc as $i => $cat)
		{
			// add subcategory as help tab
			$screen->add_help_tab(array(
				'id'       => 'vikrestaurants-' . $view . '-' . ($i + 1),
				'title'    => $cat->contentTitle,
				'content'  => $cat->content,
			));
		}

		// add help sidebar
		$screen->set_help_sidebar(
			'<p><strong>' . __('For more information:') . '</strong></p>' .
			'<p><a href="https://vikwp.com/support/documentation/vik-restaurants/" target="_blank">VikWP.com</a></p>'
		);
	}

	/**
	 * Clears the cache for the specified view, if specified.
	 *
	 * @param 	string|null  $view  Clear the cache for the specified view (if specified)
	 * 								or for all the existing views.
	 *
	 * @return 	void
	 */
	public static function clearCache($view = null)
	{
		if ($view)
		{
			delete_transient('vikrestaurants_screen_' . $view);
		}
		else
		{
			foreach (static::$lookup as $view => $args)
			{
				if (is_array($args))
				{
					delete_transient('vikrestaurants_screen_' . $view);
				}
			}

			// delete settings too
			delete_option('vikrestaurants_screen_failed_attempts');
			delete_option('vikrestaurants_list_limit');
		}
	}

	/**
	 * Lookup used to retrieve the arguments for the HTTP request.
	 *
	 * @var array
	 */
	protected static $lookup = array(
		////////////////////////////////
		/////////// DASHBOARD //////////
		////////////////////////////////

		// dashboard
		'restaurant' => array(
			'task'          => 'documentation.category',
			'category_name' => 'dashboard',
		),

		// manage widgets
		'managestatistics' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'dashboard',
			'subcategory_name' => 'customization',
		),

		////////////////////////////////
		////////// RESTAURANT //////////
		////////////////////////////////

		// rooms
		'rooms' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'rooms',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage room
		'manageroom' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'rooms',
			'content_name'     => 'parameters',
		),

		// room closures
		'roomclosures' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'rooms',
			'content_name'     => 'closures',
		),

		// manage room closure
		'manageroomclosure' => 'roomclosures',

		// tables
		'tables' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'tables',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage table
		'managetable' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'tables',
			'content_name'     => array(
				'parameters',
				'clusters',
			),
		),

		// tables maps
		'maps' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'tables maps',
			'content_name'     => array(
				'availability overview',
				'reservations management',
				'inspector details',
			),
		),

		// products
		'menusproducts' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'products',
			'content_name'     => array(
				'search tools',
				'hidden',
				'tags',
			),
		),

		// manage product
		'managemenusproduct' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'products',
			'content_name'     => array(
				'parameters',
			),
		),

		// menus
		'menus' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'menus',
			'content_name'     => array(
				'search tools',
				'preview',
			),
		),

		// manage menu
		'managemenu' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'menus',
			'content_name'     => array(
				'parameters',
				'sections',
				'add products',
			),
		),

		// reservations
		'reservations' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'reservations',
			'content_name'     => array(
				'search tools',
				'print reservations',
				'sms notifications',
				'invoices',
				'order statuses',
			),
		),

		// manage reservation
		'managereservation' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'reservations',
			'content_name'     => array(
				'parameters',
			),
		),

		// manage bill
		'managebill' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'reservations',
			'content_name'     => array(
				'bill management',
			),
		),

		// export reservations
		'exportres.restaurant' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'restaurant',
			'subcategory_name' => 'reservations',
			'content_name'     => array(
				'export',
			),
		),

		////////////////////////////////
		////////// OPERATIONS //////////
		////////////////////////////////

		// working shifts
		'shifts' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'operations',
			'subcategory_name' => 'working shifts',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage working shift
		'manageshift' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'operations',
			'subcategory_name' => 'working shifts',
			'content_name'     => array(
				'parameters',
			),
		),

		// special days
		'specialdays' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'operations',
			'subcategory_name' => 'special days',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage special day
		'managespecialday' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'operations',
			'subcategory_name' => 'special days',
			'content_name'     => array(
				'parameters',
				'restaurant',
				'take-away',
			),
		),

		// operators
		'operators' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'operations',
			'subcategory_name' => 'operators',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage operator
		'manageoperator' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'operations',
			'subcategory_name' => 'operators',
			'content_name'     => array(
				'parameters',
				'actions',
			),
		),

		////////////////////////////////
		/////////// BOOKING ////////////
		////////////////////////////////

		// customers
		'customers' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'booking',
			'subcategory_name' => 'customers',
			'content_name'     => array(
				'search tools',
				'sms notifications',
			),
		),

		// manage customer
		'managecustomer' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'booking',
			'subcategory_name' => 'customers',
			'content_name'     => array(
				'billing details',
				'delivery locations',
				'custom fields',
			),
		),

		// reviews
		'reviews' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'booking',
			'subcategory_name' => 'reviews',
			'content_name'     => array(
				'search tools',
				'reviews listing',
				'reviews leave',
			),
		),

		// manage review
		'managereview' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'booking',
			'subcategory_name' => 'reviews',
			'content_name'     => array(
				'parameters',
			),
		),

		// coupons
		'coupons' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'booking',
			'subcategory_name' => 'coupons',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage coupon
		'managecoupon' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'booking',
			'subcategory_name' => 'coupons',
			'content_name'     => array(
				'parameters',
			),
		),

		// invoices
		'invoices' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'booking',
			'subcategory_name' => 'invoices',
			'content_name'     => array(
				'search tools',
				'generation tip',
				'template customization',
			),
		),

		// manage invoice
		'manageinvoice' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'booking',
			'subcategory_name' => 'invoices',
			'content_name'     => array(
				'invoices generation',
				'invoices layout',
			),
		),

		////////////////////////////////
		/////////// TAKEAWAY ///////////
		////////////////////////////////

		// menus
		'tkmenus' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'menus',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage menu
		'managetkmenu' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'menus',
			'content_name'     => array(
				'parameters',
				'products',
				'variations',
			),
		),

		// products
		'tkproducts' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'menus',
			'content_name'     => array(
				'products',
			),
		),

		// manage product
		'managetkentry' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'menus',
			'content_name'     => array(
				'products',
				'variations',
				'toppings groups',
			),
		),

		// food attributes
		'tkmenuattr' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'food attributes',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage food attribute
		'managetkmenuattr' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'food attributes',
			'content_name'     => array(
				'parameters',
			),
		),

		// toppings
		'tktoppings' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'toppings',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage topping
		'managetktopping' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'toppings',
			'content_name'     => array(
				'parameters',
				'price quick update',
			),
		),

		// deals
		'tkdeals' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'deals',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage deal
		'managetkdeal' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'deals',
			'content_name'     => array(
				'parameters',
			),
		),

		// delivery areas
		'tkareas' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'delivery areas',
			'content_name'     => array(
				'purpose',
				'search tools',
			),
		),

		// manage delivery area
		'managetkarea' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'delivery areas',
			'content_name'     => array(
				'parameters',
				'attributes',
				'polygon',
				'circle',
				'zip codes',
			),
		),

		// orders
		'tkreservations' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'orders',
			'content_name'     => array(
				'search tools',
				'print orders',
				'sms notifications',
				'invoices',
				'order statuses',
			),
		),

		// manage order
		'managetkreservation' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'orders',
			'content_name'     => array(
				'parameters',
			),
		),

		// manage cart
		'managetkrescart' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'orders',
			'content_name'     => array(
				'cart',
			),
		),

		// manage bill
		'tkdiscord' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'orders',
			'content_name'     => array(
				'bill management',
			),
		),

		// export orders
		'exportres.takeaway' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'orders',
			'content_name'     => array(
				'export',
			),
		),

		// manage menu stocks
		'tkmenustocks' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'stocks',
			'content_name'     => array(
				'manage menu stocks',
			),
		),

		// stocks overview
		'tkstocks' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'take-away',
			'subcategory_name' => 'stocks',
			'content_name'     => array(
				'stocks overview',
			),
		),

		// stocks statistics
		'tkstatstocks' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'take-away',
			'subcategory_name' => 'stocks',
			'content_name'     => array(
				'stocks statistics',
			),
		),

		////////////////////////////////
		//////////// GLOBAL ////////////
		////////////////////////////////

		// custom fields
		'customf' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'global',
			'subcategory_name' => 'custom fields',
			'content_name'     => array(
				'search tools',
				'override',
			),
		),

		// manage custom field
		'managecustomf' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'global',
			'subcategory_name' => 'custom fields',
			'content_name'     => array(
				'parameters',
				'text type',
				'textarea type',
				'date type',
				'select type',
				'checkbox type',
				'separator type',
				'nominative rule',
				'e-mail rule',
				'phone number rule',
				'address rule',
				'city rule',
				'zip code rule',
				'delivery rule',
				'pickup rule',
			),
		),

		// payments
		'payments' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'global',
			'subcategory_name' => 'payments',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage payment
		'managepayment' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'global',
			'subcategory_name' => 'payments',
			'content_name'     => array(
				'parameters',
				'paypal',
				'offline credit card',
				'bank transfer',
			),
		),

		// media manager
		'media' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'global',
			'subcategory_name' => 'media manager',
			'content_name'     => array(
				'dialog',
			),
		),

		// new media
		'newmedia' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'global',
			'subcategory_name' => 'media manager',
			'content_name'     => array(
				'media properties',
				'quick upload',
			),
		),

		// manage media
		'managemedia' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'global',
			'subcategory_name' => 'media manager',
			'content_name'     => array(
				'manage media',
			),
		),

		// reservation codes
		'rescodes' => array(
			'task'             => 'documentation.subcategory',
			'category_name'    => 'global',
			'subcategory_name' => 'reservation codes',
			'content_name'     => array(
				'search tools',
			),
		),

		// manage reservation code
		'managerescode' => array(
			'task'             => 'documentation.content',
			'category_name'    => 'global',
			'subcategory_name' => 'reservation codes',
			'content_name'     => array(
				'parameters',
				'arrived',
				'close bill',
				'completed',
				'cooking',
				'invoice',
				'leave',
				'order dishes',
				'prepared',
				'preparing',
				'waiter',
			),
		),
	);
}

/**
 * In case VikRestaurants displayed the menu, we are probably 
 * visiting a page with a list. For this reason, we should alter
 * the VikRestaurantsScreen::$optionsType to display a specific
 * screen options form.
 */
add_action('vikrestaurants_before_build_menu', function()
{
	VikRestaurantsScreen::$optionsType = 'list';
});
