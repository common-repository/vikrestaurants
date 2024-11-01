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
 * VikRestaurants operator controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerOperator extends VREControllerAdmin
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

		$data  = [];
		$group = $app->input->getUint('group');

		if ($group)
		{
			$data['group'] = $group;
		}

		// unset user state for being recovered again
		$app->setUserState('vre.operator.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$this->setRedirect('index.php?option=com_vikrestaurants&view=manageoperator');

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
		$app->setUserState('vre.operator.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=manageoperator&cid[]=' . $cid[0]);

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
			// recover group from request
			$group = JFactory::getApplication()->input->getUint('group');

			$url = 'index.php?option=com_vikrestaurants&task=operator.add';

			if ($group)
			{
				$url .= '&group=' . $group;
			}

			$this->setRedirect($url);
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
		
		$args = array();
		$args['code'] 				= $input->get('code', '', 'string');
		$args['firstname'] 			= $input->get('firstname', '', 'string');
		$args['lastname'] 			= $input->get('lastname', '', 'string');
		$args['email'] 				= $input->get('email', '', 'string');
		$args['phone_number'] 		= $input->get('phone_number', '', 'string');
		$args['can_login'] 			= $input->get('can_login', 0, 'uint');
		$args['keep_track'] 		= $input->get('keep_track', 0, 'uint');
		$args['mail_notifications'] = $input->get('mail_notifications', 0, 'uint');
		$args['allres']		        = $input->get('allres', 0, 'uint');
		$args['assign']		        = $input->get('assign', 0, 'uint');
		$args['rooms']		        = $input->get('rooms', [], 'uint');
		$args['products']		    = $input->get('products', [], 'string');
		$args['manage_coupon']		= $input->get('manage_coupon', 0, 'uint');
		$args['group'] 				= $input->get('group', 0, 'uint');
		$args['jid'] 				= $input->get('jid', 0, 'int');
		$args['id'] 				= $input->get('id', 0, 'int');

		// user fields
		$args['user'] = [];
		$args['user']['type']     = $input->get('usertype', [], 'string');
		$args['user']['username'] = $input->get('username', '', 'string');
		$args['user']['password'] = $input->get('password', '', 'string');
		$args['user']['confirm']  = $input->get('confpassword', '', 'string');

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get operator model
		$operator = $this->getModel();

		// try to save arguments
		$id = $operator->save($args);

		if (!$id)
		{
			// update user state data by injecting the user groups and username
			$data = $app->getUserState('vre.operator.data', []);
			$data['usertype'] = $args['user']['type'];
			$data['username'] = $args['user']['username'];
			$app->setUserState('vre.operator.data', $data);

			// get string error
			$error = $operator->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=manageoperator';

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}

			// redirect to new/edit page
			$this->setRedirect($url);
				
			return false;
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect to edit page
		$this->setRedirect('index.php?option=com_vikrestaurants&task=operator.edit&cid[]=' . $id);

		return true;
	}

	/**
	 * Changes the "can login" parameter of the selected records.
	 *
	 * @return 	boolean
	 */
	public function canlogin()
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

		$cid   = $app->input->get('cid', [], 'uint');
		$state = $app->input->get('state', 0, 'uint');

		// check user permissions
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// change state of selected records
		$this->getModel()->publish($cid, $state, 'can_login');

		// back to records list
		$this->cancel();

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
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
	 * Deletes a list of logs set in the request.
	 *
	 * @return 	boolean
	 */
	public function deletelogs()
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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to delete records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// delete selected records
		$this->getModel()->deleteLogs($cid);

		$url = 'index.php?option=com_vikrestaurants&view=operatorlogs';

		$id_operator = $app->input->get('id', 0, 'uint');

		if ($id_operator)
		{
			$url .= '&id=' . $id_operator;
		}

		// back to main list
		$this->setRedirect($url);

		return true;
	}

	/**
	 * Trashes all the logs older than the specified date limit.
	 *
	 * @return 	boolean
	 */
	public function trashlogs()
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

		// check user permissions
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.operators', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to delete records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$limit = $app->input->get('datelimit', '', 'string');

		// delete all records older than the specified limit
		$n = $this->getModel()->flushLogs($limit);

		if ($n)
		{
			$app->enqueueMessage(JText::plural('VRE_DEF_N_ITEMS_DELETED', $n));
		}
		else
		{
			$app->enqueueMessage(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 'warning');
		}

		$url = 'index.php?option=com_vikrestaurants&view=operatorlogs';

		$id_operator = $app->input->get('id', 0, 'uint');

		if ($id_operator)
		{
			$url .= '&id=' . $id_operator;
		}

		// back to main list
		$this->setRedirect($url);

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=operators');
	}
}
