<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants status codes popup HTML helper.
 *
 * @since 1.8
 */
abstract class VREHtmlStatuscodes
{
	/**
	 * A list of status codes.
	 *
	 * @var array
	 */
	protected static $codes = array();

	/**
	 * Displays the popup used to change reservation code.
	 *
	 * @param 	integer  $group     The group to which the codes belong.
	 * 							    Use '1' for restaurant, '2' for take-away
	 * 								or '3' for food.
	 * @param 	string   $selector  The popup trigger selector.
	 *
	 * @return 	string  The HTML of the popup.
	 */
	public static function popup($group, $selector = null)
	{
		$document = JFactory::getDocument();

		if (!isset(static::$codes[$group]))
		{
			// get reservation codes
			static::$codes[$group] = JHtml::fetch('vikrestaurants.rescodes', $group);

			$json = array();

			// iterate codes and construct the image URI
			foreach (static::$codes[$group] as $code)
			{
				$tmp = clone $code;

				if ($tmp->icon)
				{
					$tmp->icon = VREMEDIA_SMALL_URI . $tmp->icon;
				}

				$json[] = $tmp;
			}

			// encode codes for JS usage
			$json = json_encode($json);

			// register supported status codes
			$document->addScriptDeclaration(
<<<JS
onInstanceReady(() => {
	return typeof VIKRESTAURANTS_STATUS_CODES_MAP !== 'undefined';
}).then(() => {
	VIKRESTAURANTS_STATUS_CODES_MAP[$group] = $json;
});
JS
			);
		}

		$data = [
			'codes' => static::$codes[$group],
		];

		if (JFactory::getApplication()->isClient('administrator'))
		{
			$base = VREBASE . DIRECTORY_SEPARATOR . 'layouts';
		}
		else
		{
			$base = null;
		}

		if ($selector)
		{
			switch ($group)
			{
				case 1:
					$controller = 'reservation';
					break;

				case 2:
					$controller = 'tkreservation';
					break;

				case 3:
					$controller = 'resprod';
					break;

				default:
					$controller = '';
			}

			/**
			 * Directly use AJAX end-point instead of the controller name.
			 *
			 * @since 1.8.3
			 */
			$controller = "index.php?option=com_vikrestaurants&task={$controller}.changecodeajax&tmpl=component";
			$controller = VREFactory::getPlatform()->getUri()->ajax($controller, false);

			$document->addScriptDeclaration(
<<<JS
(function($) {
	'use strict';

	$(function() {
		$('$selector').statusCodesPopup({
			group: {$group},
			controller: '{$controller}',
		});
	});
})(jQuery);
JS
			);
		}

		static $loaded = 0;

		if (!$loaded)
		{
			$loaded = 1;

			JText::script('VRSYSTEMCONNECTIONERR');
			JText::script('VRRESCODENOSTATUS');

			// load script
			JHtml::fetch('vrehtml.assets.statuscodes');

			$document->addScriptDeclaration(
<<<JS
(function($, w) {
	'use strict';

	// catch any status code changes
	$(w).on('statuscode.changed', (event, code) => {
		if (code.id) {
			// in case of selected code, make "folder" icon of
			// parent reservation filled
			$('#vrordfoldicon' + code.id_order)
				.removeClass('far')
				.addClass('fas');
		}
	});

	$(w).on('statuscode.error', (event, error) => {
		alert(error.responseText || Joomla.JText._('VRSYSTEMCONNECTIONERR'));
	});
})(jQuery, window);
JS
			);
		}

		return '';
	}
}
