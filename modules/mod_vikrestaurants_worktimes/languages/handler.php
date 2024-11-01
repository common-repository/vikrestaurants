<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_worktimes
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VikRestaurantsLoader::import('language.widget');

/**
 * Switcher class to translate the VikRestaurants Worktimes widget languages.
 *
 * @since 	1.0
 */
class Mod_VikRestaurants_WorktimesLanguageHandler extends VikRestaurantsLanguageWidget
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

			case 'VIKRESTAURANTS_WORKTIMES_MODULE_TITLE':
				$result = __('VikRestaurants Worktimes', 'vikrestaurants');
				break;

			case 'VIKRESTAURANTS_WORKTIMES_MODULE_DESCRIPTION':
				$result = __('This widget displays the active weekly working times.', 'vikrestaurants');
				break;

			case 'VRWTOPEN':
				$result = __('Open', 'vikrestaurants');
				break;

			case 'VRWTCLOSED':
				$result = __('Closed', 'vikrestaurants');
				break;

			case 'VR_WORK_GROUP':
				$result = __('Group', 'vikrestaurants');
				break;

			case 'VR_WORK_GROUP_RESTAURANT':
				$result = __('Restaurant', 'vikrestaurants');
				break;

			case 'VR_WORK_GROUP_TAKEAWAY':
				$result = __('Take-Away', 'vikrestaurants');
				break;

			case 'VR_WORK_VIEWMODE':
				$result = __('View Mode', 'vikrestaurants');
				break;

			case 'VR_WORK_VIEWMODE_DESC':
				$result = __('The available view modes.', 'vikrestaurants');
				break;

			case 'VR_WORK_VIEWMODE_OPT1':
				$result = __('Current Day', 'vikrestaurants');
				break;

			case 'VR_WORK_VIEWMODE_OPT2':
				$result = __('Weekly Days', 'vikrestaurants');
				break;

			case 'VR_WORK_VIEWMODE_OPT3':
				$result = __('- Both -', 'vikrestaurants');
				break;

			case 'VR_WORK_FIRSTVIEW':
				$result = __('First View', 'vikrestaurants');
				break;

			case 'VR_WORK_FIRSTVIEW_DESC':
				$result = __('The default view displayed once the page is loaded.', 'vikrestaurants');
				break;

			default:
				// fallback to parent handler for commons
				$result = parent::translate($string);
		}

		return $result;
	}
}
