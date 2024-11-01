<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9_1;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * The update to the 1.9 version of VikRestaurants brought a few bugs,
 * mainly related to the 1.9 SQL installation file, which was missing
 * the INSERT of some configuration settings.
 * 
 * When releasing the new 1.9.1 version, we should create a fixer that
 * makes sure all the configuration settings are properly created.
 *
 * @since 1.9.1
 */
class ConfigurationFixer extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$config = \VREFactory::getConfig();

		// define here the list of all the settings to check, where the
		// key is the paramater name and the value is the default setting
		$lookup = [
			'tkdeftax'     => '',
			'tkusetaxbd'   => 0,
			'backuptype'   => 'full',
			'backupfolder' => '',
		];

		foreach ($lookup as $param => $default)
		{
			// check whether the setting already exists
			if ($config->has($param))
			{
				// the setting already exists, 
				continue;
			}

			// install the setting with the default value
			$config->set($param, $default);
		}

		return true;
	}
}
