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
 * VikRestaurants payment controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerPayment extends VREControllerAdmin
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

		$file = $app->input->getString('file', '');

		if ($file)
		{
			$data['file'] = $file;
		}

		// unset user state for being recovered again
		$app->setUserState('vre.payment.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.payments', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managepayment');

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
		$app->setUserState('vre.payment.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.payments', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managepayment&cid[]=' . $cid[0]);

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

			$url = 'index.php?option=com_vikrestaurants&task=payment.add';

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
		$args['name']         = $input->getString('name');
		$args['file']         = $input->getString('file');
		$args['published']    = $input->getUint('published', 0);
		$args['enablecost']   = $input->getInt('enablecost_factor') * abs($input->getFloat('enablecost_amount'));
		$args['trust']        = $input->getUint('trust', 0);
		$args['charge']       = $input->getFloat('charge');
		$args['percentot']    = $input->getUint('percentot', 0);
		$args['id_tax']       = $input->getUint('id_tax', 0);
		$args['setconfirmed'] = $input->getUint('setconfirmed', 0);
		$args['selfconfirm']  = $input->getUint('selfconfirm', 0);
		$args['icontype']     = $input->getUint('icontype', 0);
		$args['position']     = $input->getString('position', '');
		$args['prenote']      = JComponentHelper::filterText($input->getRaw('prenote', ''));
		$args['note']         = JComponentHelper::filterText($input->getRaw('note', ''));
		$args['group']        = $input->getUint('group', 0);
		$args['id']           = $input->getInt('id', 0);

		switch ($args['icontype'])
		{
			case 1:
				$args['icon'] = $input->getString('font_icon', '');
				break;

			case 2:
				$args['icon'] = $input->getString('upload_icon', '');
				break;

			default:
				$args['icon'] = '';
		}

		if ($args['selfconfirm'])
		{
			// always unset auto-confirmation in case of self-confirmation
			// in order to avoid backward compatibility issues
			$args['setconfirmed'] = 0;
		}

		if ($copy)
		{
			// unset ID to create a copy
			$args['id'] = 0;
		}

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.payments', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		try
		{
			// get payment configuration
			$config = VREFactory::getPlatform()->getPaymentFactory()->getConfigurationForm($args['file']);

			$args['params'] = [];

			// load configuration from request
			foreach ($config as $k => $p)
			{
				$args['params'][$k] = $input->get('gp_' . $k, '', 'string');
			}
		}
		catch (Exception $e)
		{
			// unset file to raise error before saving the payment
			$args['file'] = false;
		}

		// get payment model
		$payment = $this->getModel();

		// try to save arguments
		$id = $payment->save($args);

		if (!$id)
		{
			// get string error
			$error = $payment->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managepayment';

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
		$this->setRedirect('index.php?option=com_vikrestaurants&task=payment.edit&cid[]=' . $id);

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.payments', 'com_vikrestaurants'))
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
	 * Changes the state of the selected records.
	 *
	 * @return 	boolean
	 */
	public function publish()
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

		$cid  = $app->input->get('cid', [], 'uint');
		$task = $app->input->get('task', null);

		$state = $task == 'unpublish' ? 0 : 1;

		// check user permissions
		if (!$user->authorise('core.edit.state', 'com_vikrestaurants') || !$user->authorise('core.access.payments', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// change state of selected records
		$this->getModel()->publish($cid, $state);

		// back to records list
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
		$this->setRedirect('index.php?option=com_vikrestaurants&view=payments');
	}

	/**
	 * AJAX end-point used to retrieve the configuration
	 * of the selected driver.
	 *
	 * @return 	void
	 */
	public function driverfields()
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
		
		$driver = $app->input->getString('driver');
		$id     = $app->input->getUint('id', 0);
		
		// access payment config through platform handler
		$form = VREFactory::getPlatform()->getPaymentFactory()->getConfigurationForm($driver);
		
		$params = [];

		if ($id)
		{
			// load payment details
			$payment = $this->getModel()->getItem($id);

			if ($payment)
			{
				// use found parameters
				$params = $payment->params;
			}
		}
		
		// build display data
		$data = [
			'fields' => $form,
			'params' => $params,
			'prefix' => 'gp_',
		];

		// render payment form
		$html = JLayoutHelper::render('form.fields', $data);
		
		// send HTML form to caller
		$this->sendJSON(json_encode($html));
	}
}
