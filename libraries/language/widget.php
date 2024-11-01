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
 * Switcher class to translate the VikRestaurants widgets languages.
 *
 * @since 	1.0
 */
class VikRestaurantsLanguageWidget implements JLanguageHandler
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
			 * Commons.
			 */

			case 'TITLE':
				$result = __('Title');
				break;

			case 'COM_MODULES_SETTINGS_FIELDSET_LABEL':
				$result = __('Settings');
				break;

			case 'VR_EVENT_MENU_ITEM':
			case 'VR_QUICK_MENU_ITEM':
			case 'VR_SEARCH_MENUITEM':
			case 'VRTK_CART_MENUITEM':
			case 'VRTK_GRID_MENUITEM':
			case 'VRTKXMLMENUITEM':
				$result = __('Menu Item', 'vikrestaurants');
				break;

			case 'VR_EVENT_MENU_ITEM_DESC':
			case 'VR_QUICK_MENU_ITEM_DESC':
			case 'VR_SEARCH_MENUITEM_DESC':
			case 'VRTK_CART_MENUITEM_DESC':
			case 'VRTK_GRID_MENUITEM_DESC':
				$result = __('Select the menu item that will be used after submitting the form.', 'vikrestaurants');
				break;
		}

		return $result;
	}
}
