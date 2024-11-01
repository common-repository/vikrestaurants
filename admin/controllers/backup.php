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
 * VikRestaurants backup controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerBackup extends VREControllerAdmin
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
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$ajax = $app->input->getBool('ajax');

		if (!JSession::checkToken())
		{
			if ($ajax)
			{
				// missing CSRF-proof token
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
			}
			else
			{
				// back to main list, missing CSRF-proof token
				$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
				$this->cancel();

				return false;
			}
		}
		
		// fetch requested action
		$args = [];
		$args['action'] = $app->input->get('backup_action', 'create');

		if ($args['action'] === 'create')
		{
			// get requested backup type
			$args['type'] = $app->input->get('type');
		}
		else
		{
			/**
			 * Take uploaded file.
			 * Use "raw" filter because Joomla seems to block the attachments
			 * containing PHP files.
			 */
			$args['file'] = $app->input->files->get('file', null, 'raw');
		}

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.admin', 'com_vikrestaurants'))
		{
			if ($ajax)
			{
				// not allowed
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}
			else
			{
				// back to main list, not authorised to create/edit records
				$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
				$this->cancel();

				return false;
			}
		}

		// get backup model
		$backup = $this->getModel();

		// try to save arguments
		$id = $backup->save($args);

		if ($id === false)
		{
			// get string error
			$error = $backup->getError(null, true);

			if ($ajax)
			{
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, $error);
			}
			else
			{
				// display error message
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

				// redirect to list page
				$this->cancel();
					
				return false;
			}
		}

		if ($ajax)
		{
			// send the details of the created backup
			$this->sendJSON($backup->getItem($id));
		}
		else
		{
			// display generic successful message
			$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

			// redirect to list page
			$this->cancel();

			return true;
		}
	}

	/**
	 * Deletes a list of records set in the request.
	 *
	 * @return 	boolean
	 */
	public function delete()
	{
		$app = JFactory::getApplication();
		$cid = $app->input->get('cid', [], 'string');

		/**
		 * Added token validation.
		 * Both GET and POST are supported.
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}

		// check user permissions
		if (!JFactory::getUser()->authorise('core.delete', 'com_vikrestaurants') || !JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to delete records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// delete selected records
		$res = $this->getModel()->delete($cid);

		// back to main list
		$this->cancel();

		return true;
	}

	/**
	 * Restores the specified backup.
	 *
	 * @return 	boolean
	 */
	public function restore()
	{
		$app = JFactory::getApplication();
		$cid = $app->input->get('cid', [], 'string');

		// take only the first backup
		$cid = array_shift($cid);

		/**
		 * Added token validation.
		 * Both GET and POST are supported.
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}

		// check user permissions
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to delete records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$model = $this->getModel();

		// restore backup
		$res = $model->restore($cid);

		if (!$res)
		{
			// get last error
			$error = $model->getError(null, true);

			if ($error)
			{
				$app->enqueueMessage($error, 'error');
			}
		}
		else
		{
			$app->enqueueMessage(JText::translate('VRE_BACKUP_RESTORED'));
		}

		// back to main list
		$this->cancel();

		return $res;
	}

	/**
	 * End-point used to download a backuo archive.
	 * 
	 * @return 	boolean
	 */
	public function download()
	{
		$app = JFactory::getApplication();
		$cid = $app->input->get('cid', [], 'string');

		// take only the first backup
		$cid = array_shift($cid);

		/**
		 * Added token validation.
		 * Both GET and POST are supported.
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}

		// check user permissions
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to delete records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// fetch backup details
		$item = $this->getModel()->getItem($cid);

		if (!$item)
		{
			// backup not found
			$app->enqueueMessage(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 'error');
			$this->cancel();

			return false;
		}

		// execute archive download
		E4J\VikRestaurants\Archive\Factory::download($item->path);

		$app->close();
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=backups');
	}

	/**
	 * Redirects the users to the parent records list.
	 *
	 * @return 	void
	 */
	public function back()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=editconfigapp');
	}
}
