<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_event
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Event widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_EventLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Event module.
			 */

			case 'VIKRESTAURANTS_EVENT_MODULE_TITLE':
				$result = __('VikRestaurants Event', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_EVENT_MODULE_DESCRIPTION':
				$result = __('This widget displays a quick booking form of a special day as event.', 'vikrestaurants');
				break;

			case 'VREVENTBOOKNOW':
				$result = __('Book Now', 'vikrestaurants');
				break;

			case 'COM_MODULES_CONFIG_FIELDSET_LABEL':
				$result = __('Configuration', 'vikrestaurants');
				break;

			case 'VR_EVENT_SPECIALDAY':
				$result = __('Special Day', 'vikrestaurants');
				break;

			case 'VR_EVENT_SPECIALDAY_DESC':
				$result = __('Choose the special day to show. Only the special days that are marked on the calendar can be picked through this option.', 'vikrestaurants');
				break;

			case 'VR_EVENT_CALENDAR':
				$result = __('Enable Calendar', 'vikrestaurants');
				break;

			case 'VR_EVENT_CALENDAR_DESC':
				$result = __('Enable this setting if you want to allow your customers to select the date. Otherwise the first available date of the special day will be taken.', 'vikrestaurants');
				break;

			case 'VR_EVENT_TIME':
				$result = __('Event Time', 'vikrestaurants');
				break;

			case 'VR_EVENT_TIME_DESC':
				$result = __('Choose the pre-selected time into the 24H format (e.g. 20:30). Leave this field blank if you want to allow the users to select a time.', 'vikrestaurants');
				break;
				
			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
