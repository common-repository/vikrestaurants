<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants plugin ACL controller.
 *
 * @since 1.0
 */
class VikRestaurantsControllerAcl extends VREControllerAdmin
{
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
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	boolean
	 */
	public function save()
	{
		$app = JFactory::getApplication();

		// get return URL
		$encoded = $app->input->getBase64('return', '');
		$active  = $app->input->get('activerole', '');

		if ($encoded)
		{
			$return = base64_decode($encoded);
		}
		else
		{
			$return = '';
		}

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken())
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}

		// make sure the user is authorised to change ACL
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			$this->setRedirect($return);

			return false;
		}

		$data = $app->input->get('acl', [], 'array');

		if ($this->model->save($data))
		{
			$app->enqueueMessage(JText::translate('ACL_SAVE_SUCCESS'));
		}
		else
		{
			$app->enqueueMessage(JText::translate('ACL_SAVE_ERROR'), 'error');
		}

		$this->setRedirect('admin.php?page=vikrestaurants&view=acl&activerole=' . $active . '&return=' . $encoded);

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$return = JFactory::getApplication()->input->getBase64('return', '');

		if ($return)
		{
			$return = base64_decode($return);
		}

		$this->setRedirect($return);
	}
}
