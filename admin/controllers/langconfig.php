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
 * VikRestaurants configuration language controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerLangconfig extends VREControllerAdmin
{
	/**
	 * Task used to access the creation page of a new record.
	 *
	 * @return 	boolean
	 */
	public function add()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// unset user state for being recovered again
		$app->setUserState('vre.langconfig.data', []);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$param  = $app->input->getString('param');
		$return = $app->input->getString('return');

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managelangconfig&param=' . $param . '&return=' . $return);

		return true;
	}

	/**
	 * Task used to access the management page of an existing record.
	 *
	 * @return 	boolean
	 */
	public function edit()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// unset user state for being recovered again
		$app->setUserState('vre.langconfig.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid    = $app->input->getUint('cid', [0]);
		$return = $app->input->getString('return');

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managelangconfig&cid[]=' . $cid[0] . '&return=' . $return);

		return true;
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the main list.
	 *
	 * @return 	void
	 */
	public function saveclose()
	{
		if ($this->save())
		{
			$this->cancel();
		}
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the creation
	 * page of a new record.
	 *
	 * @return 	void
	 */
	public function savenew()
	{
		if ($this->save())
		{
			$app = JFactory::getApplication();

			// recover configuration parameter ID from request
			$param  = $app->input->getString('param');
			$return = $app->input->getString('return');

			$this->setRedirect('index.php?option=com_vikrestaurants&task=langconfig.add&param=' . $param . '&return=' . $return);
		}
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	boolean
	 */
	public function save()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$user  = JFactory::getUser();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken())
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}
		
		$args = [];
		$args['setting'] = $input->get('setting', '', 'raw');
		$args['param']   = $input->get('param', '', 'string');
		$args['id'] 	 = $input->get('id', 0, 'uint');
		$args['tag']     = $input->get('tag', '', 'string');

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');
	
		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$return = $input->get('return');

		// get translation model
		$langconfig = $this->getModel();

		// try to save arguments
		$id = $langconfig->save($args);

		if (!$id)
		{
			// get string error
			$error = $langconfig->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managelangconfig';

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}
			else
			{
				$url .= '&param=' . $args['param'];
			}

			// redirect to new/edit page
			$this->setRedirect($url . '&return=' . $return);
				
			return false;
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect to edit page
		$this->setRedirect('index.php?option=com_vikrestaurants&task=langconfig.edit&cid[]=' . $id . '&return=' . $return);

		return true;
	}

	/**
	 * Deletes a list of records set in the request.
	 *
	 * @return 	boolean
	 */
	public function delete()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		/**
		 * Added token validation.
		 * Both GET and POST are supported.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->get('cid', [], 'uint');

		// check user permissions
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to delete records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// delete selected records
		$this->getModel()->delete($cid);

		// back to main list
		$this->cancel();

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$app = JFactory::getApplication();

		// recover configuration parameter ID from request
		$param  = $app->input->getString('param');
		$return = $app->input->getString('return');

		$this->setRedirect('index.php?option=com_vikrestaurants&view=langconfig&param=' . $param . '&return=' . $return);
	}
}
