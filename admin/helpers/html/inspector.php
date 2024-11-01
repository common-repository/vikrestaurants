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
 * VikRestaurants inspector HTML helper.
 *
 * @since 1.8
 */
abstract class VREHtmlInspector
{
	/**
	 * Renders a HTML inspector.
	 *
	 * @param 	string  $id          The inspector ID.
	 * @param 	array   $attributes  An array of attributes for the inspector.
	 * 								 - title 		An optional inspector title;
	 * 								 - closeButton  True to display the close button;
	 * 								 - keyboard		True to dismiss the inspector by pressing ESC;
	 * 								 - width		The width of the sidebar (in pixel or percentage).
	 * 								 - placement 	Where the inspector should be placed (left or right);
	 * 								 - class 		Either a string or a list of additional classes.
	 * 								 - url 			An optional URL to render the body within a <iframe>;
	 * 								 - footer 		An optional footer to be placed at bottom.
	 *                               - backdrop     Whether the backdrop is visible or not.
	 * @param 	string  $body 		 An optional HTML string to be placed in the body.
	 *
	 * @return  string  The inspector HTML.
	 *
	 * @uses 	script()
	 */
	public static function render($id, array $attributes = [], $body = null)
	{
		// raise error in case ID is empty
		if (!$id)
		{
			throw new RuntimeException('Missing Inspector ID', 404);
		}

		$vik = VREApplication::getInstance();

		// load inspector scripts
		$vik->addScript(VREASSETS_ADMIN_URI . 'js/inspector.js');
		$vik->addStyleSheet(VREASSETS_ADMIN_URI . 'css/inspector.css');
		JText::script('VRSYSTEMCONNECTIONERR');

		$data = [];

		// fetch layout data
		$data['id']          = $id;
		$data['title']       = !empty($attributes['title'])       ? $attributes['title'] : false;
		$data['closeButton'] = !empty($attributes['closeButton']) ? true : false;
		$data['keyboard']    = !empty($attributes['keyboard'])    ? true : false;
		$data['url']         = !empty($attributes['url'])         ? $attributes['url'] : false;
		$data['width']       = !empty($attributes['width'])       ? $attributes['width'] : '';
		$data['placement']   = !empty($attributes['placement'])   ? $attributes['placement'] : 'right';
		$data['class']       = !empty($attributes['class'])       ? (array) $attributes['class'] : [];
		$data['footer']      = !empty($attributes['footer'])      ? $attributes['footer'] : false;
		$data['backdrop']    = isset($attributes['backdrop'])     ? (bool) $attributes['backdrop'] : true;
		$data['body']        = (string) $body;

		// santitize width
		if (preg_match("/^[\d.]+$/", (string) $data['width']))
		{
			// append "px" because we received an amount
			$data['width'] .= 'px';
		}

		// append custom class in case of blank template
		if (JFactory::getApplication()->input->get('tmpl') === 'component')
		{
			$data['class'][] = 'blank-template';
		}

		// hide backdrop when explictly requested
		if ($data['backdrop'] === false)
		{
			$data['class'][] = 'hide-backdrop';
		}

		// join the classes
		$data['class'] = implode(' ', $data['class']);

		// create layout
		$layout = new JLayoutFile('inspector.sidebar');

		// return HTML of the field
		return $layout->render($data);
	}
}
