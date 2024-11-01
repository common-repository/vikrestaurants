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
 * VikRestaurants plugin License controller.
 *
 * @since 1.0
 */
class VikRestaurantsControllerLicense extends VREControllerAdmin
{
	/**
	 * License Key validation through ajax request.
	 * This task takes also the change-log for the current version.
	 *
	 * @return 	void
	 */
	public function validate()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// not authorised to view this resource
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('RESOURCE_AUTH_ERROR'));
		}

		// get input key
		$key = $app->input->getString('key');

		// get license model
		$model = $this->getModel();

		// dispatch license key validation
		$response = $model->validate($key);

		// make sure the validation went fine
		if ($response === false)
		{
			// nope, retrieve the error
			$error = $model->getError(null, $toString = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Error', 500);
			}

			// safely propagate the error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $error->getMessage());
		}

		$this->sendJSON($response);
	}

	/**
	 * Downloads the PRO version from VikWP servers.
	 *
	 * @return 	void
	 */
	public function downloadpro()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// not authorised to view this resource
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('RESOURCE_AUTH_ERROR'));
		}

		// get input key
		$key = $app->input->getString('key');

		// get license model
		$model = $this->getModel();

		// dispatch pro version download
		$response = $model->download($key);

		// make sure the download went fine
		if ($response === false)
		{
			// nope, retrieve the error
			$error = $model->getError(null, $toString = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Error', 500);
			}

			// safely propagate the error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $error->getMessage());
		}

		// downloaded successfully
		$this->sendJSON(1);
	}
}
