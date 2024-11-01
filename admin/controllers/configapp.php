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
 * VikRestaurants applications configuration controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerConfigapp extends VREControllerAdmin
{
	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	boolean
	 */
	public function save()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

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

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// back to dashboard, not authorised to access the configuration
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect('index.php?option=com_vikrestaurants');

			return false;
		}
		
		$args = array();

		////////////////////////////////////////////////////
		/////////////////////// API ////////////////////////
		////////////////////////////////////////////////////

		// SETTINGS

		$args['apifw']       = $app->input->get('apifw', 0, 'uint');
		$args['apilogmode']  = $app->input->get('apilogmode', 0, 'uint');
		$args['apilogflush'] = $app->input->get('apilogflush', 0, 'uint');
		$args['apimaxfail']  = $app->input->get('apimaxfail', 0, 'uint');

		////////////////////////////////////////////////////
		/////////////////// SMS PROVIDER ///////////////////
		////////////////////////////////////////////////////

		// SETTINGS

		// Global
		$args['smsapi']           = $app->input->get('smsapi', '', 'string');
		$args['smsapiwhen']       = $app->input->get('smsapiwhen', 0, 'uint');
		$args['smsapito']         = $app->input->get('smsapito', 0, 'uint');
		$args['smsapiadminphone'] = $app->input->get('smsapiadminphone', '', 'string');

		// Parameters
		
		try
		{
			$args['smsapifields'] = [];

			// get SMS driver configuration
			$smsconfig = VREApplication::getInstance()->getSmsConfig($args['smsapi']);

			foreach ($smsconfig as $k => $p)
			{
				$args['smsapifields'][$k] = $app->input->get('smsparam_' . $k, '', 'string');
			}
		}
		catch (Exception $e)
		{
			// SMS driver not supported
		}

		// TEMPLATES

		$args['smstmplcust']    = $app->input->get('smstmplcust', '', 'string');
		$args['smstmpladmin']   = $app->input->get('smstmpladmin', '', 'string');
		$args['smstmpltkcust']  = $app->input->get('smstmpltkcust', '', 'string');
		$args['smstmpltkadmin'] = $app->input->get('smstmpltkadmin', '', 'string');

		////////////////////////////////////////////////////
		//////////////////// CUSTOMIZER ////////////////////
		////////////////////////////////////////////////////

		$args['fields_layout_style'] = $app->input->get('fields_layout_style', '', 'string');

		////////////////////////////////////////////////////
		////////////////////// BACKUP //////////////////////
		////////////////////////////////////////////////////

		$args['backuptype']   = $app->input->getString('backuptype', 'full');
		$args['backupfolder'] = $app->input->getString('backupfolder', '');

		////////////////////////////////////////////////////

		// get configuration model
		$config = $this->getModel();

		// Save all configuration.
		// Do not care of any errors.
		$changed = $config->saveAll($args);

		// get customizer model
		$customizerModel = $this->getModel('customizer');

		// fetch customizer properties
		$customizer = $app->input->get('customizer', [], 'array');

		// save customizer
		$changed = $customizerModel->save($customizer) || $changed;

		// fetch CSS code
		$custom_css_code = $app->input->get('custom_css_code', '', 'raw');

		// save custom CSS code
		$changed = $customizerModel->setCustomCSS($custom_css_code) || $changed;

		if ($changed)
		{
			// display generic successful message
			$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));
		}

		// redirect to configuration page
		$this->cancel();

		return true;
	}

	/**
	 * Redirects the users to the applications configuration page.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=editconfigapp');
	}

	/**
	 * AJAX end-point to load the configuration fields
	 * of the requested SMS API driver.
	 *
	 * @return 	void
	 */
	public function smsfields()
	{	
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$driver = $app->input->getString('driver');
		
		try
		{
			// access driver config through platform handler
			$form = VREApplication::getInstance()->getSmsConfig($driver);
		}
		catch (Exception $e)
		{
			// raise AJAX error, driver not found
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(404, JText::translate('VRSMSESTIMATEERR1'));
		}
		
		$params = [];

		// retrieve SMS driver configuration
		$params = VREFactory::getConfig()->getArray('smsapifields', []);
		
		// build display data
		$data = [
			'fields' => $form,
			'params' => $params,
			'prefix' => 'smsparam_',
		];

		// render payment form
		$html = JLayoutHelper::render('form.fields', $data);
		
		// send JSON to caller
		$this->sendJSON(json_encode($html));
	}

	/**
	 * AJAX end-point to estimate the remaining balance of
	 * the current SMS driver.
	 *
	 * @return 	void
	 */
	public function smscredit()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$driver = $app->input->getString('driver');
		
		try
		{
			// access driver instance through platform handler
			$api = VREApplication::getInstance()->getSmsInstance($driver);
		}
		catch (Exception $e)
		{
			// raise AJAX error, driver not found
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(404, JText::translate('VRSMSESTIMATEERR1'));
		}
		
		$phone = $app->input->get('phone', '', 'string');

		if (empty($phone))
		{
			// use admin phone number
			$phone = VREFactory::getConfig()->get('smsapiadminphone');
		}
		
		// make sure the driver support an estimation feature
		if (!method_exists($api, 'estimate'))
		{
			// raise AJAX error, estimate not supported
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(405, JText::translate('VRSMSESTIMATEERR2'));
		}
		
		// try to estimate
		$result = $api->estimate($phone, 'Sample');
		
		if ($result->errorCode != 0)
		{
			// raise AJAX error, unable to estimate
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, JText::translate('VRSMSESTIMATEERR3'));
		}
		
		// return the plain user credit
		$this->sendJSON($result->userCredit);
	}
}
