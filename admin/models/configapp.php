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

VRELoader::import('models.configuration', VREADMIN);

/**
 * VikRestaurants applications configuration model.
 *
 * @since 1.9
 */
class VikRestaurantsModelConfigapp extends VikRestaurantsModelConfiguration
{
	/**
	 * Hook identifier for triggers.
	 *
	 * @var string
	 */
	protected $hook = 'Configapp';

	/**
	 * Validates and prepares the settings to be stored.
	 *
	 * @param 	array 	&$args  The configuration associative array.
	 *
	 * @return 	void
	 */
	protected function validate(&$args)
	{
		$app = JFactory::getApplication();

		if (isset($args['apimaxfail']))
		{
			// the API max failures cannot be lower than 1
			$args['apimaxfail'] = max(1, $args['apimaxfail']);
		}

		if (isset($args['smsapifields']) && !is_string($args['smsapifields']))
		{
			// stringify SMS API fields
			$args['smsapifields'] = json_encode($args['smsapifields']);
		}

		if (isset($args['backupfolder']))
		{
			$tmp = $app->get('tmp_path');

			if (!$args['backupfolder'])
			{
				// path not specified, use temporary folder
				$args['backupfolder'] = $tmp;
			}

			$current = VREFactory::getConfig()->get('backupfolder');

			if (!$current)
			{
				// path was missing, use temporary folder
				$current = $tmp;
			}

			// check whether the backup folder has been moved
			if ($current && $args['backupfolder'] && rtrim($current, DIRECTORY_SEPARATOR) !== rtrim($args['backupfolder'], DIRECTORY_SEPARATOR))
			{
				$backupModel = JModelVRE::getInstance('backup');

				// backup folder moved, try to copy all the existing overrides
				if (!$backupModel->moveArchives($args['backupfolder']))
				{
					// iterate all errors and display them
					foreach ($backupModel->getErrors() as $error)
					{
						$app->enqueueMessage($error, 'warning');
					}
				}
			}
		}
	}
}
