<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_quickres
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Quick Reservation widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_QuickresLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Quick reservation module.
			 */

			case 'VIKRESTAURANTS_QUICKRES_MODULE_TITLE':
				$result = __('VikRestaurants Quick Reservation', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_QUICKRES_MODULE_DESCRIPTION':
				$result = __('This widget can be used to quickly schedule reservations through a minified form.', 'vikrestaurants');
				break;

			case 'VRFINDTABLE':
				$result = __('Find a Table', 'vikrestaurants');
				break;

			case 'VRROOMSELECTION':
				$result = __('Select a Room', 'vikrestaurants');
				break;

			case 'VRFILLCUSTFIELDS':
				$result = __('Fill in your Details', 'vikrestaurants');
				break;

			case 'VRORDERSUMMARY':
				$result = __('Order Summary', 'vikrestaurants');
				break;

			case 'VRCONTINUE':
				$result = __('Continue', 'vikrestaurants');
				break;

			case 'VRCONFIRMRESERVATION':
				$result = __('Confirm Reservation', 'vikrestaurants');
				break;

			case 'VRVISITORDERPAGE':
				$result = __('Visit Order Page', 'vikrestaurants');
				break;

			case 'VRNOTABLESEEHINTS':
				$result = __('None of the tables is available at the selected date and time. Please check the available times below:', 'vikrestaurants');
				break;

			case 'VRCONNECTIONLOST':
				$result = __('An error occurred! Please try again.', 'vikrestaurants');
				break;

			case 'VR_QUICK_SESSION':
				$result = __('Session Lifetime (min.)', 'vikrestaurants');
				break;

			case 'VR_QUICK_SESSION_DESC':
				$result = __('Customers can store a reservation every X minutes. It is suggested to set this value higher than 0 to avoid SPAM attempts.', 'vikrestaurants');
				break;

			case 'VR_QUICK_ROOMS':
				$result = __('Rooms Choosable', 'vikrestaurants');
				break;

			case 'VR_QUICK_ROOMS_DESC':
				$result = __('Allow users to choose rooms (only when possible).', 'vikrestaurants');
				break;

			case 'VR_QUICK_HEADTITLE':
				$result = __('Head Title', 'vikrestaurants');
				break;

			case 'VR_QUICK_HEADTITLE_DESC':
				$result = __('The title in the header of the widget.', 'vikrestaurants');
				break;

			case 'VR_QUICK_HEADSUBTITLE':
				$result = __('Head Sub-Title', 'vikrestaurants');
				break;

			case 'VR_QUICK_HEADSUBTITLE_DESC':
				$result = __('The sub-title in the header of the widget.', 'vikrestaurants');
				break;

			case 'VR_QUICK_IMAGEURL':
				$result = __('Image URL', 'vikrestaurants');
				break;

			case 'VR_QUICK_IMAGEURL_DESC':
				$result = __('The image logo URL to show in the header of the widget.', 'vikrestaurants');
				break;

			case 'VR_QUICK_AUTOREDIRECT':
				$result = __('Auto Redirect', 'vikrestaurants');
				break;

			case 'VR_QUICK_AUTOREDIRECT_DESC':
				$result = __('Auto redirect customers to the order page when the reservation is completed.', 'vikrestaurants');
				break;

			case 'VR_QUICK_SUMMARYSPACER':
				$result = __('Ignore the fields below if you are using auto redirect.', 'vikrestaurants');
				break;

			case 'VR_QUICK_ORDERSUMMARY':
				$result = __('Order Summary', 'vikrestaurants');
				break;

			case 'VR_QUICK_ORDERSUMMARY_DESC':
				$result = __('A short text to display at the end of the booking process.', 'vikrestaurants');
				break;

			case 'VR_QUICK_RECAPTCHA':
				$result = __('Use ReCAPTCHA', 'vikrestaurants');
				break;

			case 'VR_QUICK_RECAPTCHA_DESC':
				$result = __('When enabled, the form will require a ReCAPTCHA to be validated before submitting the reservation request. The ReCAPTCHA must be configured by an apposite WordPress plugin.', 'vikrestaurants');
				break;

			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
