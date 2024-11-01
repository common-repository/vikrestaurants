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
 * VikRestaurants restaurant configuration controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerConfigres extends VREControllerAdmin
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

		// RESTAURANT

		// System
		$args['ondashboard'] = $app->input->get('ondashboard', 0, 'uint');

		// Reservations
		$args['defstatus']       = $app->input->get('defstatus', '', 'string');
		$args['selfconfirm']     = $app->input->get('selfconfirm', 0, 'uint');
		$args['averagetimestay'] = $app->input->get('averagetimestay', 0, 'uint');
		$args['tablocktime']     = $app->input->get('tablocktime', 0, 'uint');
		$args['loginreq']        = $app->input->get('loginreq', 0, 'uint');

		// Deposit
		$args['askdeposit']    = $app->input->get('askdeposit', 0, 'uint');
		$args['resdeposit']    = $app->input->get('resdeposit', 0, 'float');
		$args['costperperson'] = $app->input->get('costperperson', 0, 'uint');

		// Cancellation
		$args['enablecanc'] = $app->input->get('enablecanc', 0, 'uint');
		$args['cancreason'] = $app->input->get('cancreason', 0, 'uint');
		$args['canctime']   = $app->input->get('canctime', 0, 'uint');
		$args['cancunit']   = $app->input->get('cancunit', 'days', 'string');
		$args['cancmins']   = $app->input->get('cancmins', 0, 'uint');

		// SEARCH

		// Date & Time
		$args['minuteintervals'] = $app->input->get('minuteintervals', 0, 'uint');
		$args['bookrestr']       = $app->input->get('bookrestr', 0, 'uint');
		$args['mindate']         = $app->input->get('mindate', 0, 'uint');
		$args['maxdate']         = $app->input->get('maxdate', 0, 'uint');

		// People
		$args['minimumpeople'] = $app->input->get('minimumpeople', 0, 'uint');
		$args['maximumpeople'] = $app->input->get('maximumpeople', 0, 'uint');
		$args['largepartylbl'] = $app->input->get('largepartylbl', 0, 'uint');
		$args['largepartyurl'] = $app->input->get('largepartyurl', '', 'string');

		// Table
		$args['reservationreq'] = $app->input->get('reservationreq', 0, 'uint');

		// Safety
		$args['safedistance'] = $app->input->get('safedistance', 0, 'uint');
		$args['safefactor']   = $app->input->get('safefactor', 1, 'float');

		// FOOD

		$args['choosemenu']    = $app->input->get('choosemenu', 0, 'uint');
		$args['orderfood']     = $app->input->get('orderfood', 0, 'uint');
		$args['editfood']      = $app->input->get('editfood', 0, 'uint');
		$args['servingnumber'] = $app->input->get('servingnumber', 0, 'uint');

		// TAXES

		$args['deftax']   = $app->input->get('deftax', 0, 'uint');
		$args['usetaxbd'] = $app->input->get('usetaxbd', 0, 'uint');

		// COLUMNS

		// Reservations List Columns
		$args['listablecols'] = $app->input->get('listablecols', [], 'string');

		// Custom Fields
		$args['listablecf'] = $app->input->get('listablecf', [], 'string');

		////////////////////////////////////////////////////
		////////////////////// E-MAIL //////////////////////
		////////////////////////////////////////////////////

		// NOTIFICATIONS

		$args['mailcustwhen']  = $app->input->get('mailcustwhen', [], 'string');
		$args['mailoperwhen']  = $app->input->get('mailoperwhen', [], 'string');
		$args['mailadminwhen'] = $app->input->get('mailadminwhen', [], 'string');

		// TEMPLATES

		$args['mailtmpl']      = $app->input->get('mailtmpl', '', 'string');
		$args['adminmailtmpl'] = $app->input->get('adminmailtmpl', '', 'string');
		$args['cancmailtmpl']  = $app->input->get('cancmailtmpl', '', 'string');

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
	 * Redirects the users to the restaurant configuration page.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=editconfigres');
	}
}
