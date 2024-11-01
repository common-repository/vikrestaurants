<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_items
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Items widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_ItemsLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Items module.
			 */

			case 'VIKRESTAURANTS_ITEMS_MODULE_TITLE':
				$result = __('VikRestaurants Items', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_ITEMS_MODULE_DESCRIPTION':
				$result = __('This widget allows you to choose which products you want to point out in your website.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_PRODUCTS':
				$result = __('Products', 'vikrestaurants');
				break;

			case 'VIKREITEMS_PRODUCTSDESC':
				$result = __('Choose the products to show.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_SHOWTITLE':
				$result = __('Show Title', 'vikrestaurants');
				break;

			case 'VIKREITEMS_SHOWDESC':
				$result = __('Show Description', 'vikrestaurants');
				break;

			case 'VIKREITEMS_SHOWIMAGE':
				$result = __('Show Image', 'vikrestaurants');
				break;

			case 'VIKREITEMS_SHOWPRICE':
				$result = __('Show Price', 'vikrestaurants');
				break;

			case 'VIKREITEMS_BACKCOLOR':
				$result = __('Title Color', 'vikrestaurants');
				break;

			case 'VIKREITEMS_BACKCOLORDESC':
				$result = __('Choose the color for the title and for the price background.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_COLOR':
				$result = __('Price Text Color', 'vikrestaurants');
				break;

			case 'VIKREITEMS_COLORDESC':
				$result = __('Choose your base color for the price label.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_NUMBROW':
				$result = __('Items per Row', 'vikrestaurants');
				break;

			case 'VIKREITEMS_NUMBROWDESC':
				$result = __('The number of items to display simultaneously.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_DOTNAV':
				$result = __('Dotted Navigation', 'vikrestaurants');
				break;

			case 'VIKREITEMS_DOTNAVDESC':
				$result = __('Choose if you want to show the dotted navigation.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_ARROWS':
				$result = __('Pagination', 'vikrestaurants');
				break;

			case 'VIKREITEMS_ARROWSDESC':
				$result = __('Choose if you want to enable the pagination buttons.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_AUTOPLAY':
				$result = __('Autoplay', 'vikrestaurants');
				break;

			case 'VIKREITEMS_AUTOPLAYDESC':
				$result = __('Enable automatic horizontal scrolling.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_TIMESCROLL':
				$result = __('Time Scrolling', 'vikrestaurants');
				break;

			case 'VIKREITEMS_TIMESCROLLDESC':
				$result = __('Autoplay Time Scrolling in milliseconds.', 'vikrestaurants');
				break;

			case 'VIKREITEMS_RANDOMIZE':
				$result = __('Randomize', 'vikrestaurants');
				break;

			case 'VIKREITEMS_RANDOMIZEDESC':
				$result = __('Choose whether the products should be displayed with a random ordering.', 'vikrestaurants');
				break;
				
			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
