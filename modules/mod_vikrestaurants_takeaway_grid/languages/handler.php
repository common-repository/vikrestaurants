<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_grid
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Take-Away Grid widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_Takeaway_GridLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Take-away grid module.
			 */

			case 'VIKRESTAURANTS_TAKEAWAY_GRID_MODULE_TITLE':
				$result = __('VikRestaurants Take-Away Grid', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_TAKEAWAY_GRID_MODULE_DESCRIPTION':
				$result = __('This widget displays a grid of some take-away products.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_ALL_MENUS':
				$result = __('See All', 'vikrestaurants');
				break;

			case 'VRTK_GRID_FILTER_MENUS':
				$result = __('Filter Menus', 'vikrestaurants');
				break;

			case 'VRTK_GRID_FILTER_MENUS_DESC':
				$result = __('Enable this option if you want to allow the customers to filter the products by menu.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_FILTER_MENUS_ALL':
				$result = __('Show All Menus', 'vikrestaurants');
				break;

			case 'VRTK_GRID_FILTER_MENUS_ALL_DESC':
				$result = __('Enable this option to allow the customers to display all the menus. When this option is disabled, the first available menu will be auto-selected.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_PRODUCTS':
				$result = __('Products', 'vikrestaurants');
				break;

			case 'VRTK_GRID_PRODUCTS_DESC':
				$result = __('Choose the products to show. Leave empty to display all the published products.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_NUM_ITEMS':
				$result = __('Items per Row', 'vikrestaurants');
				break;

			case 'VRTK_GRID_NUM_ITEMS_DESC':
				$result = __('The maximum number of items to display on each row.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_PRICE':
				$result = __('Show Price', 'vikrestaurants');
				break;

			case 'VRTK_GRID_PRICE_DESC':
				$result = __('Enable this option if you want to display the price of the item.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_IMAGE':
				$result = __('Show Image', 'vikrestaurants');
				break;

			case 'VRTK_GRID_IMAGE_DESC':
				$result = __('Enable this option if you want to display the image of the item.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_MENU':
				$result = __('Show Menu', 'vikrestaurants');
				break;

			case 'VRTK_GRID_MENU_DESC':
				$result = __('Enable this option if you want to display the parent menu of the item.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_ATTRIBUTES':
				$result = __('Show Attributes', 'vikrestaurants');
				break;

			case 'VRTK_GRID_ATTRIBUTES_DESC':
				$result = __('Enable this option if you want to display the attributes icon of the item.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_RATING':
				$result = __('Show Rating', 'vikrestaurants');
				break;

			case 'VRTK_GRID_RATING_DESC':
				$result = __('Enable this option if you want to display the rating (stars) of the item.', 'vikrestaurants');
				break;

			case 'VRTK_GRID_ALWAYS':
				$result = __('Always', 'vikrestaurants');
				break;

			case 'VRTK_GRID_WHEN_AVAILABLE':
				$result = __('Only when available', 'vikrestaurants');
				break;

			case 'VRTK_GRID_NEVER':
				$result = __('Never', 'vikrestaurants');
				break;

			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
