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
 * Switcher class to translate the VikRestaurants plugin system languages.
 *
 * @since 	1.0
 */
class VikRestaurantsLanguageSystem implements JLanguageHandler
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
			 * MVC errors.
			 */

			case 'ERROR':
			case 'FATAL_ERROR':
				$result = __('Error', 'vikrestaurants');
				break;

			case 'JERROR_ALERTNOAUTHOR':
				$result = __('You are not authorised to view this resource.', 'vikrestaurants');
				break;

			case 'CONTROLLER_FILE_NOT_FOUND_ERR':
				$result = __('The controller does not exist.', 'vikrestaurants');
				break;

			case 'CONTROLLER_CLASS_NOT_FOUND_ERR':
				$result = __('The controller [%s] classname does not exist.', 'vikrestaurants');
				break;

			case 'CONTROLLER_INVALID_INSTANCE_ERR':
				$result = __('The controller must be an instance of JController.', 'vikrestaurants');
				break;

			case 'CONTROLLER_PROTECTED_METHOD_ERR':
				$result = __('You cannot call JController reserved methods.', 'vikrestaurants');
				break;

			case 'TEMPLATE_VIEW_NOT_FOUND_ERR':
				$result = __('Template view not found.', 'vikrestaurants');
				break;

			case 'RESOURCE_AUTH_ERROR':
				$result = __('You are not authorised to access this resource.', 'vikrestaurants');
				break;

			case 'JINVALID_TOKEN':
				$result = __('The most recent request was denied because it had an invalid security token. Please refresh the page and try again.', 'vikrestaurants');
				break;

			case 'JINVALID_TOKEN_NOTICE':
				$result = __('The security token did not match. The request was aborted to prevent any security breach. Please try again.', 'vikrestaurants');
				break;

			case 'PLG_RECAPTCHA_ERROR_INCORRECT_CAPTCHA_SOL':
				$result = __('The CAPTCHA was incorrect.', 'vikrestaurants');
				break;

			case 'CONNECTION_LOST':
				// translation provided by wordpress
				$result = __('Connection lost or the server is busy. Please try again later.');
				break;

			/**
			 * Native ACL rules.
			 */

			case 'VREACLMENUTITLE':
				$result = __('VikRestaurants - Access Control List', 'vikrestaurants');
				break;

			case 'JACTION_ADMIN':
				$result = __('Configure ACL & Options', 'vikrestaurants');
				break;

			case 'JACTION_ADMIN_COMPONENT_DESC':
				$result = __('Allows users in the group to edit the options and permissions of this plugin.', 'vikrestaurants');
				break;

			case 'JACTION_MANAGE':
				$result = __('Access Administration Interface', 'vikrestaurants');
				break;

			case 'JACTION_MANAGE_COMPONENT_DESC':
				$result = __('Allows users in the group to access the administration interface for this plugin.', 'vikrestaurants');
				break;

			case 'JACTION_CREATE':
				$result = __('Create', 'vikrestaurants');
				break;

			case 'JACTION_CREATE_COMPONENT_DESC':
				$result = __('Allows users in the group to create any content in this plugin.', 'vikrestaurants');
				break;

			case 'JACTION_DELETE':
				$result = __('Delete', 'vikrestaurants');
				break;

			case 'JACTION_DELETE_COMPONENT_DESC':
				$result = __('Allows users in the group to delete any content in this plugin.', 'vikrestaurants');
				break;

			case 'JACTION_EDIT':
				// use WP CORE translation
				$result = __('Edit');
				break;

			case 'JACTION_EDIT_COMPONENT_DESC':
				$result = __('Allows users in the group to edit any content in this plugin.', 'vikrestaurants');
				break;

			case 'JACTION_EDITSTATE':
				$result = __('Edit State', 'vikrestaurants');
				break;

			case 'JACTION_EDITSTATE_COMPONENT_DESC':
				$result = __('Allows users in the group to change the state of any content in this extension.', 'vikrestaurants');
				break;

			/**
			 * ACL form.
			 */

			case 'ACL_SAVE_SUCCESS':
				$result = __('ACL saved.', 'vikrestaurants');
				break;

			case 'ACL_SAVE_ERROR':
				$result = __('An error occurred while saving the ACL.', 'vikrestaurants');
				break;

			case 'JALLOWED':
				$result = __('Allowed', 'vikrestaurants');
				break;

			case 'JDENIED':
				$result = __('Denied', 'vikrestaurants');
				break;

			case 'JACTION':
				// use WP CORE translation
				$result = __('Action');
				break;

			case 'JNEW_SETTING':
				$result = __('New Setting', 'vikrestaurants');
				break;

			case 'JCURRENT_SETTING':
				$result = __('Current Setting', 'vikrestaurants');
				break;

			/**
			 * Toolbar buttons.
			 */

			case 'JTOOLBAR_NEW':
				// use WP CORE translation
				$result = __('New');
				break;

			case 'JTOOLBAR_EDIT':
				// use WP CORE translation
				$result = __('Edit');
				break;

			case 'JTOOLBAR_BACK':
				// use WP CORE translation
				$result = __('Back');
				break;

			case 'JTOOLBAR_PUBLISH':
				// use WP CORE translation
				$result = __('Publish');
				break;

			case 'JTOOLBAR_UNPUBLISH':
				$result = __('Unpublish', 'vikrestaurants');
				break;

			case 'JTOOLBAR_ARCHIVE':
				$result = __('Archive', 'vikrestaurants');
				break;

			case 'JTOOLBAR_UNARCHIVE':
				$result = __('Unarchive', 'vikrestaurants');
				break;

			case 'JTOOLBAR_DELETE':
				$result = __('Delete', 'vikrestaurants');
				break;

			case 'JTOOLBAR_TRASH':
				// use WP CORE translation
				$result = __('Trash');
				break;

			case 'JSAVE':
			case 'JAPPLY':
			case 'JTOOLBAR_APPLY':
				// use WP CORE translation
				$result = __('Save');
				break;

			case 'JTOOLBAR_SAVE':
				$result = __('Save & Close', 'vikrestaurants');
				break;

			case 'JTOOLBAR_SAVE_AND_NEW':
				$result = __('Save & New', 'vikrestaurants');
				break;

			case 'JTOOLBAR_SAVE_AS_COPY':
				$result = __('Save as Copy', 'vikrestaurants');
				break;

			case 'JTOOLBAR_CANCEL':
			case 'JCANCEL':
				// use WP CORE translation
				$result = __('Cancel');
				break;

			case 'JTOOLBAR_CLOSE':
				// use WP CORE translation
				$result = __('Close');
				break;

			case 'JTOOLBAR_OPTIONS':
				$result = __('Permissions', 'vikrestaurants');
				break;

			case 'JTOOLBAR_HELP':
				// use WP CORE translation
				$result = __('Help');
				break;

			case 'JTOOLBAR_SHORTCODES':
				$result = __('Shortcodes', 'vikrestaurants');
				break;

			/**
			 * Filters.
			 */

			case 'JOPTION_SELECT_LANGUAGE':
				$result = __('- Select Language -', 'vikrestaurants');
				break;

			case 'JOPTION_SELECT_TYPE':
				$result = __('- Select Type -', 'vikrestaurants');
				break;

			case 'JOPTION_SELECT_PUBLISHED':
				$result = __('- Select Status -', 'vikrestaurants');
				break;

			case 'JOPTION_ANY':
				$result = __('Any', 'vikrestaurants');
				break;

			case 'JSEARCH_FILTER_SUBMIT':
				// use WP CORE translation
				$result = _x('Search', 'submit button');
				break;

			case 'JSEARCH_TOOLS':
				$result = __('Search Tools', 'vikrestaurants');
				break;

			case 'JSEARCH_FILTER_CLEAR':
				// use WP CORE translation
				$result = __('Clear');
				break;

			/**
			 * Access options.
			 */

			case 'JOPTION_ACCESS_SHOW_ALL_ACCESS':
				$result = __('Show All Access', 'vikrestaurants');
				break;

			case 'JOPTION_ACCESS_PUBLIC':
				// use WP CORE translation
				$result = __('Public');
				break;

			case 'JOPTION_ACCESS_GUEST':
				$result = __('Guest', 'vikrestaurants');
				break;

			case 'JOPTION_ACCESS_REGISTERED':
				$result = __('Registered', 'vikrestaurants');
				break;

			case 'JOPTION_ACCESS_SPECIAL':
				$result = __('Special', 'vikrestaurants');
				break;

			case 'JOPTION_ACCESS_SUPERUSER':
				$result = __('Super User', 'vikrestaurants');
				break;

			/**
			 * Fields.
			 */

			case 'JFIELD_ALIAS_LABEL':
				$result = __('Alias', 'vikrestaurants');
				break;

			case 'JFIELD_ALIAS_DESC':
				$result = __('The Alias will be used in the SEF URL. Leave this blank and the system will fill in a default value from the title.', 'vikrestaurants');
				break;

			case 'JFIELD_ACCESS_LABEL':
				$result = __('Access', 'vikrestaurants');
				break;

			case 'JFIELD_ACCESS_DESC':
				$result = __('The access level group that is allowed to view this item.', 'vikrestaurants');
				break;

			case 'JFIELD_META_DESCRIPTION_LABEL':
				$result = __('Meta Description', 'vikrestaurants');
				break;

			case 'JFIELD_META_DESCRIPTION_DESC':
				$result = __('An optional paragraph to be used as the description of the page in the HTML output. This will generally display in the results of search engines.', 'vikrestaurants');
				break;

			case 'JFIELD_META_KEYWORDS_LABEL':
				$result = __('Meta Keywords', 'vikrestaurants');
				break;

			case 'JFIELD_META_KEYWORDS_DESC':
				$result = __('An optional comma-separated list of keywords and/or phrases to be used in the HTML output.', 'vikrestaurants');
				break;

			case 'COM_CONTENT_FIELD_BROWSER_PAGE_TITLE_LABEL':
				$result = __('Browser Page Title', 'vikrestaurants');
				break;

			case 'COM_CONTENT_FIELD_BROWSER_PAGE_TITLE_DESC':
				$result = __('Optional text for the "Browser page title" element to be used when the post is viewed with a non-post menu item. If blank, the post\'s title is used instead.', 'vikrestaurants');
				break;

			/**
			 * Pagination.
			 */

			case 'JPAGINATION_ITEMS':
				$result = __('%d items', 'vikrestaurants');
				break;

			case 'JPAGINATION_PAGE_OF_TOT':
				// use WP CORE translation
				$result = _x('%1$s of %2$s', 'paging');
				break;

			/**
			 * Menu items - fieldset titles.
			 */

			case 'COM_MENUS_REQUEST_FIELDSET_LABEL':
				// use WP CORE translation
				$result = __('Details');
				break;

			/**
			 * Commons.
			 */
			
			case 'JYES':
				// use WP CORE translation
				$result = __('Yes');
				break;

			case 'JNO':
				// use WP CORE translation
				$result = __('No');
				break;

			case 'JADMINISTRATOR':
				// @TRANSLATORS: The back-end section of WP.
				$result = _x('Admin', 'The back-end section of WP.', 'vikrestaurants');
				break;

			case 'JSITE':
				// @TRANSLATORS: The front-end section of WP.
				$result = _x('Site', 'The front-end section of WP.', 'vikrestaurants');
				break;

			case 'JALL':
				// use WP CORE translation
				$result = __('All');
				break;

			case 'JPUBLISHED':
				// use WP CORE translation
				$result = __('Published');
				break;

			case 'JUNPUBLISHED':
				$result = __('Unpublished', 'vikrestaurants');
				break;

			case 'JTRASHED':
				$result = __('Trashed', 'vikrestaurants');
				break;

			case 'JID':
			case 'JGRID_HEADING_ID':
				// use WP CORE translation
				$result = __('ID');
				break;

			case 'JCREATEDBY':
				$result = __('Author');
				break;

			case 'JCREATEDON':
				// use WP CORE translation
				$result = __('Date');
				break;

			case 'JNAME':
				// use WP CORE translation
				$result = __('Name');
				break;

			case 'JDETAILS':
				// use WP CORE translation
				$result = __('Details');
				break;

			case 'JTYPE':
				// use WP CORE translation
				$result = __('Type');
				break;

			case 'JSHORTCODE':
				// use WP CORE translation
				$result = __('Shortcode');
				break;

			case 'JLANGUAGE':
				// use WP CORE translation
				$result = __('Language');
				break;

			case 'JPOST':
				// use WP CORE translation
				$result = __('Post');
				break;

			case 'JNEXT':
				$result = __('Next');
				break;

			case 'JPREV':
				$result = __('Previous');
				break;

			case 'PLEASE_MAKE_A_SELECTION':
			case 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST':
				$result = __('Please first make a selection from the list.', 'vikrestaurants');
				break;

			case 'JLIB_APPLICATION_ERROR_SAVE_FAILED':
				$result = __('Save failed with the following error: %s', 'vikrestaurants');
				break;

			case 'JERROR_AN_ERROR_HAS_OCCURRED':
				$result = __('An error has occurred.', 'vikrestaurants');
				break;

			case 'JERROR_SAVE_FAILED':
				$result = __('Could not save data. Error: %s', 'vikrestaurants');
				break;

			case 'JLIB_APPLICATION_SAVE_SUCCESS':
				$result = __('Item saved.', 'vikrestaurants');
				break;

			case 'JGLOBAL_SELECT_AN_OPTION':
				$result = __('Select an option', 'vikrestaurants');
				break;

			case 'JGLOBAL_USE_GLOBAL':
				$result = __('Use Global', 'vikrestaurants');
				break;

			case 'JGLOBAL_SELECTION_ALL':
				// use WP CORE translation
				$result = __('Select All');
				break;
				
			case 'JGLOBAL_SELECTION_NONE':
				$result = __('Clear Selection', 'vikrestaurants');
				break;

			case 'NO_ROWS_FOUND':
			case 'JGLOBAL_NO_MATCHING_RESULTS':
				// use WP CORE translation
				$result = __('No items found.');
				break;

			case 'JOPTION_USE_DEFAULT':
				$result = __('- Use Default -', 'vikrestaurants');
				break;

			case 'JGLOBAL_FIELDSET_BASIC':
			case 'COM_MENUS_BASIC_FIELDSET_LABEL':
			case 'JFIELD_PARAMS_LABEL':
				$result = __('Options', 'vikrestaurants');
				break;

			case 'JGLOBAL_FIELDSET_ADVANCED':
			case 'COM_MENUS_ADVANCED_FIELDSET_LABEL':
				// use WP CORE translation
				$result = __('Advanced');
				break;

			case 'COM_MENUS_CONFIG_FIELDSET_LABEL':
			case 'COM_MENUS_SETTINGS_FIELDSET_LABEL':
				// use WP CORE translation
				$result = __('Settings');
				break;

			case 'COM_MENUS_SLIDE_FIELDSET_LABEL':
				// use WP CORE translation
				$result = __('Slide', 'vikrestaurants');
				break;

			case 'JGLOBAL_FIELDSET_PUBLISHING':
				$result = __('Publishing', 'vikrestaurants');
				break;

			case 'JGLOBAL_FIELDSET_METADATA_OPTIONS':
				// use WP CORE translation
				$result = __('Metadata');
				break;

			case 'JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT':
				$result = __('Maximum upload size: <strong>%s</strong>', 'vikrestaurants');
				break;

			case 'JFIELD_ALT_LAYOUT_LABEL':
				$result = __('Layout', 'vikrestaurants');
				break;

			case 'JFIELD_ALT_MODULE_LAYOUT_DESC':
				$result = __('Use a layout from the supplied widget or overrides in the themes.', 'vikrestaurants');
				break;

			case 'COM_MODULES_OPTION_SELECT_MENU_ITEM':
				$result = __('- Select Menu Item -', 'vikrestaurants');
				break;

			case 'COM_MODULES_FIELD_MODULECLASS_SFX_LABEL':
				$result = __('Widget Class Suffix', 'vikrestaurants');
				break;

			case 'COM_MODULES_FIELD_MODULECLASS_SFX_DESC':
				$result = __('A suffix to be applied to the CSS class of the widget. This allows for individual styling.', 'vikrestaurants');
				break;

			case 'COM_MODULES_FIELD_TITLE_LABEL':
				$result = __('Block Title', 'vikrestaurants');
				break;

			case 'COM_MODULES_FIELD_TITLE_DESC':
				$result = __('An optional title to be displayed above the block contents.', 'vikrestaurants');
				break;

			case 'COM_MODULES_PREVIEW_NOT_AVAIL':
				$result = __('Preview not yet available. Try to adjust the configuration of the widget.', 'vikrestaurants');
				break;

			case 'COM_USERS_REGISTRATION_SAVE_SUCCESS':
				$result = __('New user created.');
				break;

			/**
			 * Users.
			 */

			case 'COM_USERS_LOGIN_REMEMBER_ME':
				// use WP CORE translation
				$result = __('Remember Me');
				break;

			case 'COM_USERS_LOGIN_RESET':
				// use WP CORE translation
				$result = __('Lost your password?');
				break;

			/**
			 * Media manager.
			 */

			case 'JMEDIA_PREVIEW_TITLE':
				// use WP CORE translation
				$result = __('Preview');
				break;

			case 'JMEDIA_CHOOSE_IMAGE':
				// use WP CORE translation
				$result = __('Choose image');
				break;

			case 'JMEDIA_CHOOSE_IMAGES':
				$result = __('Choose one or more images', 'vikrestaurants');
				break;

			case 'JMEDIA_SELECT':
				// use WP CORE translation
				$result = __('Select');
				break;

			case 'JMEDIA_UPLOAD_BUTTON':
				$result = __('Pick or upload an image', 'vikrestaurants');
				break;

			case 'JMEDIA_CLEAR_BUTTON':
				$result = __('Clear selection', 'vikrestaurants');
				break;

			/**
			 * Dates.
			 */

			case 'DATE_FORMAT_LC':
				$result = get_option('date_format', 'l, d F Y');
				break;

			case 'DATE_FORMAT_LC1':
				// $result = __('l, d F Y', 'vikrestaurants');
				$result = get_option('date_format', 'l, d F Y');
				break;

			case 'DATE_FORMAT_LC2':
				// $result = __('l, d F Y H:i', 'vikrestaurants');
				$result = get_option('date_format', 'l, d F Y') . ' ' . get_option('time_format', 'H:i');
				break;

			case 'DATE_FORMAT_LC3':
				// $result = __('d F Y', 'vikrestaurants');
				$result = get_option('date_format', 'l, d F Y');
				break;
				
			case 'JANUARY':
				$result = __('January');
				break;

			case 'FEBRUARY':
				$result = __('February');
				break;

			case 'MARCH':
				$result = __('March');
				break;

			case 'APRIL':
				$result = __('April');
				break;

			case 'MAY':
				$result = __('May');
				break;

			case 'JUNE':
				$result = __('June');
				break;

			case 'JULY':
				$result = __('July');
				break;

			case 'AUGUST':
				$result = __('August');
				break;

			case 'SEPTEMBER':
				$result = __('September');
				break;

			case 'OCTOBER':
				$result = __('October');
				break;

			case 'NOVEMBER':
				$result = __('November');
				break;

			case 'DECEMBER':
				$result = __('December');
				break;

			case 'JANUARY_SHORT':
				$result = _x('Jan', 'January abbreviation');
				break;

			case 'FEBRUARY_SHORT':
				$result = _x('Feb', 'February abbreviation');
				break;

			case 'MARCH_SHORT':
				$result = _x('Mar', 'March abbreviation');
				break;

			case 'APRIL_SHORT':
				$result = _x('Apr', 'April abbreviation');
				break;

			case 'MAY_SHORT':
				$result = _x('May', 'May abbreviation');
				break;

			case 'JUNE_SHORT':
				$result = _x('Jun', 'June abbreviation');
				break;

			case 'JULY_SHORT':
				$result = _x('Jul', 'July abbreviation');
				break;

			case 'AUGUST_SHORT':
				$result = _x('Aug', 'August abbreviation');
				break;

			case 'SEPTEMBER_SHORT':
				$result = _x('Sep', 'September abbreviation');
				break;

			case 'OCTOBER_SHORT':
				$result = _x('Oct', 'October abbreviation');
				break;

			case 'NOVEMBER_SHORT':
				$result = _x('Nov', 'November abbreviation');
				break;

			case 'DECEMBER_SHORT':
				$result = _x('Dec', 'December abbreviation');
				break;

			case 'MONDAY':
				$result = __('Monday');
				break;

			case 'TUESDAY':
				$result = __('Tuesday');
				break;

			case 'WEDNESDAY':
				$result = __('Wednesday');
				break;

			case 'THURSDAY':
				$result = __('Thursday');
				break;

			case 'FRIDAY':
				$result = __('Friday');
				break;

			case 'SATURDAY':
				$result = __('Saturday');
				break;

			case 'SUNDAY':
				$result = __('Sunday');
				break;

			case 'MON':
				$result = __('Mon');
				break;

			case 'TUE':
				$result = __('Tue');
				break;

			case 'WED':
				$result = __('Wed');
				break;

			case 'THU':
				$result = __('Thu');
				break;

			case 'FRI':
				$result = __('Fri');
				break;

			case 'SAT':
				$result = __('Sat');
				break;

			case 'SUN':
				$result = __('Sun');
				break;

			/**
			 * Relative dates.
			 */
			
			case 'JLIB_HTML_DATE_RELATIVE_LESSTHANAMINUTE':
				$result = __('Less than a minute ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_MINUTES':
				$result = __('%d minutes ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_MINUTES_1':
				$result = __('a minute ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_HOURS':
				$result = __('%d hours ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_HOURS_1':
				$result = __('an hour ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_DAYS':
				$result = __('%d days ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_DAYS_1':
				$result = __('a day ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_WEEKS':
				$result = __('%d weeks ago.', 'vikrestaurants');
				break;

			case 'JLIB_HTML_DATE_RELATIVE_WEEKS_1':
				$result = __('a week ago.', 'vikrestaurants');
				break;

			/**
			 * Natives.
			 */

			case 'VRESHORTCDSMENUTITLE':
				$result = __('VikRestaurants - Shortcodes', 'vikrestaurants');
				break;

			case 'VRENEWSHORTCDMENUTITLE':
				$result = __('VikRestaurants - New Shortcode', 'vikrestaurants');
				break;

			case 'VREEDITSHORTCDMENUTITLE':
				$result = __('VikRestaurants - Edit Shortcode', 'vikrestaurants');
				break;

			case 'VRE_SHORTCODE_VIEW_FRONT':
				// use WP CORE translation
				$result = __('View page');
				break;

			case 'VRE_SHORTCODE_VIEW_TRASHED':
				$result = __('View trashed post', 'vikrestaurants');
				break;

			case 'VRE_SHORTCODE_CREATE_PAGE':
				$result = __('Create page', 'vikrestaurants');
				break;

			case 'VRE_SHORTCODE_CREATE_PAGE_CONFIRM':
				$result = __('You can always manually create a custom page/post and use this shortcode text inside it. By proceeding, a page containing this shortcode will be automatically created. Do you want to go ahead?', 'vikrestaurants');
				break;

			case 'VRE_SHORTCODE_CREATE_PAGE_SUCCESS':
				$result = __('The shortcode was successfully added to a new page of your website. Visit the new page in the front-end to see its content (if any).', 'vikrestaurants');
				break;

			case 'VRE_SHORTCODE_PARENT_FIELD':
				$result = __('Parent Shortcode', 'vikrestaurants');
				break;

			/**
			 * License.
			 */

			case 'VREMAINGOTOPROTITLE':
				$result = __('VikRestaurants - Upgrade to Pro', 'vikrestaurants');
				break;

			case 'VREGOTOPROBTN':
				$result = __('Upgrade to PRO', 'vikrestaurants');
				break;

			case 'VREISPROBTN':
				$result = __('PRO Version', 'vikrestaurants');
				break;

			case 'VREINVALIDPROKEY':
				$result = __('Please, enter a valid license key.', 'vikrestaurants');
				break;

			case 'VREINVALIDRESPONSE':
				$result = __('Invalid response: %s', 'vikrestaurants');
				break;

			case 'VRENOPROERROR':
				$result = __('No valid and active license key found.', 'vikrestaurants');
				break;

			case 'VREMAINGETPROTITLE':
				$result = __('VikRestaurants - Downloading Pro version', 'vikrestaurants');
				break;

			case 'VREPROPLWAIT':
				$result = __('Please wait', 'vikrestaurants');
				break;

			case 'VREPRODLINGPKG':
				$result = __('Downloading the package...', 'vikrestaurants');
				break;

			case 'VREUPDCOMPLOKCLICK':
				$result = __('Update completed, click here to continue', 'vikrestaurants');
				break;

			case 'VREUPDCOMPLNOKCLICK':
				$result = __('Update failed, click here to continue', 'vikrestaurants');
				break;
		}

		return $result;
	}
}
