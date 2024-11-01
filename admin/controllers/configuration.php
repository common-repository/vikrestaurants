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
 * VikRestaurants configuration controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerConfiguration extends VREControllerAdmin
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
		////////////////////// GLOBAL //////////////////////
		////////////////////////////////////////////////////

		// SYSTEM

		$args['restname']         = $app->input->get('restname', '', 'string');
		$args['companylogo']      = $app->input->get('companylogo', '', 'string');
		$args['enablerestaurant'] = $app->input->get('enablerestaurant', 0, 'uint');
		$args['enabletakeaway']   = $app->input->get('enabletakeaway', 0, 'uint');
		$args['multilanguage']    = $app->input->get('multilanguage', 0, 'uint');
		$args['showfooter']       = $app->input->get('showfooter', 0, 'uint');
		$args['refreshdash']      = $app->input->get('refreshdash', 0, 'uint');

		// date & time
		$args['dateformat']   = $app->input->get('dateformat', '', 'string');
		$args['timeformat']   = $app->input->get('timeformat', '', 'string');
		$args['opentimemode'] = $app->input->get('opentimemode', 0, 'uint');
		$args['hourfrom']     = $app->input->get('hourfrom', 0, 'uint');
		$args['hourto']       = $app->input->get('hourto', 0, 'uint');

		// booking
		$args['enablereg']   = $app->input->get('enablereg', 0, 'uint');
		$args['phoneprefix'] = $app->input->get('phoneprefix', 0, 'uint');

		// E-MAIL

		$args['adminemail']  = $app->input->get('adminemail', '', 'string');
		$args['senderemail'] = $app->input->get('senderemail', '', 'string');

		// GDPR

		$args['gdpr']       = $app->input->get('gdpr', 0, 'uint');
		$args['policylink'] = $app->input->get('policylink', '', 'string');

		// Google

		$args['googleapikey']        = $app->input->get('googleapikey', '', 'string');
		$args['googleapiplaces']     = $app->input->get('googleapiplaces', 0, 'uint');
		$args['googleapidirections'] = $app->input->get('googleapidirections', 0, 'uint');
		$args['googleapistaticmap']  = $app->input->get('googleapistaticmap', 0, 'uint');

		////////////////////////////////////////////////////
		///////////////////// CURRENCY /////////////////////
		////////////////////////////////////////////////////

		$args['currencysymb']     = $app->input->get('currencysymb', '', 'string');
		$args['currencyname']     = $app->input->get('currencyname', '', 'string');
		$args['symbpos']          = $app->input->get('symbpos', 0, 'int');
		$args['currdecimalsep']   = $app->input->get('currdecimalsep', '', 'string');
		$args['currthousandssep'] = $app->input->get('currthousandssep', '', 'string');
		$args['currdecimaldig']   = $app->input->get('currdecimaldig', 0, 'uint');

		////////////////////////////////////////////////////
		///////////////////// REVIEWS //////////////////////
		////////////////////////////////////////////////////

		$args['enablereviews']    = $app->input->get('enablereviews', 0, 'uint');
		$args['revleavemode']     = $app->input->get('revleavemode', 0, 'uint');
		$args['revcommentreq']    = $app->input->get('revcommentreq', 0, 'uint');
		$args['revminlength']     = $app->input->get('revminlength', 0, 'uint');
		$args['revmaxlength']     = $app->input->get('revmaxlength', 0, 'uint');
		$args['revlimlist']       = $app->input->get('revlimlist', 5, 'uint');
		$args['revlangfilter']    = $app->input->get('revlangfilter', 0, 'uint');
		$args['revautopublished'] = $app->input->get('revautopublished', 0, 'uint');

		////////////////////////////////////////////////////
		/////////////////// CLOSING DAYS ///////////////////
		////////////////////////////////////////////////////

		$args['closingdays'] = $app->input->get('cl_day_json', array(), 'json');

		////////////////////////////////////////////////////

		// get configuration model
		$config = $this->getModel();

		// Save all configuration.
		// Do not care of any errors.
		$changed = $config->saveAll($args);

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
	 * Redirects the users to the configuration page.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=editconfig');
	}
}
