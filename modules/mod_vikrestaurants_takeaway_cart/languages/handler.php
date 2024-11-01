<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_cart
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Take-Away Cart widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_Takeaway_CartLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Take-away cart module.
			 */

			case 'VIKRESTAURANTS_TAKEAWAY_CART_MODULE_TITLE':
				$result = __('VikRestaurants Take-Away Cart', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_TAKEAWAY_CART_MODULE_DESCRIPTION':
				$result = __('This widget displays the shopping cart of the user.', 'vikrestaurants');
				break;

			case 'VRFREE':
				$result = __('Free', 'vikrestaurants');
				break;

			case 'VRTKMODCARTMINORDER':
				$result = __('Minimum Order:', 'vikrestaurants');
				break;

			case 'VRTKMODCARTTOTALPRICE':
				$result = __('Total:', 'vikrestaurants');
				break;

			case 'VRTKMODCARTTOTALDISCOUNT':
				$result = __('Discount:', 'vikrestaurants');
				break;

			case 'VRTKMODEMPTYBUTTON':
				$result = __('Empty', 'vikrestaurants');
				break;

			case 'VRTKMODORDERBUTTON':
				$result = __('Order Now', 'vikrestaurants');
				break;

			case 'VRTKMODQUANTITYSUFFIX':
				$result = __('x', 'vikrestaurants');
				break;

			case 'VRTK_CART_TITLE':
				$result = __('Cart Widget Title', 'vikrestaurants');
				break;

			case 'VRTK_CART_TITLE_DESC':
				$result = __('A second title displayed inside the widget. If you have the "Follow Page Scroll" parameter enabled, it is recommended to use this parameter instead of the default block title.', 'vikrestaurants');
				break;

			case 'VRTK_CART_FOLLOWSCROLL':
				$result = __('Follow Page Scroll', 'vikrestaurants');
				break;

			case 'VRTK_CART_FOLLOWSCROLL_DESC':
				$result = __('When enabled, if your theme will not have conflicts with this css rule, the widget will follow the page when scrolling. Enable this option is you can place this widget on a sidebar.', 'vikrestaurants');
				break;

			case 'VRTK_CART_PADDINGTOP':
				$result = __('Top Padding', 'vikrestaurants');
				break;

			case 'VRTK_CART_PADDINGTOP_DESC':
				$result = __('In case the module is hidden behind a sticky element (such as the theme header), you can try to increase this value until the module is properly displayed.', 'vikrestaurants');
				break;

			case 'VRTK_CART_MOBILESTICKY':
				$result = __('Mobile Sticky', 'vikrestaurants');
				break;

			case 'VRTK_CART_MOBILESTICKY_DESC':
				$result = __('Enable this option to use a sticky version of the widget for mobile devices. The widget will be stuck on the bottom side of the screen only for the take-away menus list page.', 'vikrestaurants');
				break;
				
			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
