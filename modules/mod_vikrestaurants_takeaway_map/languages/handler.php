<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_map
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Take-Away Map widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_Takeaway_MapLanguageHandler extends VikRestaurantsLanguageWidget
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
			 * Take-away map module.
			 */

			case 'VIKRESTAURANTS_TAKEAWAY_MAP_MODULE_TITLE':
				$result = __('VikRestaurants Take-Away Map', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_TAKEAWAY_MAP_MODULE_DESCRIPTION':
				$result = __('This widget displays a map containing the supported delivery areas.', 'vikrestaurants');
				break;

			case 'VRTKMAPDELIVERYHEAD':
				$result = __('Verify the availability of your address', 'vikrestaurants');
				break;

			case 'VRTKMAPADDRPLACEHOLDER':
				$result = __('type here your address', 'vikrestaurants');
				break;

			case 'VRTKMAPAREANAME':
				$result = __('Area:', 'vikrestaurants');
				break;

			case 'VRTKMAPAREACHARGE':
				$result = __('Charge:', 'vikrestaurants');
				break;

			case 'VRTKMAPAREAMINCOST':
				$result = __('Min. Cost:', 'vikrestaurants');
				break;

			case 'VRTKMAPGEOERRDENIED':
				$result = __('User denied the request for Geolocation.', 'vikrestaurants');
				break;

			case 'VRTKMAPGEOERRNOTAV':
				$result = __('Location information is unavailable.', 'vikrestaurants');
				break;

			case 'VRTKMAPGEOERRTIMEOUT':
				$result = __('The request to get user location timed out.', 'vikrestaurants');
				break;

			case 'VRTKMAPGEOERRUNKNOWN':
				$result = __('An unknown error occurred.', 'vikrestaurants');
				break;

			case 'VRTKMAPGEOERRNOTSUPP':
				$result = __('Your browser does not support Geolocation.', 'vikrestaurants');
				break;

			case 'VRTKMAPCONNECTERR':
				$result = __('Connection lost! Please, try again.', 'vikrestaurants');
				break;

			case 'VRTKMAPADDRNOTFOUND':
				$result = __('Invalid address! Please try again.', 'vikrestaurants');
				break;

			case 'VRTKXMLMENUITEMDESC':
				$result = __('Select the menu item that will be used to validate the address.', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPSETTINGS':
				$result = __('Map Settings', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPWIDTH':
				$result = __('Map Width', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPHEIGHT':
				$result = __('Map Height', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPSIZEDESC':
				$result = __('In pixel (px) or percentage (%).', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPSTYLE':
				$result = __('Map Style', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPZOOM':
				$result = __('Map Zoom Level', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPZOOMDESC':
				$result = __('The zoom level must be an integer between 0 (min zoom) and 18 (max zoom). If you leave this field empty, the zoom will be calculated automatically.', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPCENTERLAT':
				$result = __('Map Center Latitude', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPCENTERLATDESC':
				$result = __('The center latitude must be in radians. If you leave this field empty, the map will be centered automatically.', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPCENTERLNG':
				$result = __('Map Center Longitude', 'vikrestaurants');
				break;

			case 'VRTKXMLMAPCENTERLNGDESC':
				$result = __('The center longitude must be in radians. If you leave this field empty, the map will be centered automatically.', 'vikrestaurants');
				break;

			case 'VRTKXMLLOCATIONS':
				$result = __('Locations', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERY':
				$result = __('Delivery', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYSHOWAREAS':
				$result = __('Show Delivery Areas', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYSHOWAREASDESC':
				$result = __('Enable this field to show the delivery areas inside the map.', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYENABLESEARCH':
				$result = __('Enable Delivery Search', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYENABLESEARCHDESC':
				$result = __('Enable this field to allow the customers to check the availability of their address for the delivery.', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYSEARCHPOS':
				$result = __('Delivery Search Position', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYSEARCHPOSDESC':
				$result = __('Choose where you would like to show the delivery search box. You can ignore this field if the delivery search field is disabled.', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYTEXT':
				$result = __('Delivery Text', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYTEXTDESC':
				$result = __('Insert here a text for the delivery, which will be displayed below the map.', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYPOSABOVE':
				$result = __('Above the Map', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYPOSLEFT':
				$result = __('Left', 'vikrestaurants');
				break;

			case 'VRTKXMLDELIVERYPOSRIGHT':
				$result = __('Right', 'vikrestaurants');
				break;

			case 'VRTKXMLLOCATIONSDESC':
				$result = __('Since the 1.2.4 version of VikRestaurants it is possible to manage the locations from the configuration of the component.', 'vikrestaurants');
				break;
			
			case 'VRTKXMLLOCATIONSMANAGE':
				$result = __('Manage Locations', 'vikrestaurants');
				break;
			
			case 'VRTKXMLLOCATIONSAVAIL':
				$result = __('Here\'s a list containing all the existing locations:', 'vikrestaurants');
				break;
				
			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
