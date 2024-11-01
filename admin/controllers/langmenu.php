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
 * VikRestaurants menus language controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerLangmenu extends VREControllerAdmin
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
		$app->setUserState('vre.langmenu.data', []);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.menus', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$id_menu = $app->input->getUint('id_menu');

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managelangmenu&id_menu=' . $id_menu);

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
		$app->setUserState('vre.langmenu.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.menus', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managelangmenu&cid[]=' . $cid[0]);

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
			// recover menu ID from request
			$id_menu = JFactory::getApplication()->input->getUint('id_menu');

			$url = 'index.php?option=com_vikrestaurants&task=langmenu.add';

			if ($id_menu)
			{
				$url .= '&id_menu=' . $id_menu;
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

		if (!JSession::checkToken())
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}
		
		$args = array();
		$args['name'] 		 = $input->get('name', '', 'string');
		$args['alias'] 		 = $input->get('alias', '', 'string');
		$args['description'] = JComponentHelper::filterText($input->get('description', '', 'raw'));
		$args['id_menu']     = $input->get('id_menu', 0, 'uint');
		$args['id'] 	     = $input->get('id', 0, 'uint');
		$args['tag']         = $input->get('tag', '', 'string');
		
		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get db model
		$langmenu = $this->getModel();

		// try to save arguments
		$id = $langmenu->save($args);

		if (!$id)
		{
			// get string error
			$error = $langmenu->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managelangmenu';

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}
			else
			{
				$url .= '&id_menu=' . $args['id_menu'];
			}

			// redirect to new/edit page
			$this->setRedirect($url);
				
			return false;
		}

		// save sections
		$sections_id      = $input->get('section_id', [], 'uint');
		$sections_lang_id = $input->get('section_lang_id', [], 'uint');
		$sections_name    = $input->get('section_name', [], 'string');
		$sections_desc    = $input->get('section_description', [], 'raw');

		$langsection = $this->getModel('langmenusection');

		for ($i = 0; $i < count($sections_id); $i++)
		{
			$src = [];
			$src['id']          = $sections_lang_id[$i];
			$src['id_section']  = $sections_id[$i];
			$src['name']        = $sections_name[$i];
			$src['description'] = JComponentHelper::filterText($sections_desc[$src['id_section']]);
			$src['id_parent']   = $id;
			$src['tag']         = $args['tag'];

			$langsection->save($src);
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect to edit page
		$this->setRedirect('index.php?option=com_vikrestaurants&task=langmenu.edit&cid[]=' . $id);

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

		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// back to main list, missing CSRF-proof token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->get('cid', [], 'uint');

		// check user permissions
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.menus', 'com_vikrestaurants'))
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
		// recover menu ID from request
		$id_menu = JFactory::getApplication()->input->getUint('id_menu');

		$url = 'index.php?option=com_vikrestaurants&view=langmenus';

		if ($id_menu)
		{
			$url .= '&id_menu=' . $id_menu;
		}

		$this->setRedirect($url);
	}
}
