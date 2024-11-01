<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  system
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Class used to provide support for the <head> of the page.
 *
 * @since 1.0
 */
class VikRestaurantsAssets
{
	/**
	 * A list containing all the methods already used.
	 *
	 * @var array
	 */
	protected static $loaded = array();

	/**
	 * Loads all the assets required for the plugin.
	 *
	 * @return 	void
	 */
	public static function load()
	{
		// loads only once
		if (static::isLoaded(__METHOD__))
		{
			return;
		}

		$document = JFactory::getDocument();

		$internalFilesOptions = array('version' => VIKRESTAURANTS_SOFTWARE_VERSION);

		// system.js must be loaded on both front-end and back-end for tmpl=component support
		$document->addScript(VIKRESTAURANTS_CORE_MEDIA_URI . 'js/system.js', $internalFilesOptions, array('id' => 'vre-sys-script'));

		if (JFactory::getApplication()->isAdmin())
		{
			// add CSS and JS for all pages
			RestaurantsHelper::load_css_js();
			// always load font awesome
			JHtml::fetch('vrehtml.assets.fontawesome');

			$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/system.css', $internalFilesOptions, array('id' => 'vre-sys-style'));
			$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/bootstrap.lite.css', $internalFilesOptions, array('id' => 'bootstrap-lite-style'));

			// look for CSS variations depending on the current WP version
			
			$wp = new JVersion();

			if (version_compare($wp->getShortVersion(), '5.3', '>='))
			{
				$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/bc/wp5.3.css', $internalFilesOptions, array('id' => 'vre-wp-bc53-style'));
			}
			
			if (version_compare($wp->getShortVersion(), '5.5', '>='))
			{
				$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/bc/wp5.5.css', $internalFilesOptions, array('id' => 'vre-wp-bc55-style'));
			}

			$document->addScript(VIKRESTAURANTS_CORE_MEDIA_URI . 'js/admin.js', $internalFilesOptions, array('id' => 'vre-admin-script'));
			$document->addScript(VIKRESTAURANTS_CORE_MEDIA_URI . 'js/bootstrap.min.js', $internalFilesOptions, array('id' => 'bootstrap-script'));
		}
		else
		{	
			// add CSS and JS for all pages
			VikRestaurants::load_css_js();

			$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/site.css', $internalFilesOptions, array('id' => 'vre-site-style'));
		}

		if (wp_doing_ajax()) 
		{
			// always re-init JS currency because it might
			// have been replaced after adding the scripts via JS
			JHtml::fetch('vrehtml.assets.currency');
		}

		// include localised strings for script files
		JText::script('CONNECTION_LOST');

		// Joomla instance is now available on both admin and site sections
		$document->addScriptDeclaration(
<<<JS
if (typeof Joomla === 'undefined') {
	var Joomla = new JoomlaCore();
} else {
	// reload options
	JoomlaCore.loadOptions();
}

if (typeof VikFormValidator !== 'undefined') {
	// change default method to obtain the form label
	VikFormValidator.prototype.getLabel = JFormValidator.prototype.getLabel;
}

// iterate all window vars and search for an instance of VikFormValidator
for (var k in window) {
	if (window.hasOwnProperty(k)
		&& window[k]
		&& window[k].constructor
		&& window[k].constructor.name == 'VikFormValidator')
	{
		// instance found, overwrite getLabel method to access
		// the <label> tag properly
		window[k].getLabel = JFormValidator.prototype.getLabel;
	}
}
JS
		);
	}

	/**
	 * Fixes the usage of some scripts when loaded via AJAX.
	 *
	 * @return 	void
	 */
	public static function fixAjax()
	{
		// loads only once
		if (static::isLoaded(__METHOD__))
		{
			return;
		}

		add_filter('vik_before_include_script', function($return, $url, $id, $version, $footer)
		{
			if (!wp_doing_ajax())
			{
				// leave as it is if not AJAX
				return $return;
			}

			// lookup intltel
			$intltel = array(
				VREASSETS_URI . 'js/tel/jquery.intlTelInput.min.js',
				VREASSETS_URI . 'js/tel/utils.js',
			);

			$condition = null;

			// look for intltel plugin
			if (in_array($url, $intltel))
			{
				$condition = 'jQuery.fn.intlTelInput === undefined';
				$return    = false;
			}
			// look for Google Maps
			else if (strpos($url, 'https://maps.google.com/maps/api/js') === 0)
			{
				$condition = 'typeof google === \'undefined\'';
				$return    = false;
			}

			if ($condition)
			{
				// register script to load the source file only
				// in case the condition is satisfied
				JFactory::getDocument()->addScriptDeclaration(
<<<JS
if ({$condition}) {
	jQuery('body').append('<script type="text/javascript" src="{$url}"><\/script>');
}
JS
				);
			}

			return $return;
		}, 10, 5);
	}

	/**
	 * Checks if the method has been already loaded.
	 * This function assumes that after this check we are going
	 * to use the specified method.
	 *
	 * A method is considered loaded only if the arguments used are the same.
	 *
	 * @param 	string 	 $method 	The method to check for.
	 * @param 	array 	 $args 		The list of arguments.
	 * 
	 * @return 	boolean  True if already used, otherwise false.
	 */
	protected static function isLoaded($method, array $args = array())
	{
		// generate a unique signature containing the method name
		// and the list of arguments to use
		$sign = serialize(array($method, $args));

		// check if the method has been already loaded
		if (isset(static::$loaded[$sign]))
		{
			// already loaded
			return true;
		}

		// mark the method as loaded
		static::$loaded[$sign] = 1;

		// not loaded
		return false;
	}
}
