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
 * Adapter used to implement URI commons methods between the platform interfaces.
 * 
 * @since 1.9
 */
abstract class UriAware implements UriInterface
{
	/**
	 * @inheritDoc
	 */
	public function getUrlFromPath($path, bool $relative = false)
	{
		// get platform base path
		$base = $this->getAbsolutePath();

		if (strpos($path, $base) !== 0)
		{
			// The path doesn't start with the base path of the site...
			// Probably the path cannot be reached via URL.
			return null;
		}

		// remove initial path
		$path = str_replace($base, '', $path);
		// remove initial directory separator
		$path = preg_replace("/^[\/\\\\]/", '', $path);

		if (DIRECTORY_SEPARATOR === '\\')
		{
			// replace Windows DS
			$path = preg_replace("[\\\\]", '/', $path);
		}

		if ($relative)
		{
			return $path;
		}

		// rebuild URL
		return \JUri::root() . $path;
	}
}
