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
 * VikRestaurants oversight controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerOversight extends VREControllerAdmin
{
	/**
	 * Disconnects the current logged-in user.
	 *
	 * @return 	void
	 */
	public function logout()
	{
		$app = JFactory::getApplication();

		// disconnect current user
		$app->logout(JFactory::getUser()->id);

		$url = 'index.php?option=com_vikrestaurants&view=oversight';

		$itemid = $app->input->get('Itemid', 0, 'uint');

		if ($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}

		$this->setRedirect(JRoute::rewrite($url, false));
	}

	/**
	 * Task used to switch table for the given reservation.
	 *
	 * @return 	boolean
	 */
	public function changetable()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
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

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$args = [];
		$args['id_table'] = $app->input->get('newid', 0, 'uint');
		$args['id']       = $app->input->get('id_order', 0, 'uint');

		// check user permissions (do not allow creation of new reservations here)
		if (!$operator->isRestaurantAllowed() || !$args['id'] || (!$operator->canSeeAll() && !$operator->canAssign($args['id_order'])))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get search arguments from request
		$date    = $app->input->get('date', '', 'string');
		$hourmin = $app->input->get('hourmin', '', 'string');
		$table   = $args['id_table'];

		// get reservation model
		$model = $this->getModel('reservation');

		// recover number of people from reservation details
		$reservation = $model->getItem((int) $args['id']);

		if (!$reservation)
		{
			throw new Exception('Unable to find the reservation [' . $args['id'] . ']', 404);
		}

		// create search parameters
		$search = new VREAvailabilitySearch($date, $hourmin, $reservation->people);

		// check if the specified table is available
		if ($search->isTableAvailable($table, $args['id']))
		{
			// update reservation
			if ($model->save($args))
			{
				$app->enqueueMessage(JText::translate('VRMAPTABLECHANGEDSUCCESS'));
			}
			else
			{
				// get string error
				$error = $model->getError(null, true);

				// display error message
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');
			}
		}
		else
		{
			// table already occupied
			$app->enqueueMessage(JText::translate('VRMAPTABLENOTCHANGED'), 'error');
		}

		$this->cancel();

		return true;
	}

	/**
	 * AJAX end-point used to change the status code of a reservation.
	 *
	 * @return 	void
	 */
	public function changecodeajax()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}
		
		$id_order   = $app->input->get('id', 0, 'uint');
		$id_rescode = $app->input->get('id_code', 0, 'uint');
		$notes 		= $app->input->get('notes', null, 'string');
		$group      = $app->input->get('group', 1, 'uint');

		if ($notes === '')
		{
			// prevent the system from resetting the existing notes in case of update
			$notes = null;
		}

		$args = [
			'id'    => $id_rescode,
			'notes' => $notes,
		];

		// for restaurant only
		if ($group == 1)
		{
			// check if the operator can edit the order
			if (!$operator->canSeeAll() && !$operator->canAssign($id_order))
			{
				// raise AJAX error, not authorised to edit records
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}

			// do not update operator in case of master account
			if (!$operator->canSeeAll())
			{
				$args['id_operator'] = $operator->get('id');
			}
		}

		/** @var JModelLegacy */
		$model = $this->getModel($group === 1 ? 'reservation' : 'tkreservation');

		// attempt to change the reservation code
		$saved = $model->changeCode($id_order, $args);

		if (!$saved)
		{
			// obtain the latest error
			$error = $model->getError();

			if (!$error instanceof Exception)
			{
				// create an exception for a better ease of use
				$error = new Exception($error ?: 'Error', 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}
		
		// get reservation codes details
		$rescode = JHtml::fetch('vikrestaurants.rescode', $id_rescode, $group);

		$this->sendJSON($rescode);
	}

	/**
	 * AJAX end-point used to change the reservation notes.
	 *
	 * @return 	void
	 */
	public function savenotesajax()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$args = [];
		$args['id']    = $app->input->get('id', 0, 'uint');
		$args['notes'] = $app->input->get('notes', '', 'string');

		// check if the operator can edit the order
		if (!$args['id'] || (!$operator->canSeeAll() && !$operator->canAssign($args['id'])))
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// do not update operator in case of master account
		if (!$operator->canSeeAll())
		{
			$args['id_operator'] = $operator->get('id');
		}

		/** @var JModelLegacy */
		$model = $this->getModel('reservation');

		// attempt to change the reservation notes
		$saved = $model->save($args);

		if (!$saved)
		{
			// obtain the latest error
			$error = $model->getError();

			if (!$error instanceof Exception)
			{
				// create an exception for a better ease of use
				$error = new Exception($error ?: 'Error', 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(
				$error->getCode(),
				JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage())
			);
		}

		$this->sendJSON(1);
	}

	/**
	 * AJAX end-point used to assign an operator to the reservation.
	 *
	 * @return 	void
	 */
	public function assignoperatorajax()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$args = [];
		$args['id']          = $app->input->get('id', 0, 'uint');
		$args['id_operator'] = $app->input->get('id_operator', 0, 'uint');

		// check user permissions (abort in case the order ID is missing)
		if (!$operator->canSeeAll() || !$args['id'])
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		/** @var JModelLegacy */
		$model = $this->getModel('reservation');

		// attempt to change the reservation operator
		$saved = $model->save($args);

		if (!$saved)
		{
			// obtain the latest error
			$error = $model->getError();

			if (!$error instanceof Exception)
			{
				// create an exception for a better ease of use
				$error = new Exception($error ?: 'Error', 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(
				$error->getCode(),
				JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage())
			);
		}

		$this->sendJSON(1);
	}

	/**
	 * Task used to save a reservation closure.
	 *
	 * @param 	boolean  $ajax  True if the request has been made via AJAX.
	 *
	 * @return 	boolean
	 */
	public function saveclosure($ajax = false)
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			if ($ajax)
			{
				// raise error
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

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator || !$operator->isRestaurantAllowed())
		{
			if ($ajax)
			{
				// raise error
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}
			else
			{
				// raise error, not authorised to access private area
				$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
				$this->cancel();

				return false;
			}
		}
		
		$args = [];
		$args['id'] = $app->input->get('id', 0, 'uint');

		if ($app->input->getBool('reopen'))
		{
			// permanently delete closure in case "RE-OPEN" checkbox was checked
			$this->getModel('closure')->reopen($args['id']);
			$this->cancel();
			return true;
		}

		// load closure data from request
		$args['date']      = $app->input->get('date', '', 'string');
		$args['hourmin']   = $app->input->get('hourmin', '', 'string');
		$args['hour']	   = $app->input->get('hour', '', 'string');
		$args['min']	   = $app->input->get('min', '', 'string');
		$args['id_table']  = $app->input->get('id_table', 0, 'string');
		$args['notes']     = $app->input->get('notes', '', 'raw');
		$args['stay_time'] = $app->input->get('stay_time', 0, 'uint');

		// get record table
		$closure = $this->getModel('closure');

		// try to save arguments
		$id = $closure->save($args);

		if (!$id)
		{
			// get string error
			$error = $closure->getError();

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			if ($ajax)
			{
				// raise error
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(
					$error->getCode(),
					JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage())
				);
			}
			else
			{
				// display error message
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage()), 'error');
				$this->cancel();
				return false;
			}
		}

		if ($ajax)
		{
			$this->sendJSON($id);
		}

		$this->cancel();
		return true;
	}

	/**
	 * AJAX end-point used to save a reservation closure.
	 *
	 * @return 	void
	 *
	 * @since 	1.8.1
	 */
	public function saveclosureajax()
	{
		$this->saveclosure(true);
	}

	/**
	 * AJAX end-point used to obtain the widget contents
	 * or datasets.
	 *
	 * @return 	void
	 */
	public function loadwidgetdata()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// first of all, get selected group
		$group = $app->input->get('group', 'restaurant', 'string');

		// fetch ACL rule based on group
		$rule = $group == 'restaurant' ? 'isRestaurantAllowed' : 'isTakeawayAllowed';

		// check user permissions
		if (!$operator->$rule())
		{
			// raise error, not authorised to access statistics
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get widget name and ID
		$widget = $app->input->get('widget', '', 'string');
		$id     = $app->input->get('id', 0, 'uint');

		VikRestaurants::loadLanguage(JFactory::getLanguage()->getTag(), JPATH_ADMINISTRATOR);

		VRELoader::import('library.statistics.factory');

		try
		{
			// try to instantiate the widget
			$widget = VREStatisticsFactory::getWidget($widget, $group);

			// set up widget ID
			$widget->setID($id);

			// fetch widget data
			$data = $widget->getData();
		}
		catch (Exception $e)
		{
			// an error occurred while trying to access the widget
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
		}

		// encode data in JSON and return them
		$this->sendJSON(json_encode($data));
	}

	/**
	 * AJAX end-point used to confirm a reservation.
	 *
	 * @return 	void
	 */
	public function confirmajax()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$ids   = $app->input->get('cid', [], 'uint');
		$group = $app->input->get('group', 1, 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$operator->isGroupAllowed($group) || count($ids) == 0)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get reservation model
		$model = $this->getModel($group === 1 ? 'reservation' : 'tkreservation');

		foreach ($ids as $id)
		{
			$data = [
				'id'         => $id,
				'status'     => JHtml::fetch('vrehtml.status.confirmed', $group === 1 ? 'restaurant' : 'takeaway', 'code'),
				'need_notif' => 1,
			];

			// update reservation
			if (!$model->save($data))
			{
				// get string error
				$error = $model->getError();

				if (!$error instanceof Exception)
				{
					$error = new Exception($error, 500);
				}

				// raise returned error while saving the record
				E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $error->getMessage());
			}
		}

		$this->sendJSON(1);
	}

	/**
	 * AJAX end-point used to decline a reservation.
	 *
	 * @return 	void
	 */
	public function refuseajax()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$ids   = $app->input->get('cid', [], 'uint');
		$group = $app->input->get('group', 1, 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$operator->isGroupAllowed($group) || count($ids) == 0)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get reservation model
		$model = $this->getModel($group === 1 ? 'reservation' : 'tkreservation');

		foreach ($ids as $id)
		{
			$data = [
				'id'     => $id,
				'status' => JHtml::fetch('vrehtml.status.removed', $group === 1 ? 'restaurant' : 'takeaway', 'code'),
			];

			// update reservation
			if (!$model->save($data))
			{
				// get string error
				$error = $model->getError();

				if (!$error instanceof Exception)
				{
					$error = new Exception($error, 500);
				}

				// raise returned error while saving the record
				E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $error->getMessage());
			}
		}

		$this->sendJSON(1);
	}

	/**
	 * AJAX end-point used to notify a reservation.
	 *
	 * @return 	void
	 */
	public function notifyajax()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$ids   = $app->input->get('cid', [], 'uint');
		$group = $app->input->get('group', 1, 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$operator->isGroupAllowed($group) || count($ids) == 0)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get reservation model
		$model = $this->getModel($group === 1 ? 'reservation' : 'tkreservation');

		foreach ($ids as $id)
		{
			$data = [
				'id'         => $id,
				'need_notif' => 0,
				'notifycust' => 1,
			];

			// update reservation
			if (!$model->save($data))
			{
				// get string error
				$error = $model->getError();

				if (!$error instanceof Exception)
				{
					$error = new Exception($error, 500);
				}

				// raise returned error while saving the record
				E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $error->getMessage());
			}
		}

		$this->sendJSON(1);
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$app = JFactory::getApplication();

		$query = [];
		$query['datefilter'] = $app->input->getString('date', '');
		$query['hourmin']    = $app->input->getString('hourmin', '');
		$query['people']     = $app->input->getUint('people', 0);

		$query['Itemid'] = $app->input->getUint('Itemid', 0);

		// remove empty attributes
		$query = array_filter($query);

		$from = $app->input->get('from', null);

		// build return URL
		$url = 'index.php?option=com_vikrestaurants&view=' . ($from ? $from : 'opreservations'). '&' . http_build_query($query);

		$this->setRedirect(JRoute::rewrite($url, false));
	}
}
