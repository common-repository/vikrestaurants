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
 * VikRestaurants special day controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerSpecialday extends VREControllerAdmin
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
		$app->setUserState('vre.specialday.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.specialdays', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managespecialday');

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
		$app->setUserState('vre.specialday.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.specialdays', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managespecialday&cid[]=' . $cid[0]);

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

			$url = 'index.php?option=com_vikrestaurants&task=specialday.add';

			if ($group)
			{
				$url .= '&group=' . $group;
			}

			$this->setRedirect($url);
		}
	}

	/**
	 * Task used to save the record data as a copy of the current item.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	void
	 * 
	 * @since   1.9
	 */
	public function savecopy()
	{
		$this->save(true);
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 * 
	 * @param 	boolean  $copy  True to save the record as a copy (@since 1.9).
	 *
	 * @return 	boolean
	 */
	public function save($copy = false)
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
		$args['name']              = $input->getString('name');
		$args['start_ts']          = $input->getString('start_ts');
		$args['end_ts']            = $input->getString('end_ts');
		$args['working_shifts']    = $input->getString('working_shifts', []);
		$args['days_filter']       = $input->getString('days_filter', []);
		$args['askdeposit']        = $input->getUint('askdeposit', 0);
		$args['depositcost']       = $input->getFloat('depositcost', 0);
		$args['perpersoncost']     = $input->getUint('perpersoncost', 0);
		$args['peopleallowed']     = $input->getInt('peopleallowed', 0);
		$args['markoncal']         = $input->getUint('markoncal', 0);
		$args['ignoreclosingdays'] = $input->getUint('ignoreclosingdays', 0);
		$args['priority']          = $input->getUint('priority', 0);
		$args['choosemenu']        = $input->getUint('choosemenu', 0);
		$args['freechoose']        = $input->getUint('freechoose', 0);
		$args['minorder']          = $input->getFloat('minorder', 0);
		$args['delivery_service']  = $input->getInt('delivery_service', -1);
		$args['delivery_areas']    = $input->getUint('delivery_areas', array());
		$args['images']            = $input->getString('images', []);
		$args['menus']             = $input->getUint('id_menu', []);
		$args['group']             = $input->getUint('group', 0);
		$args['id'] 	           = $input->getUint('id', 0);

		if ($copy)
		{
			// unset ID to create a copy
			$args['id'] = 0;
		}

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.specialdays', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get special day model
		$specialday = $this->getModel();

		// try to save arguments
		$id = $specialday->save($args);

		if (!$id)
		{
			// get string error
			$error = $specialday->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managespecialday';

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
		$this->setRedirect('index.php?option=com_vikrestaurants&task=specialday.edit&cid[]=' . $id);

		return true;
	}

	/**
	 * Changes the "mark on calendar" parameter of the selected records.
	 *
	 * @return 	boolean
	 */
	public function markoncal()
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
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.specialdays', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// change state of selected records
		$this->getModel()->publish($cid, $state, 'markoncal');

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.specialdays', 'com_vikrestaurants'))
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
		$this->setRedirect('index.php?option=com_vikrestaurants&view=specialdays');
	}

	/**
	 * AJAX end-point used to test the special days.
	 *
	 * @return 	void
	 *
	 * @since 	1.8.2
	 */
	public function test()
	{
		$app = JFactory::getApplication();

		$args = [];
		$args['group'] = $app->getUserStateFromRequest('vre.specialdaystest.group', 'group', 'restaurant', 'string');
		$args['date']  = $app->getUserStateFromRequest('vre.specialdaystest.date', 'date', '', 'string');

		// make sure the group is supported
		$args['group'] = JHtml::fetch('vrehtml.admin.getgroup', $args['group'], ['restaurant', 'takeaway']);
		
		if (!$args['date'])
		{
			// if not specified, use the current date
			$args['date'] = date(VREFactory::getConfig()->get('dateformat'), VikRestaurants::now());
		}

		// instantiate special days manager
		$sdManager = new VRESpecialDaysManager($args['group']);

		// set date filter
		$sdManager->setStartDate($args['date']);

		// recover the list of supported special days
		$sdList = $sdManager->getList();

		// check if the specified date is closed
		$closed = VikRestaurants::isClosingDay($args);

		// get global working shifts for the specified date
		$shifts = JHtml::fetch('vikrestaurants.shifts', $sdManager->getGroup(), $args['date'], $strict = false);

		// prepare layout file
		$layout = new JLayoutFile('blocks.sdtest');
		// render layout
		$html = $layout->render([
			'list'   => $sdList,
			'closed' => (bool) $closed,
			'args'   => $args,
			'shifts' => $shifts,
		]);

		$this->sendJSON(json_encode($html));
	}
}
