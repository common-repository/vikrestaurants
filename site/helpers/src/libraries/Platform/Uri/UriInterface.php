<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\Uri;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Declares all the URI helper methods that may differ between every supported platform.
 * 
 * @since 1.9
 */
interface UriInterface
{
	/**
	 * Rewrites an internal URI that needs to be used outside of the website.
	 * This means that the routed URI MUST start with the base path of the site.
	 *
	 * @param 	mixed   $query   The query string or an associative array of data.
	 * @param 	bool    $xhtml   Replace & by &amp; for XML compliance.
	 * @param 	mixed   $itemid  The itemid to use. If null, the current one will be used.
	 *
	 * @return 	string  The complete routed URI.
	 */
	public function route($query = '', bool $xhtml = true, $itemid = null);

	/**
	 * Routes an admin URL for being used outside from the website (complete URI).
	 *
	 * @param 	mixed   $query  The query string or an associative array of data.
	 * @param 	bool    $xhtml  Replace & by &amp; for XML compliance.
	 *
	 * @return 	string  The complete routed URI. 
	 */
	public function admin($query = '', bool $xhtml = true);

	/**
	 * Prepares a plain/routed URL to be used for an AJAX request.
	 *
	 * @param 	mixed   $query  The query string or a routed URL.
	 * @param 	bool    $xhtml  Replace & by &amp; for XML compliance.
	 *
	 * @return 	string  The AJAX end-point URI.
	 */
	public function ajax($query = '', bool $xhtml = false);

	/**
	 * Includes the CSRF-proof token within the specified query string/URL.
	 *
	 * @param 	mixed 	$query  The query string or a routed URL.
	 * @param 	bool    $xhtml  Replace & by &amp; for XML compliance.
	 *
	 * @return 	string 	The resulting path.
	 */
	public function addCSRF($query = '', bool $xhtml = false);

	/**
	 * Converts the given absolute path into a reachable URL.
	 *
	 * @param 	string  $path      The absolute path.
	 * @param 	bool    $relative  True to receive a relative path.
	 *
	 * @return 	mixed   The resulting URL on success, null otherwise.
	 */
	public function getUrlFromPath($path, bool $relative = false);

	/**
	 * Returns the absolute path used by the current platform.
	 * 
	 * @return  string  The absolute path.
	 */
	public function getAbsolutePath();
}
