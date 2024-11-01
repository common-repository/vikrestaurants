<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_search
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Search widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_SearchLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Search module.
			 */

			case 'VIKRESTAURANTS_SEARCH_MODULE_TITLE':
				$result = __('VikRestaurants Search', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_SEARCH_MODULE_DESCRIPTION':
				$result = __('This widget displays the form to schedule new table reservations.', 'vikrestaurants');
				break;
				
			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
