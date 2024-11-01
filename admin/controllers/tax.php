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
 * VikRestaurants tax controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerTax extends VREControllerAdmin
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
		$app->setUserState('vre.tax.data', []);

		// check if we should use a blank template
		$blank = $app->input->get('tmpl') === 'component';

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.taxes', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$url = 'index.php?option=com_vikrestaurants&view=managetax';

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
	 * @return 	boolean
	 */
	public function edit()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// unset user state for being recovered again
		$app->setUserState('vre.tax.data', []);

		// check if we should use a blank template
		$blank = $app->input->get('tmpl') === 'component';

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.taxes', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$url = 'index.php?option=com_vikrestaurants&view=managetax&cid[]=' . $cid[0];

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
			$this->setRedirect('index.php?option=com_vikrestaurants&task=tax.add');
		}
	}

	/**
	 * Task used to save the record data as a copy of the current item.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	void
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
	 * @param 	boolean  $copy  True to save the record as a copy.
	 *
	 * @return 	boolean
	 */
	public function save($copy = false)
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
		$args['name']        = $input->getString('name', '');
		$args['description'] = $input->getString('description');
		$args['rules']       = $input->get('rule_json', [], 'array');
		$args['id']          = $input->getUint('id', 0);

		// also register the deleted rules to properly recover them in case of failure
		$args['deleted_rules'] = $input->get('rule_deleted', [], 'uint');

		if ($copy)
		{
			// do not delete in case of copy
			$args['deleted_rules'] = [];

			// unset ID to create a copy
			$args['id'] = 0;
		}

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check if we should use a blank template
		$blank = $app->input->get('tmpl') === 'component';

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.taxes', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get tax model
		$tax = $this->getModel();

		// try to save arguments
		$id = $tax->save($args);

		if (!$id)
		{
			// get string error
			$error = $tax->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managetax';

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

		// get tax rule model
		$this->getModel('taxrule')->delete($args['deleted_rules']);

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		$url = 'index.php?option=com_vikrestaurants&task=tax.edit&cid[]=' . $id;

		if ($blank)
		{
			$url .= '&tmpl=component';
		}

		// redirect to edit page
		$this->setRedirect($url);

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.taxes', 'com_vikrestaurants'))
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
		$this->setRedirect('index.php?option=com_vikrestaurants&view=taxes');
	}

	/**
	 * AJAX end-point used to test how the taxes are applied.
	 * The task expects the following arguments to be set in request.
	 *
	 * @param 	integer  id_tax  The tax ID.
	 * @param 	float    amount  The base amount.
	 *
	 * @return 	void
	 */
	function testajax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		$id_tax  = $input->getUint('id_tax', 0);
		$amount  = $input->getFloat('amount', 0);
		$id_user = $input->getUint('id_user', 0);
		$langtag = $input->getString('langtag', null);
		$subject = $input->getString('subject', null);

		// store the last search in the user state
		$app->setUserState('vretaxestest.id_tax', $id_tax);
		$app->setUserState('vretaxestest.amount', $amount);
		$app->setUserState('vretaxestest.langtag', $langtag);

		$options = [];
		$options['lang']    = $langtag;
		$options['subject'] = $subject;

		if ($id_user)
		{
			$options['id_user'] = $id_user;
		}

		// calculate taxes
		$result = E4J\VikRestaurants\Taxing\TaxesFactory::calculate($id_tax, $amount, $options);
		
		// send result to caller
		$this->sendJSON($result);
	}
}
