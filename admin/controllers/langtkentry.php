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
 * VikRestaurants take-away menu entry language controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerLangtkentry extends VREControllerAdmin
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
		$app->setUserState('vre.langtkentry.data', []);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.tkmenus', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$id_entry = $app->input->getUint('id_entry');
		$id_menu  = $app->input->getUint('id_takeaway_menu');

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managelangtkproduct&id_entry=' . $id_entry . '&id_takeaway_menu=' . $id_menu);

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
		$app->setUserState('vre.langtkentry.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.tkmenus', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managelangtkproduct&cid[]=' . $cid[0]);

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

			// recover entry ID from request
			$id_entry = $app->input->getUint('id_entry');
			$id_menu  = $app->input->getUint('id_takeaway_menu');

			$url = 'index.php?option=com_vikrestaurants&task=langtkentry.add';

			if ($id_entry)
			{
				$url .= '&id_entry=' . $id_entry;
			}

			if ($id_menu)
			{
				$url .= '&id_takeaway_menu=' . $id_menu;
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
		
		$args = [];
		$args['name']        = $input->get('name', '', 'string');
		$args['alias']       = $input->get('alias', '', 'string');
		$args['description'] = JComponentHelper::filterText($input->get('description', '', 'raw'));
		$args['id_entry']    = $input->get('id_entry', 0, 'uint');
		$args['id_parent']   = $input->get('id_takeaway_menu', 0, 'uint');
		$args['id'] 	     = $input->get('id', 0, 'uint');
		$args['tag']         = $input->get('tag', '', 'string');
		
		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.tkmenus', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get translation model
		$langentry = $this->getModel();

		// try to save arguments
		$id = $langentry->save($args);

		if (!$id)
		{
			// get string error
			$error = $langentry->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managelangtkproduct';

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}
			else
			{
				$url .= '&id_entry=' . $args['id_entry'] . '&id_takeaway_menu' . $args['id_parent'];
			}

			// redirect to new/edit page
			$this->setRedirect($url);
				
			return false;
		}

		// save options
		$options_id      = $input->get('option_id', [], 'uint');
		$options_lang_id = $input->get('option_lang_id', [], 'uint');
		$options_name    = $input->get('option_name', [], 'string');
		$options_alias   = $input->get('option_alias', [], 'string');

		$langoption = $this->getModel('langtkentryoption');

		for ($i = 0; $i < count($options_id); $i++)
		{
			$option = [];
			$option['id']        = $options_lang_id[$i];
			$option['id_option'] = $options_id[$i];
			$option['name']      = $options_name[$i];
			$option['alias']     = $options_alias[$i];
			$option['id_parent'] = $id;
			$option['tag']       = $args['tag'];

			$langoption->save($option);
		}

		// save toppings
		$groups_id      = $input->get('group_id', [], 'uint');
		$groups_lang_id = $input->get('group_lang_id', [], 'uint');
		$groups_name    = $input->get('group_name', [], 'string');
		$groups_desc    = $input->get('group_description', [], 'string');

		$langgroup = $this->getModel('langtkentrygroup');

		for ($i = 0; $i < count($groups_id); $i++)
		{
			$group = [];
			$group['id']          = $groups_lang_id[$i];
			$group['id_group']    = $groups_id[$i];
			$group['name']        = $groups_name[$i];
			$group['description'] = JComponentHelper::filterText($groups_desc[$group['id_group']]);
			$group['id_parent']   = $id;
			$group['tag']         = $args['tag'];

			$langgroup->save($group);
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect to edit page
		$this->setRedirect('index.php?option=com_vikrestaurants&task=langtkentry.edit&cid[]=' . $id);

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.tkmenus', 'com_vikrestaurants'))
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

		// recover entry ID from request
		$id_entry = $app->input->getUint('id_entry');
		$id_menu  = $app->input->getUint('id_takeaway_menu');

		$url = 'index.php?option=com_vikrestaurants&view=langtkproducts';

		if ($id_entry)
		{
			$url .= '&id_entry=' . $id_entry;
		}

		if ($id_menu)
		{
			$url .= '&id_takeaway_menu=' . $id_menu;
		}

		$this->setRedirect($url);
	}
}
