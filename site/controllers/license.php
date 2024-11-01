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

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants license controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerLicense extends VREControllerAdmin
{
	/**
	 * Hash validation. Forcing it to be valid is useless.
	 * 
	 * @return void
	 */
	public function pingback()
	{
		$app = JFactory::getApplication();

		// fetch hash generated during the first license validation
		$storedHash = VREFactory::getConfig()->get('licensehash');

		if (!$storedHash)
		{
			// hash not yet stored
			$app->close();
		}

		// recover hash sent by the server
		$serverHash = $app->input->getString('hash');

		// the received hash must be equals to the stored one
		if (strcmp($serverHash, $storedHash))
		{
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, 'Hash mismatch.');
		}
		
		// hash validated successfully
		$app->close();
	}
}
