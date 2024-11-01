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
 * VikRestaurants take-away order controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerTkreservation extends VREControllerAdmin
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

		// unset user state for being recovered again
		$app->setUserState('vre.tkreservation.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// recover incoming view
		$from = $app->input->get('from');

		$url = 'index.php?option=com_vikrestaurants&view=managetkreservation';

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
		$app->setUserState('vre.tkreservation.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		// recover incoming view
		$from = $app->input->get('from');

		$url = 'index.php?option=com_vikrestaurants&view=managetkreservation&cid[]=' . $cid[0];

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

			$url = 'index.php?option=com_vikrestaurants&task=tkreservation.add';

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
		
		$args = array();
		$args['date']                 = $input->getString('date', '');
		$args['hourmin']              = $input->getString('hourmin', '');
		$args['service']              = $input->getString('service', 'delivery');
		$args['id_user']              = $input->getUint('id_user', 0);
		$args['purchaser_nominative'] = $input->getString('purchaser_nominative', '');
		$args['purchaser_mail']       = $input->getString('purchaser_mail', '');
		$args['purchaser_phone']      = $input->getString('purchaser_phone', '');
		$args['purchaser_prefix']     = $input->getString('purchaser_prefix', '');
		$args['purchaser_country']    = $input->getString('purchaser_country', '');
		// always let the address is fully recovered from the custom fields
		// because the purchaser address string might not contain "address_2"
		// $args['purchaser_address']    = $input->getString('id_useraddr', '');
		$args['notifycust']           = $input->getBool('notifycust', false);
		$args['total_to_pay']         = $input->getFloat('total_to_pay', 0.0);
		$args['total_net']            = $input->getFloat('total_net', 0.0);
		$args['total_tax']            = $input->getFloat('total_tax', 0.0);
		$args['status']               = $input->getString('status', '');
		$args['id_payment']           = $input->getUint('id_payment', 0);
		$args['route']                = $input->get('route', [], 'array');
		$args['notes']                = JComponentHelper::filterText($input->getRaw('notes', ''));
		$args['id']                   = $input->getUint('id', 0);

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

		// get service charge actions
		$args['update_service_charge'] = [];

		if ($updateServiceCharge = $input->get('update_service_charge', [], 'array'))
		{
			for ($i = 0; $i < count($updateServiceCharge['value']); $i++) {
				$args['update_service_charge'][] = [
					'value'     => $updateServiceCharge['value'][$i],
					'percentot' => $updateServiceCharge['percentot'][$i],
				];
			}
		}

		/**
		 * Retrieve custom fields by using the related helper.
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter)
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

		/** @var E4J\VikRestaurants\CustomFields\FieldService[] */
		$services = E4J\VikRestaurants\CustomFields\Factory::getSupportedServices($objects = true);

		if (isset($services[$args['service']]))
		{
			// use the provided service
			$service = $services[$args['service']];
		}
		else
		{
			// service not supported
			$service = null;
		}

		// create requestor for the take-away custom fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor($customFields, $service);

		// load custom fields
		$args['custom_f'] = $requestor->loadForm($tmp, $strict = false);

		// register data fetched by the custom fields so that the reservation
		// model is able to use them for saving purposes
		$args['fields_data'] = $tmp;

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// recover incoming view
		$from = $input->get('from');

		// get take-away order model
		$order = $this->getModel();

		// try to save arguments
		$id = $order->save($args);

		if (!$id)
		{
			// get string error
			$error = $order->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managetkreservation';

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

		$url = 'index.php?option=com_vikrestaurants&task=tkreservation.edit&cid[]=' . $id;

		if ($from)
		{
			$url .= '&from=' . $from;
		}

		// redirect to edit page
		$this->setRedirect($url);

		// check if we have any registered errors, which may refer to a failure
		// of the e-mail notification
		$error = $order->getError(null, true);

		if ($error)
		{
			// display as warning message
			$app->enqueueMessage($error, 'warning');
		}

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
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

		$this->setRedirect('index.php?option=com_vikrestaurants&view=' . ($from ? $from : 'tkreservations'));
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
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
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
		$rescode = JHtml::fetch('vikrestaurants.rescode', $id_rescode, $group = 2);

		// send response to caller
		$this->sendJSON($rescode);
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

		/** @var JModelLegacy */
		$orderItemModel = $this->getModel('tkresprod');

		/** @var object */
		$item = $orderItemModel->getItem($id, $blank = true);

		if (!$item->id_product)
		{
			$item->id_product = (int) $id_product;
		}

		$item->toppings = [];

		if ($item->id)
		{
			foreach ($orderItemModel->getToppings($item->id) as $group)
			{
				$item->toppings[$group->id] = $group->toppings;
			}
		}

		/** @var JModelLegacy */
		$itemModel = $this->getModel('tkentry');

		/** @var object */
		$product = $itemModel->getItem($item->id_product);

		if (!$product)
		{
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(404, 'Item [' . $id_product . '] not found.');
		}

		// fetch product variations and toppings
		$product->options  = $itemModel->getVariations($item->id_product);
		$product->toppings = $itemModel->getToppingsGroups($item->id_product);

		// recover stocks information
		$product = $itemModel->getStocks($product);

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

			// iterate topping groups
			foreach ($product->toppings as $group)
			{
				// check if the group has toppings, requires a single selection and
				// it is suitable for any variation or for the selected one
				if (!$group->multiple && $group->toppings && ($group->id_variation == 0 || $group->id_variation == $item->id_product_option))
				{
					// mark first topping as selected ("1" is the quantity)
					$item->toppings[$group->id] = [$group->toppings[0]->id => 1];

					// increase default price by the first available topping
					$item->price += $group->toppings[0]->rate;
				}
			}
		}

		// render layout
		$html = JLayoutHelper::render('cart.itemform.takeaway', [
			'product' => $product,
			'item'    => $item,
		]);

		// send form to caller
		$this->sendJSON(json_encode($html));
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
		if (!$data['id'] || !$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
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
	 * AJAX end-point used to confirm a take-away order.
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
			|| !$user->authorise('core.access.tkorders', 'com_vikrestaurants')
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
				'status'     => JHtml::fetch('vrehtml.status.confirmed', 'takeaway', 'code'),
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
	 * AJAX end-point used to decline a take-away order.
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
			|| !$user->authorise('core.access.tkorders', 'com_vikrestaurants')
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
			$status = JHtml::fetch('vrehtml.status.rejected', 'takeaway', 'code');
		}
		catch (Exception $e)
		{
			// rejected status code not supported, fallback to removed status
			$status = JHtml::fetch('vrehtml.status.removed', 'takeaway', 'code');	
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
	 * AJAX end-point used to notify a take-away order.
	 *
	 * @return 	void
	 */
	public function notifyajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$ids = $app->input->get('cid', [], 'uint');

		// filter empty IDs to avoid inserting them
		$ids = array_filter($ids);

		// check user permissions (abort in case the order ID is missing)
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants')
			|| !$user->authorise('core.access.tkorders', 'com_vikrestaurants')
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
	 * Sends a notification SMS to the customer of the specified take-away order.
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
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
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
	 * Starts the incoming take-away orders.
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

		if (!$user->authorise('core.access.tkorders', 'com_vikrestaurants') || !$user->authorise('core.edit.state', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to send SMS notifications
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		// restart incoming reservation
		VREFactory::getConfig()->set('tkstopuntil', -1);
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

		if (!$user->authorise('core.access.tkorders', 'com_vikrestaurants') || !$user->authorise('core.edit.state', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to send SMS notifications
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		// fetch limit
		$date  = getdate(VikRestaurants::now());
		$until = mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']);

		// restart incoming reservation
		VREFactory::getConfig()->set('tkstopuntil', $until);
		return true;
	}

	/**
	 * AJAX end-point used to assign an operator to the reservation.
	 *
	 * @return 	void
	 * 
	 * @since   1.8.2
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
	 * AJAX end-point used to increase/decrease an availability override
	 * for the specified time slot.
	 *
	 * @return 	void
	 *
	 * @since 	1.8.3
	 */
	public function increasetimeslotajax()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$args = [];
		$args['date']    = $app->input->get('date', '', 'string');
		$args['hourmin'] = $app->input->get('hourmin', '', 'string');
		$args['units']   = $app->input->get('units', 0, 'int');

		// check user permissions
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.tkorders'))
		{
			// raise AJAX error, not authorised to edit records state
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get availability table
		$override = $this->getModel('tkavail');

		// update override
		if (!$override->save($args))
		{
			// get string error
			$error = $override->getError();

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			// raise returned error while saving the record
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		$this->sendJSON(1);
	}
}
