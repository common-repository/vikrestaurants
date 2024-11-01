<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Document\WebApp\Apps;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Document\WebApp\Manifest;

/**
 * Creates a WebApp manifest for the back-end interface of VikRestaurants.
 * 
 * @since 1.9
 */
class VikRestaurantsAdminManifest implements Manifest
{
	/**
	 * @inheritDoc
	 */
	public function getPath()
	{
		if (\VersionListener::isJoomla())
		{
			// /administrator/components/com_vikrestaurants/assets/manifest.json (Joomla)
			return \JPath::clean(VREADMIN . '/assets/manifest.json');
		}
		else if (\VersionListener::isWordpress())
		{
			// /wp-content/uploads/vikrestaurants/webapp/admin.json (WordPress)
			return \JPath::clean(VRE_UPLOAD_DIR_PATH . '/webapp/admin.json');
		}

		// cannot deal with the provided platform
		throw new \RuntimeException('Invalid platform');
	}

	/**
	 * @inheritDoc
	 */
	public function buildJson()
	{
		$manifest = new \stdClass;

		/**
		 * Set up the name of the web application.
		 * You can change the name of the web application simply by creating an override
		 * for the "COM_VIKRESTAURANTS" language definition.
		 * 
		 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest/name
		 */
		$manifest->name = \JText::translate('COM_VIKRESTAURANTS');

		// make sure a translation exists for "COM_VIKRESTAURANTS" as the sys.ini (in Joomla)
		// might be loaded after dispatching the component
		if ($manifest->name === 'COM_VIKRESTAURANTS')
		{
			// missing translation, use the default component name
			$manifest->name = 'VikRestaurants';
		}

		/**
		 * Make sure the application does not look like a browser.
		 * 
		 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest/display
		 */
		$manifest->display = 'standalone';

		/**
		 * Define here the entry point of the application (VikRestaurants back-end).
		 * 
		 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest/start_url
		 */
		$manifest->start_url = \VREFactory::getPlatform()->getUri()->admin('index.php?option=com_vikrestaurants');

		/**
		 * Specify here what are the icons to use for the web application.
		 * 
		 * @link https://developer.mozilla.org/en-US/docs/Web/Manifest/icons
		 */
		$manifest->icons = [];

		if ($logo = \VREFactory::getConfig()->get('companylogo'))
		{
			// use the company logo as icon for the web application
			$manifest->icons[] = (object) [
				'src'   => VREMEDIA_URI . $logo,
				'sizes' => 'any',
			];
		}

		return $manifest;
	}
}
