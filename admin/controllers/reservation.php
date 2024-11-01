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
 * VikRestaurants reservation controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerReservation extends VREControllerAdmin
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

		$data = [];

		// use the checkin date, if specified
		$checkin_date = $app->input->getString('date', '');

		if ($checkin_date)
		{
			$data['date'] = $checkin_date;
		}

		// use the checkin time, if specified
		$checkin_time = $app->input->getString('hourmin', '');

		if ($checkin_time)
		{
			$data['hourmin'] = $checkin_time;
		}

		// use the table, if specified
		$id_table = $app->input->getUint('id_table', 0);

		if ($id_table)
		{
			$data['id_table'] = $id_table;
		}

		// use the number of participants, if specified
		$people = $app->input->getUint('people', 0);

		if ($people)
		{
			$data['people'] = $people;
		}

		// unset user state for being recovered again
		$app->setUserState('vre.reservation.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// recover incoming view
		$from = $app->input->get('from');

		$url = 'index.php?option=com_vikrestaurants&view=managereservation';

		if ($from)
		{
			$url .= '&from=' . $from;
		}

		$this->setRedirect($url);

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
		$app->setUserState('vre.reservation.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		// recover incoming view
		$from = $app->input->get('from');

		$url = 'index.php?option=com_vikrestaurants&view=managereservation&cid[]=' . $cid[0];

		if ($from)
		{
			$url .= '&from=' . $from;
		}

		$this->setRedirect($url);

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
			// recover incoming view
			$from = JFactory::getApplication()->input->get('from');

			$url = 'index.php?option=com_vikrestaurants&task=reservation.add';

			if ($from)
			{
				$url .= '&from=' . $from;
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
		$args['date']                 = $input->getString('date', '');
		$args['hourmin']              = $input->getString('hourmin', '');
		$args['id_table']             = $input->getString('id_table', '');
		$args['people']               = $input->getUint('people', 0);
		$args['id_user'] 	          = $input->getUint('id_user', 0);
		$args['purchaser_nominative'] = $input->getString('purchaser_nominative', '');
		$args['purchaser_mail']       = $input->getString('purchaser_mail', '');
		$args['purchaser_phone']      = $input->getString('purchaser_phone', '');
		$args['purchaser_prefix']     = $input->getString('purchaser_prefix', '');
		$args['purchaser_country']    = $input->getString('purchaser_country', '');
		$args['notifycust']           = $input->getBool('notifycust', false);
		$args['deposit']              = $input->getFloat('deposit', 0.0);
		$args['total_net']            = $input->getFloat('total_net', 0.0);
		$args['total_tax']            = $input->getFloat('total_tax', 0.0);
		$args['bill_value']           = $input->getFloat('bill_value', 0.0);
		$args['bill_closed']          = $input->getUint('bill_closed', 0);
		$args['status']               = $input->getString('status', '');
		$args['id_payment']           = $input->getUint('id_payment', 0);
		$args['notes']                = JComponentHelper::filterText($input->getRaw('notes', ''));
		$args['stay_time']            = $input->getUint('stay_time', 0);
		$args['id']                   = $input->getInt('id', 0);

		$args['menus'] = $input->get('menus', [], 'array');
		$args['items'] = $input->get('item_json', [], 'array');

		// also register the deleted items to properly recover them in case of failure
		$args['deleted_items'] = $input->get('item_deleted', [], 'uint');

		// get discount actions
		$args['add_discount']    = $input->getString('add_discount', '');
		$args['remove_discount'] = $input->getBool('remove_discount', false);

		if ($args['add_discount'] === 'manual')
		{
			// fetch manual discount from request
			$args['add_discount'] = $input->get('manual_discount', [], 'array');
		}

		// get tip actions
		$args['add_tip']    = $input->get('manual_tip', [], 'array');
		$args['remove_tip'] = $input->getBool('remove_tip', false);

		/**
		 * Retrieve custom fields by using the related helper.
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter)
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

		// create requestor for the restaurant custom fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor($customFields);

		// load custom fields
		$args['custom_f'] = $requestor->loadForm($tmp, $strict = false);

		// register data fetched by the custom fields so that the reservation
		// model is able to use them for saving purposes
		$args['fields_data'] = $tmp;

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// recover incoming view
		$from = $input->get('from');

		// get reservation model
		$reservation = $this->getModel();

		// try to save arguments
		$id = $reservation->save($args);

		if (!$id)
		{
			// get string error
			$error = $reservation->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managereservation';

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}

			if ($from)
			{
				$url .= '&from=' . $from;
			}

			// redirect to new/edit page
			$this->setRedirect($url);
				
			return false;
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		$url = 'index.php?option=com_vikrestaurants&task=reservation.edit&cid[]=' . $id;

		if ($from)
		{
			$url .= '&from=' . $from;
		}

		// redirect to edit page
		$this->setRedirect($url);

		// check if we have any registered errors, which may refer to a failure
		// of the e-mail notification
		$error = $reservation->getError(null, true);

		if ($error)
		{
			// display as warning message
			$app->enqueueMessage($error, 'warning');
		}

		return true;
	}

	/**
	 * Toggles the bill status (open/closed) of the selected records.
	 *
	 * @return 	boolean
	 */
	public function changebill()
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
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// change state of selected records
		$this->getModel()->publish($cid, $state, 'bill_closed');

		// back to records list
		$this->cancel();

		return true;
	}

	/**
	 * Task used to switch table for the given reservation.
	 *
	 * @return 	boolean
	 */
	public function changetable()
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

		$args = [];
		$args['id_table'] = $app->input->get('newid', 0, 'uint');
		$args['id']       = $app->input->get('id_order', 0, 'uint');

		// check user permissions (do not allow creation of new reservations here)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants') || !$args['id'])
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
		$model = $this->getModel();

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
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
		// recover incoming view
		$from = JFactory::getApplication()->input->get('from', null);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=' . ($from ? $from : 'reservations'));
	}

	/**
	 * AJAX end-point used to switch table for the given reservation.
	 *
	 * @return 	void
	 *
	 * @since 	1.8.3
	 */
	public function changetableajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$args = [];
		$args['id_table'] = $app->input->get('id_table', 0, 'uint');
		$args['id']       = $app->input->get('id_order', 0, 'uint');

		// check user permissions (do not allow creation of new reservations here)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants') || !$args['id'])
		{
			// not authorised to create/edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// do not search for tables availability

		/** @var JModelLegacy */
		$model = $this->getModel();

		// attempt to change the reservation table
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
	 * AJAX end-point used to change the status code of a reservation.
	 *
	 * @return 	void
	 */
	public function changecodeajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$id_order   = $app->input->get('id', 0, 'uint');
		$id_rescode = $app->input->get('id_code', 0, 'uint');
		$notes 		= $app->input->get('notes', null, 'string');

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		/** @var JModelLegacy */
		$model = $this->getModel();

		// attempt to change the reservation code
		$saved = $model->changeCode($id_order, [
			'id'    => $id_rescode,
			'notes' => $notes,
		]);

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
		$rescode = JHtml::fetch('vikrestaurants.rescode', $id_rescode, $group = 1);

		// send response to caller
		$this->sendJSON($rescode);
	}

	/**
	 * AJAX end-point used to change the status of the reservation.
	 * The task expects the following arguments to be set in request.
	 *
	 * @return 	void
	 * 
	 * @since   1.9
	 */
	public function changestatusajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		$data = [];
		$data['id']     = $app->input->getUint('id');
		$data['status'] = $app->input->getString('status');

		// check user permissions
		if (!$data['id'] || !$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			// not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get status code details
		$code = JHtml::fetch('vrehtml.status.find', '*', ['code' => $data['status']], $limit = true);

		if (!$code)
		{
			// code not found
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(404, JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
		}

		// register comment for status change
		// $data['status_comment'] = 'VRE_STATUS_CHANGED_FROM_LIST';

		// update status
		$this->getModel()->save($data);

		// render HTML
		$code->html = JHtml::fetch('vrehtml.status.display', $code, $app->input->getString('layout'));

		// send code to caller
		$this->sendJSON($code);
	}

	/**
	 * AJAX end-point used to change the reservation notes.
	 *
	 * @return 	void
	 */
	public function savenotesajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$args = [];
		$args['id']    = $app->input->get('id', 0, 'uint');
		$args['notes'] = $app->input->get('notes', '', 'string');

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants') || !$args['id'])
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		/** @var JModelLegacy */
		$model = $this->getModel();

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
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$args = [];
		$args['id']          = $app->input->get('id', 0, 'uint');
		$args['id_operator'] = $app->input->get('id_operator', 0, 'uint');

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$args['id'])
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		/** @var JModelLegacy */
		$model = $this->getModel();

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
	 * AJAX end-point to obtain a JSON list of available tables for
	 * the specified search arguments.
	 *
	 * @return 	void
	 */
	public function availabletablesajax()
	{
		$app = JFactory::getApplication();

		$filters = [];
		$filters['date']     = $app->input->get('date', '', 'string');
		$filters['hourmin']  = $app->input->get('hourmin', '0:0', 'string');
		$filters['people']   = $app->input->get('people', 2, 'uint');
		$filters['staytime'] = $app->input->get('staytime', null, 'uint');
		$filters['id_res']   = $app->input->get('id_res', 0, 'uint');

		$mapOptions = $app->input->get('options', [], 'array');

		/** @var JModelLegacy */
		$mapModel = $this->getModel('map');

		$rooms = [];

		foreach ($mapModel->getRooms() as $room)
		{
			/** @var VREMapFactory */
			$renderer = $mapModel->createMapRenderer($room->id, $filters, $mapOptions);

			$rooms[$room->id] = $renderer->admin()->build();
		}

		$this->sendJSON($rooms);
	}

	/**
	 * AJAX end-point to obtain a JSON list of available menus for
	 * the specified search arguments.
	 *
	 * @return 	void
	 * 
	 * @since   1.9
	 */
	public function availablemenusajax()
	{
		$app = JFactory::getApplication();

		$filters = [];
		$filters['date']    = $app->input->get('date', '', 'string');
		$filters['hourmin'] = $app->input->get('hourmin', '0:0', 'string');

		/** @var object[] */
		$menus = VikRestaurants::getAllAvailableMenusOn($filters, $choosable = true);

		$this->sendJSON($menus);
	}

	/**
	 * AJAX end-point used to confirm a reservation.
	 *
	 * @return 	void
	 */
	public function confirmajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$ids = $app->input->get('cid', [], 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants')
			|| !$user->authorise('core.access.reservations', 'com_vikrestaurants')
			|| count($ids) == 0)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get reservation model
		$model = $this->getModel();

		foreach ($ids as $id)
		{
			$data = [
				'id'         => $id,
				'status'     => JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code'),
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
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$ids = $app->input->get('cid', [], 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants')
			|| !$user->authorise('core.access.reservations', 'com_vikrestaurants')
			|| count($ids) == 0)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get reservation model
		$model = $this->getModel();

		try
		{
			// look for a specific rejected status code
			$status = JHtml::fetch('vrehtml.status.rejected', 'restaurant', 'code');
		}
		catch (Exception $e)
		{
			// rejected status code not supported, fallback to removed status
			$status = JHtml::fetch('vrehtml.status.removed', 'restaurant', 'code');	
		}

		foreach ($ids as $id)
		{
			$data = [
				'id'     => $id,
				'status' => $status,
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
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$ids = $app->input->get('cid', array(), 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants')
			|| !$user->authorise('core.access.reservations', 'com_vikrestaurants')
			|| count($ids) == 0)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get reservation model
		$model = $this->getModel();

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
	 * Sends a notification SMS to the customer of the specified reservation.
	 *
	 * @return 	void
	 */
	public function sendsms()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$user  = JFactory::getUser();

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
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to send SMS notifications
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}
		
		$ids = $input->get('cid', [], 'uint');

		// get reservation model
		$model = $this->getModel();

		$notified = 0;
		$errors   = array();

		foreach ($ids as $id)
		{
			// try to send SMS notification
			if ($model->sendSmsNotification($id))
			{
				$notified++;
			}
			else
			{
				// get string error
				$error = $model->getError(null, true);

				// enqueue error message
				$errors[] = $error;
			}
		}

		if ($notified)
		{
			// successful message
			$app->enqueueMessage(JText::plural('VRCUSTOMERSMSSENT', $notified));
		}
		else
		{
			// no notifications sent
			$app->enqueueMessage(JText::plural('VRCUSTOMERSMSSENT', $notified), 'warning');
		}

		// display any returned errors
		if ($errors)
		{
			// do not display duplicate or empty errors
			$errors = array_unique(array_filter($errors));

			foreach ($errors as $err)
			{
				$app->enqueueMessage($err, 'error');
			}
		}

		// back to main list
		$this->cancel();
	}

	/**
	 * Starts the incoming reservations.
	 *
	 * @return 	void
	 */
	public function startincoming()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// already set redirect to dashboard
		$this->setRedirect('index.php?option=com_vikrestaurants');

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
			return false;
		}

		if (!$user->authorise('core.access.reservations', 'com_vikrestaurants') || !$user->authorise('core.edit.state', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to send SMS notifications
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		// restart incoming reservation
		VREFactory::getConfig()->set('stopuntil', -1);
		return true;
	}

	/**
	 * Stops the incoming reservations.
	 *
	 * @return 	void
	 */
	public function stopincoming()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// already set redirect to dashboard
		$this->setRedirect('index.php?option=com_vikrestaurants');

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
			return false;
		}

		if (!$user->authorise('core.access.reservations', 'com_vikrestaurants') || !$user->authorise('core.edit.state', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to send SMS notifications
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		// fetch limit
		$date  = getdate(VikRestaurants::now());
		$until = mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']);

		// restart incoming reservation
		VREFactory::getConfig()->set('stopuntil', $until);
		return true;
	}

	/**
	 * AJAX end-point used to load the management form of a cart product.
	 * 
	 * @return  void
	 * 
	 * @since   1.9
	 */
	public function itemformajax()
	{
		$app = JFactory::getApplication();

		// the primary key of the cart item
		$id = $app->input->getUint('id', 0);
		// the ID of the product
		$id_product = $app->input->getUint('id_product', 0);

		/** @var object */
		$item = $this->getModel('resprod')->getItem($id, $blank = true);

		if (!$item->id_product)
		{
			$item->id_product = (int) $id_product;
		}

		/** @var object */
		$product = $this->getModel('menusproduct')->getItem($item->id_product, $blank = true);

		if (!$item->id)
		{
			$item->price = $product->price;

			if ($product->options)
			{
				// auto-select the first available option
				$item->id_product_option = $product->options[0]->id;

				// increase default price by the variation price
				$item->price += $product->options[0]->inc_price;
			}
		}

		// render layout
		$html = JLayoutHelper::render('cart.itemform.restaurant', [
			'product' => $product,
			'item'    => $item,
		]);

		// send form to caller
		$this->sendJSON(json_encode($html));
	}

	/**
	 * AJAX end-point used to reset the PIN of a reservation.
	 * 
	 * @return  void
	 */
	public function resetpinajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken())
		{
			// raise AJAX error, missing token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// the primary key of the cart item
		$id = $app->input->getUint('id', 0);
		
		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants')
			|| !$user->authorise('core.access.reservations', 'com_vikrestaurants')
			|| !$id)
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$model = $this->getModel();

		// refresh the pin code and reset the number of failures
		$pin = $model->resetPin($id);

		if ($pin === false)
		{
			// fetch error from model
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Error', 500);
			}

			// raise AJAX error, failed to update
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode() ?: 500, $error->getMessage());
		}

		// return new PIN to caller
		$this->sendJSON(json_encode($pin));
	}
}
