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
 * VikRestaurants customer controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerCustomer extends VREControllerAdmin
{
	/**
	 * Task used to access the creation page of a new record.
	 *
	 * @return  boolean
	 */
	public function add()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// unset user state for being recovered again
		$app->setUserState('vre.customer.data', []);

		// check if we should use a blank template
		$blank = $app->input->get('tmpl') === 'component';

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.customers', 'com_vikrestaurants'))
		{
			if ($blank)
			{
				// throw exception in order to avoid unexpected behaviors
				throw new Exception(JText::translate('JERROR_ALERTNOAUTHOR'), '403');
			}

			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$url = 'index.php?option=com_vikrestaurants&view=managecustomer';

		if ($blank)
		{
			$url .= '&tmpl=component';
		}

		$this->setRedirect($url);

		return true;
	}

	/**
	 * Task used to access the management page of an existing record.
	 *
	 * @return  boolean
	 */
	public function edit()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// unset user state for being recovered again
		$app->setUserState('vre.customer.data', []);

		// check if we should use a blank template
		$blank = $app->input->get('tmpl') === 'component';

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.customers', 'com_vikrestaurants'))
		{
			if ($blank)
			{
				// throw exception in order to avoid unexpected behaviors
				throw new Exception(JText::translate('JERROR_ALERTNOAUTHOR'), '403');
			}

			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$url = 'index.php?option=com_vikrestaurants&view=managecustomer&cid[]=' . $cid[0];

		if ($blank)
		{
			$url .= '&tmpl=component';
		}

		$this->setRedirect($url);

		return true;
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the main list.
	 *
	 * @return  void
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
	 * @return  void
	 */
	public function savenew()
	{
		if ($this->save())
		{
			$this->setRedirect('index.php?option=com_vikrestaurants&task=customer.add');
		}
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return  boolean
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
		$args['jid']               = $input->get('jid', 0, 'int');
		$args['billing_name']      = $input->get('billing_name', '', 'string');
		$args['billing_mail']      = $input->get('billing_mail', '', 'string');
		$args['billing_phone']     = $input->get('billing_phone', '', 'string');
		$args['country_code']      = $input->get('country_code', '', 'string');
		$args['billing_state']     = $input->get('billing_state', '', 'string');
		$args['billing_city']      = $input->get('billing_city', '', 'string');
		$args['billing_address']   = $input->get('billing_address', '', 'string');
		$args['billing_address_2'] = $input->get('billing_address_2', '', 'string');
		$args['billing_zip']       = $input->get('billing_zip', '', 'string');
		$args['latitude']          = $input->get('billing_lat', null, 'float');
		$args['longitude']         = $input->get('billing_lng', null, 'float');
		$args['company']           = $input->get('company', '', 'string');
		$args['vatnum']            = $input->get('vatnum', '', 'string');
		$args['ssn']               = $input->get('ssn', '', 'string');
		$args['notes']             = $input->get('notes', '', 'string');
		$args['image']             = $input->get('image', '', 'string');
		$args['id']                = $input->get('id', 0, 'int');

		// fill user fields only if we need to create them
		if ($input->getBool('create_new_user'))
		{
			// user fields
			$args['user'] = array();
			$args['user']['username'] = $input->get('username', '', 'string');
			$args['user']['usermail'] = $input->get('usermail', '', 'string');
			$args['user']['password'] = $input->get('password', '', 'string');
			$args['user']['confirm']  = $input->get('confpassword', '', 'string');
		}

		// check if we should automatically create a delivery location according with
		// the provided billing information
		$args['delivery_as_billing'] = $input->getBool('delivery_as_billing', false);

		// fetch all the delivery locations
		$args['locations'] = $input->get('location_json', [], 'array');

		// also register the deleted locations to properly recover them in case of failure
		$args['deleted_locations'] = $input->get('location_deleted', [], 'uint');

		/**
		 * Retrieve custom fields by using the related helper.
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

		// create requestor for the restaurant custom fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor(
			$customFields->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter)
		);

		// load fields
		$args['fields'] = $requestor->loadForm($tmp, $strict = false);

		// create requestor for the take-away custom fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor(
			$customFields->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter)
		);

		// load fields
		$args['tkfields'] = $requestor->loadForm($tmp, $strict = false);

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check if we should use a blank template
		$blank = $app->input->get('tmpl') === 'component';

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.customers', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get customer model
		$customer = $this->getModel();

		// try to save arguments
		$id = $customer->save($args);

		if (!$id)
		{
			// get string error
			$error = $customer->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managecustomer';

			if ($args['id'])
			{
				$url .= '&cid[]=' . $args['id'];
			}

			if ($blank)
			{
				$url .= '&tmpl=component';
			}

			// redirect to new/edit page
			$this->setRedirect($url);
				
			return false;
		}

		$this->getModel('userlocation')->delete($args['deleted_locations']);

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		$url = 'index.php?option=com_vikrestaurants&task=customer.edit&cid[]=' . $id;

		if ($blank)
		{
			// keep blank template when returning to edit page
			$url .= '&tmpl=component';
		}

		// redirect to edit page
		$this->setRedirect($url);

		return true;
	}

	/**
	 * AJAX end-point used to auto-save the customer notes.
	 *
	 * @return 	void
	 */
	public function savenotesajax()
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
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$args = [];
		$args['notes'] = $app->input->getString('notes', '');
		$args['id']    = $app->input->getUint('id', 0);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.customers', 'com_vikrestaurants') || !$args['id'])
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get customer model
		$customer = $this->getModel();

		// try to save arguments
		$id = $customer->save($args);

		if (!$id)
		{
			// get string error
			$error = $customer->getError(null, true);
			
			// raise returned error while saving the record
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, $error);
		}

		// notes saved
		$app->close();
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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.customers', 'com_vikrestaurants'))
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
	 * @return  void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=customers');
	}

	/**
	 * Sends a custom SMS to the specified customer.
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
		
		$cid = $input->get('cid', [], 'uint');

		// check user permissions
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.customers', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to send SMS notifications
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		try
		{
			// get current SMS instance
			$smsapi = VREApplication::getInstance()->getSmsInstance();
		}
		catch (Exception $e)
		{
			// back to main list, SMS API not configured
			$app->enqueueMessage(JText::translate('VRSMSESTIMATEERR1'), 'error');
			$this->cancel();

			return false;
		}

		// load message from request
		$message = $input->get('sms_message', '', 'string');

		// make sure the message is not empty
		if (!$message)
		{
			// missing contents, back to main list
			$this->cancel();

			return false;
		}

		$notified = 0;
		$errors   = [];

		foreach ($cid as $id)
		{
			// get customer details
			$customer = VikRestaurants::getCustomer($id);

			if ($customer && $customer->billing_phone)
			{
				// send message
				$response = $smsapi->sendMessage($customer->billing_phone, $message);

				// validate response
				if ($smsapi->validateResponse($response))
				{
					// successful notification
					$notified++;
				}
				else
				{
					// unable to send the notification, register error message
					$errors[] = $smsapi->getLog();
				}
			}
		}

		// update default message if needed
		if ($input->getBool('sms_keep_def'))
		{
			// alter configuration
			VREFactory::getConfig()->set('smstextcust', $message);
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
	 * AJAX end-point to obtain a list of users belonging
	 * to the current platform (CMS).
	 *
	 * @return 	void
	 * 
	 * @since   1.9  Moved from main controller.
	 */
	public function jusers()
	{	
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$search = $app->input->getString('term', '');
		$id 	= $app->input->getUint('id');

		// get customers model
		$model = $this->getModel();

		// search CMS users
		$users = $model->searchUsers($search, $id);

		// send users to caller
		$this->sendJSON($users);
	}

	/**
	 * AJAX end-point to obtain a list of customers.
	 *
	 * @return 	void
	 * 
	 * @since   1.9  Moved from main controller.
	 */
	public function users()
	{	
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$search = $app->input->getString('term', '');

		// get customers model
		$model = $this->getModel();

		// search customers
		$customers = $model->search($search);

		// send users to caller
		$this->sendJSON($customers);
	}
}
