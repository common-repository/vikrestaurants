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
 * VikRestaurants plugin Override controller.
 *
 * @since 1.0
 */
class VikRestaurantsControllerOverride extends VREControllerAdmin
{
	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	boolean
	 */
	public function save()
	{
		$app = JFactory::getApplication();

		$ajax = wp_doing_ajax();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			if ($ajax)
			{
				// ajax request
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
			}
			else
			{
				// post request
				throw new Exception(JText::translate('JINVALID_TOKEN'), 403);
			}
		}

		// make sure the user is authorised to manage overrides
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// action denied
			if ($ajax)
			{
				// ajax request
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}
			else
			{
				// post request
				throw new Exception(JText::translate('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		// get selected file and override
		$client   = $app->input->getString('client', 'site');
		$status   = $app->input->getString('status', '');
		$file     = $app->input->getBase64('selectedfile', '');
		$override = $app->input->getBase64('overridefile', '');

		// build return URL
		$url = sprintf(
			'admin.php?page=vikrestaurants&view=overrides&client=%s&selectedfile=%s&overridefile=%s',
			$client,
			$file,
			$override
		);

		if ($status !== '')
		{
			$url .= '&status=' . (int) $status;
		}

		// register redirect URL
		$this->setRedirect($url);

		// build save data
		$data = [];
		$data['file'] = base64_decode($override);
		$data['code'] = $app->input->get('code', '', 'raw');

		if (empty($data['code']))
		{
			$error = __('No code received', 'vikrestaurants');

			// missing code data
			if ($ajax)
			{
				// ajax request
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(400, $error);
			}
			else
			{
				// post request, enqueue message and do redirect
				$app->enqueueMessage($error, 'error');

				return false;
			}
		}

		// load overrides model
		$overridesModel = $this->getModel('overrides', 'VikRestaurantsModel');

		// make sure the override we are going to create/update is supported
		$supported = $overridesModel->isSupported($client, $data['file']);

		if (!$supported)
		{
			$error = sprintf(__('Cannot use the file as destination: [%s]', 'vikrestaurants'), $data['file']);

			// path not supported
			if ($ajax)
			{
				// ajax request
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, $error);
			}
			else
			{
				// post request, enqueue message and do redirect
				$app->enqueueMessage($error, 'error');

				return false;
			}
		}

		// dispatch model to save the item
		if (!$this->model->save($data))
		{
			// get string error
			$error = $this->model->getError(null, false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Unknown', 500);
			}

			$string = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage());

			if ($ajax)
			{
				// ajax request
				E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $string);
			}
			else
			{
				// post request, enqueue message and do redirect
				$app->enqueueMessage($string, 'error');

				return false;
			}
		}

		if ($ajax)
		{
			// exit in case of AJAX request
			$this->sendJSON(1);
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		return true;
	}

	/**
	 * Deletes a list of records set in the request.
	 *
	 * @return 	boolean
	 */
	public function delete()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// missing CSRF-proof token
			throw new Exception(JText::translate('JINVALID_TOKEN'), 403);
		}

		// make sure the user is authorised to manage overrides
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// action denied
			throw new Exception(JText::translate('JERROR_ALERTNOAUTHOR', 403));
		}

		// get selected file and override
		$client   = $app->input->getString('client', 'site');
		$status   = $app->input->getString('status', '');
		$file     = $app->input->getBase64('selectedfile', '');
		$override = $app->input->getBase64('overridefile', '');

		// build return URL
		$url = sprintf(
			'admin.php?page=vikrestaurants&view=overrides&client=%s&selectedfile=%s&overridefile=%s',
			$client,
			$file,
			$override
		);

		if ($status !== '')
		{
			$url .= '&status=' . (int) $status;
		}

		// register redirect URL
		$this->setRedirect($url);

		// fetch file PK
		$pk = base64_decode($override);

		// load overrides model
		$overridesModel = $this->getModel('overrides', 'VikRestaurantsModel');

		// make sure the override we are going to delete is supported
		$supported = $overridesModel->isSupported($client, $pk);

		if (!$supported)
		{
			// invalid file
			$app->enqueueMessage(sprintf('The file to remove is not an override: [%s]', $pk), 'error');

			return false;
		}

		// dispatch model to delete the item
		if ($this->model->delete($pk))
		{
			$app->enqueueMessage(JText::plural('COM_VIKRESTAURANTS_N_ITEMS_DELETED', 1));
		}

		return true;
	}

	/**
	 * Task used to publish/unpublish an existing override.
	 *
	 * @return 	boolean
	 */
	public function publish()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// missing CSRF-proof token
			throw new Exception(JText::translate('JINVALID_TOKEN'), 403);
		}

		$state = $app->input->get('task') == 'unpublish' ? false : true;

		// make sure the user is authorised to manage overrides
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// action denied
			throw new Exception(JText::translate('JERROR_ALERTNOAUTHOR', 403));
		}

		// get selected file and override
		$client   = $app->input->getString('client', 'site');
		$status   = $app->input->getString('status', '');
		$file     = $app->input->getBase64('selectedfile', '');
		$override = $app->input->getBase64('overridefile', '');

		// build return URL
		$url = sprintf(
			'admin.php?page=vikrestaurants&view=overrides&client=%s&selectedfile=%s&overridefile=%s',
			$client,
			$file,
			$override
		);

		if ($status !== '')
		{
			$url .= '&status=' . (int) $status;
		}

		// register redirect URL
		$this->setRedirect($url);

		// fetch file PK
		$pk = base64_decode($override);

		// load overrides model
		$overridesModel = $this->getModel('overrides', 'VikRestaurantsModel');

		// make sure the override we are going to toggle is supported
		$supported = $overridesModel->isSupported($client, $pk);

		if (!$supported)
		{
			// invalid file
			$app->enqueueMessage(sprintf('The file to publish is not an override: [%s]', $pk), 'error');

			return false;
		}

		// dispatch model to toggle the item
		$this->model->publish($pk, $state);

		return true;
	}

	/**
	 * AJAX end-point used to dismiss the breaking changes.
	 *
	 * @return 	void
	 * 
	 * @since   1.3
	 */
	public function dismissbc()
	{
		$app = JFactory::getApplication();

		// get a list of specified files, if any
		$files = $app->input->get('files', null, 'string');

		// unregister breaking changes
		VikRestaurantsInstaller::unregisterBreakingChanges($files);

		$this->sendJSON(1);
	}
}
