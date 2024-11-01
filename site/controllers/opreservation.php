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
 * VikRestaurants operator reservation controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerOpreservation extends VREControllerAdmin
{
	/**
	 * Task used to access the creation page of a new record.
	 *
	 * @return 	boolean
	 */
	public function add()
	{
		$app = JFactory::getApplication();

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

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}

		$url = 'index.php?option=com_vikrestaurants&view=opmanageres';

		$from = $app->input->get('from');

		if ($from)
		{
			$url .= '&from=' . $from;
		}

		$itemid = $app->input->get('Itemid', 0, 'uint');

		if ($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}

		$this->setRedirect(JRoute::rewrite($url, false));

		return true;
	}

	/**
	 * Task used to access the management page of an existing record.
	 *
	 * @return 	boolean
	 */
	public function edit()
	{
		$app = JFactory::getApplication();

		// unset user state for being recovered again
		$app->setUserState('vre.reservation.data', []);

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}

		$cid = $app->input->getUint('cid', array(0));

		$url = 'index.php?option=com_vikrestaurants&view=opmanageres&cid[]=' . $cid[0];

		$from = $app->input->get('from');

		if ($from)
		{
			$url .= '&from=' . $from;
		}

		$itemid = $app->input->get('Itemid', 0, 'uint');

		if ($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}

		$this->setRedirect(JRoute::rewrite($url, false));

		return true;
	}

	/**
	 * Task used to access the management page of an existing record.
	 *
	 * @return 	boolean
	 */
	public function editbill()
	{
		$app = JFactory::getApplication();

		// unset user state for being recovered again
		$app->setUserState('vre.bill.data', []);

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}

		$cid = $app->input->getUint('cid', []);

		if (empty($cid))
		{
			// try to recover ID from a different var
			$cid = [$app->input->getUint('id', 0)];
		}

		$url = 'index.php?option=com_vikrestaurants&view=opeditbill&cid[]=' . $cid[0];

		$from = $app->input->get('bill_from');

		if ($from)
		{
			// use direct redirect
			$url .= '&bill_from=' . $from;
		}
		else
		{
			// fallback to default redirect
			$from = $app->input->get('from');

			if ($from)
			{
				$url .= '&from=' . $from;
			}
		}

		$itemid = $app->input->get('Itemid', 0, 'uint');

		if ($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}

		$this->setRedirect(JRoute::rewrite($url, false));

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

			$itemid = $app->input->get('Itemid', 0, 'uint');

			$url = 'index.php?option=com_vikrestaurants&task=opreservation.add' . ($itemid ? '&Itemid=' . $itemid : '');

			$from = $app->input->get('bill_from');

			if ($from)
			{
				// use direct redirect
				$url .= '&bill_from=' . $from;
			}
			else
			{
				// fallback to default redirect
				$from = $app->input->get('from');

				if ($from)
				{
					$url .= '&from=' . $from;
				}
			}

			$this->setRedirect(JRoute::rewrite($url, false));
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

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}
		
		$args = [];
		$args['date']        = $app->input->getString('date', '');
		$args['hourmin']     = $app->input->getString('hourmin', '');
		$args['id_table']    = $app->input->getString('id_table', '');
		$args['people']      = $app->input->getUint('people', 0);
		$args['status']      = $app->input->getString('status', '');
		$args['rescode']     = $app->input->getUint('rescode', 0);
		$args['stay_time']   = $app->input->getUint('stay_time', 0);
		$args['id_operator'] = $app->input->getUint('id_operator', 0);
		$args['id_user']     = $app->input->getUint('id_user', 0);
		$args['id']          = $app->input->getInt('id', 0);

		if ($args['id'])
		{
			// make sure the operator is allowed to edit the reservation details
			if (!$operator->canSeeAll() && !$operator->canAssign($args['id']))
			{
				// reservation already assigned to someone else
				$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
				$this->cancel();

				return false;
			}
		}

		if ($args['stay_time'] == VREFactory::getConfig()->getUint('averagetimestay'))
		{
			// unset stay time in case it is equals to the default amount
			unset($args['stay_time']);
		}

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

		/**
		 * Search customer according to the provided details and try to auto-assign the
		 * reservation to the matching customer. Do not proceed in case the reservation
		 * has been already assigned to an existing user.
		 * 
		 * @since 1.9
		 */
		if ($args['id_user'] <= 0)
		{
			$args['id_user'] = $this->getModel('customer')->hasCustomer($args['fields_data']);
		}

		$itemid   = $app->input->get('Itemid', 0, 'uint');
		$billfrom = $app->input->get('bill_from');
		$from     = $app->input->get('from');

		// get reservation model
		$reservation = $this->getModel('reservation');

		// try to save arguments
		$id = $reservation->save($args);

		if (!$id)
		{
			// get string error
			$error = $reservation->getError(null, true);
			$error = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error);

			$app->enqueueMessage($error, 'error');

			$url = 'index.php?option=com_vikrestaurants&view=opmanageres' . ($itemid ? '&Itemid=' . $itemid : '');

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}

			if ($billfrom)
			{
				$url .= '&bill_from=' . $billfrom;
			}
			else if ($from)
			{
				$url .= '&from=' . $from;
			}

			// redirect to new/edit page
			$this->setRedirect(JRoute::rewrite($url, false));
				
			return false;
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// check if the reservation code has changed
		if ($args['rescode'] && $args['rescode'] != $app->input->get('prevrescode', 0, 'uint'))
		{
			$reservation->changeCode($id, $args['rescode']);
		}

		// check if we should send a notification e-mail to the customer
		if ($app->input->getBool('notifycust'))
		{
			// send e-mail notification to customer
			$reservation->sendEmailNotification($id);
		}

		$url = 'index.php?option=com_vikrestaurants&task=opreservation.edit&cid[]=' . $id . ($itemid ? '&Itemid=' . $itemid : '');

		if ($billfrom)
		{
			$url .= '&bill_from=' . $billfrom;
		}
		else if ($from)
		{
			$url .= '&from=' . $from;
		}

		// redirect to edit page
		$this->setRedirect(JRoute::rewrite($url, false));

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @param 	string  $view  The return view.
	 *
	 * @return 	void
	 */
	public function cancel($view = null)
	{
		$app = JFactory::getApplication();

		$itemid = $app->input->get('Itemid', 0, 'uint');

		$url = 'index.php?option=com_vikrestaurants' . ($itemid ? '&Itemid=' . $itemid : '');

		if (is_null($view))
		{
			$from = $app->input->get('from', null);

			$url .= '&view=' . ($from ? $from : 'opreservations');
		}
		else
		{
			$url .= '&view=' . $view;
		}

		$this->setRedirect(JRoute::rewrite($url, false));
	}

	/**
	 * AJAX end-point to obtain a JSON list of available tables for
	 * the specified search arguments.
	 *
	 * @return 	void
	 * 
	 * @since   1.9
	 */
	public function availabletablesajax()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

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
	 * AJAX end-point used to search the products.
	 *
	 * @return 	void
	 */
	public function searchproductajax()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// filter products by the given term
		$query    = $app->input->getString('term');
		$products = $this->getModel('opbill')->searchProducts($query);
		
		// return products to caller
		$this->sendJSON($products);
	}

	/**
	 * AJAX end-point used to return a list of sections
	 * that belong to the specified menu.
	 *
	 * @return 	void
	 */
	public function menusectionsajax()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// fetch all the sections of the provided menu
		$idMenu   = $app->input->getUint('id_menu', 0);
		$sections = $this->getModel('opbill')->getMenuSections($idMenu);

		$this->sendJSON($sections);
	}

	/**
	 * AJAX end-point used to return a list of products
	 * that belong to the specified section.
	 *
	 * @return 	void
	 */
	public function sectionproductsajax()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// fetch all the products of the provided section
		$idSection = $app->input->getUint('id_section', 0);
		$products  = $this->getModel('opbill')->getSectionProducts($idSection);

		$this->sendJSON($products);
	}

	/**
	 * Returns the HTML used to insert a product within the bill.
	 *
	 * @return 	void
	 */
	public function getproducthtml()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$idProduct = $app->input->getUint('id_product', 0);
		$idAssoc   = $app->input->getUint('id_assoc', 0);

		if ($idProduct > 0)
		{
			// fetch item details
			$item = $this->getModel('opbill')->getCartItem($idProduct, $idAssoc);
		}
		else
		{
			// create a new product
			$item = null;
		}

		/**
		 * Generate item form with a layout.
		 *
		 * @since 1.8
		 */
		$html = JLayoutHelper::render('oversight.billitem', [
			'item'       => $item,
			'id_assoc'   => $idAssoc,
			'id_product' => $idProduct,
		]);

		$this->sendJSON(json_encode($html));
	}

	/**
	 * AJAX end-point used to add an item to the bill.
	 *
	 * @return 	void
	 */
	public function additemajax()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$args = [];
		$args['id_reservation']    = $app->input->get('id', 0, 'uint');
		$args['id_product']        = $app->input->get('id_entry', 0, 'uint');
		$args['id_product_option'] = $app->input->get('id_option', 0, 'uint');
		$args['name']              = $app->input->get('name', null, 'string');
		$args['price']             = $app->input->get('price', null, 'float');
		$args['quantity']          = $app->input->get('quantity', 1, 'uint');
		$args['servingnumber']     = $app->input->get('serving_number', 0, 'uint');
		$args['notes']             = $app->input->get('notes', '', 'string');
		$args['id']                = $app->input->get('item_index', 0, 'uint');

		// make sure the operator is allowed to edit the reservation details
		if (!$operator->canSeeAll() && !$operator->canAssign($args['id']))
		{
			// the reservation is assigned to someone else
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// fetch the ID of the section to which the product belongs
		$idSection = $app->input->getUint('id_section');

		$model = $this->getModel('opbill');

		// try to add the item to the bill
		$response = $model->addItem($args, $idSection);

		if (!$response)
		{
			// an error has occurred, recover it from the model
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Error', 500);
			}

			// send error to the client
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		// send response to the caller
		$this->sendJSON($response);	
	}

	/**
	 * AJAX end-point used to add an item to the bill.
	 *
	 * @return 	void
	 */
	public function removeitemajax()
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

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin() || !$operator->isRestaurantAllowed())
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$id_assoc = $app->input->get('id_assoc', 0, 'uint');
		$id_res   = $app->input->get('id_res', 0, 'uint');

		// make sure the operator is allowed to edit the reservation details
		if (!$operator->canSeeAll() && !$operator->canAssign($id_res))
		{
			// the reservation is assigned to someone else
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$model = $this->getModel('opbill');

		// try to add the item to the bill
		$response = $model->removeItem($id_res, $id_assoc);

		if (!$response)
		{
			// an error has occurred, recover it from the model
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Error', 500);
			}

			// send error to the client
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		// send response to the caller
		$this->sendJSON($response);	
	}
}
