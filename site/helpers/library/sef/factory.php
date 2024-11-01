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
 * Factory class used to load the correct router handler
 * according to the platform version.
 *
 * @since 1.8.3
 */
final class VRESefFactory
{
	/**
	 * Loads the router instance.
	 * It is needed to return or instantiate the class,
	 * since it is enough let Joomla accesses it.
	 *
	 * @return  void
	 */
	public static function loadRouter()
	{
		if (VersionListener::isJoomla4x())
		{
			// load router compatible with Joomla 4 or higher
			VRELoader::import('library.sef.router.j40');
		}
		else
		{
			// load default router
			VRELoader::import('library.sef.router.default');
		}
	}
}
