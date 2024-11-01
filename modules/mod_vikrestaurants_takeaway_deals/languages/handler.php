<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_deals
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Take-Away Deals widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_Takeaway_DealsLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Take-away deals module.
			 */

			case 'VIKRESTAURANTS_TAKEAWAY_DEALS_MODULE_TITLE':
				$result = __('VikRestaurants Take-Away Deals', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_TAKEAWAY_DEALS_MODULE_DESCRIPTION':
				$result = __('This widget displays a list of active deals for your products.', 'vikrestaurants');
				break;

			case 'VRTKDEALTOGGLE':
				$result = __('Hide', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_DATEFILTER':
				$result = __('Date Filtering', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_DATEFILTER_DESC':
				$result = __('Shows only the deals published for the selected date.', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_DOTTEDNAV':
				$result = __('Enable Dotted Navigation', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_DOTTEDNAV_DESC':
				$result = __('Enable the dotted navigation to scroll the deals.', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_ARROWNAV':
				$result = __('Enable Arrow Navigation', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_ARROWNAV_DESC':
				$result = __('Enable arrow navigation to scroll the deals.', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_DURATION':
				$result = __('Slide Duration Time', 'vikrestaurants');
				break;

			case 'VRTK_DEALS_DURATION_DESC':
				$result = __('The duration in milliseconds of each frame (deal). If you don\'t want to auto slide the deals, just specify an unreachable value.', 'vikrestaurants');
				break;
				
			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
