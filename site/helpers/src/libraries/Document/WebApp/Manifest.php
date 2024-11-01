<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Document\WebApp;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * This interface declares the common methods for the generation of a web application manifest.
 * 
 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest 
 * 
 * @since 1.9
 */
interface Manifest
{
	/**
	 * Returns the absolute path where the JSON manifest should be located.
	 * 
	 * @return  string
	 */
	public function getPath();

	/**
	 * Returns the object that should be used to generate the JSON manifest.
	 * 
	 * @return  \JsonSerializable
	 */
	public function buildJson();
}
