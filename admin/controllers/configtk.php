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
 * VikRestaurants take-away configuration controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerConfigtk extends VREControllerAdmin
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

		// SHOP

		// Orders
		$args['tkdefstatus']   = $app->input->get('tkdefstatus', '', 'string');
		$args['tkselfconfirm'] = $app->input->get('tkselfconfirm', 0, 'uint');
		$args['tklocktime']    = $app->input->get('tklocktime', 0, 'uint');
		$args['tkloginreq']    = $app->input->get('tkloginreq', 0, 'uint');

		// Cancellation
		$args['tkenablecanc'] = $app->input->get('tkenablecanc', 0, 'uint');
		$args['tkcancreason'] = $app->input->get('tkcancreason', 0, 'uint');
		$args['tkcanctime']   = $app->input->get('tkcanctime', 0, 'uint');
		$args['tkcancunit']   = $app->input->get('tkcancunit', 'days', 'string');
		$args['tkcancmins']   = $app->input->get('tkcancmins', 0, 'uint');

		// Menus List
		$args['revtakeaway']      = $app->input->get('revtakeaway', 0, 'uint');
		$args['tkshowimages']     = $app->input->get('tkshowimages', 0, 'uint');
		$args['tkshowtimes']      = $app->input->get('tkshowtimes', 0, 'uint');
		$args['tkproddesclength'] = $app->input->get('tkproddesclength', 0, 'uint');
		$args['tknote']           = JComponentHelper::filterText($app->input->get('tknote', '', 'raw'));

		// PURCHASE

		// Date & Time
		$args['tkminint']    = $app->input->get('tkminint', 0, 'uint');
		$args['asapafter']   = $app->input->get('asapafter', 0, 'uint');
		$args['tkallowdate'] = $app->input->get('tkallowdate', 0, 'uint');
		$args['tkwhenopen']  = $app->input->get('tkwhenopen', 0, 'uint');
		$args['tkpreorder']  = $app->input->get('tkpreorder', 0, 'uint');
		$args['tkmindate']   = $app->input->get('tkmindate', 0, 'uint');
		$args['tkmaxdate']   = $app->input->get('tkmaxdate', 0, 'uint');

		// Cart
		$args['mincostperorder'] = $app->input->get('mincostperorder', 0.0, 'float');
		$args['tkmaxitems']      = $app->input->get('tkmaxitems', 0, 'uint');

		// Availability
		$args['mealsperint']      = $app->input->get('mealsperint', 0, 'uint');
		$args['tkordmaxser']      = $app->input->get('tkordmaxser', 0, 'uint');
		$args['tkordperint']      = $app->input->get('tkordperint', 0, 'uint');
		$args['tkmealsbackslots'] = $app->input->get('tkmealsbackslots', 0, 'uint');

		// Food
		$args['tkuseoverlay']  = $app->input->get('tkuseoverlay', 0, 'uint');
		$args['tkenablestock'] = $app->input->get('tkenablestock', 0, 'uint');

		// Gratuities
		$args['tkenablegratuity'] = $app->input->get('tkenablegratuity', 0, 'uint');
		$args['tkdefgratuity']    = $app->input->get('tkdefgrat_amount', 0, 'float') . ':' . $app->input->get('tkdefgrat_percentot', 1, 'uint');

		// DELIVERY

		// Delivery
		$args['enabledelivery'] = $app->input->get('enabledelivery', null, 'string');
		$args['dsprice']        = $app->input->get('dsprice', 0.0, 'float');
		$args['dspercentot']    = $app->input->get('dspercentot', 0, 'uint');
		$args['freedelivery']   = $app->input->get('freedelivery', 0.0, 'float');

		// Takeaway
		$args['enablepickup']    = $app->input->get('enablepickup', null, 'string');
		$args['pickupprice']     = $app->input->get('pickupprice', 0.0, 'float');
		$args['pickuppercentot'] = $app->input->get('pickuppercentot', 0, 'uint');

		// Service
		$args['tkdefaultservice'] = $app->input->get('tkdefaultservice', '', 'string');

		// TAXES

		$args['tkdeftax']   = $app->input->get('tkdeftax', 0, 'uint');
		$args['tkusetaxbd'] = $app->input->get('tkusetaxbd', 0, 'uint');

		// COLUMNS

		// Reservations List Columns
		$args['tklistablecols'] = $app->input->get('tklistablecols', [], 'string');

		// Custom Fields
		$args['tklistablecf'] = $app->input->get('tklistablecf', [], 'string');

		////////////////////////////////////////////////////
		////////////////////// E-MAIL //////////////////////
		////////////////////////////////////////////////////

		// NOTIFICATIONS

		$args['tkmailcustwhen']  = $app->input->get('tkmailcustwhen', [], 'string');
		$args['tkmailoperwhen']  = $app->input->get('tkmailoperwhen', [], 'string');
		$args['tkmailadminwhen'] = $app->input->get('tkmailadminwhen', [], 'string');

		// TEMPLATES

		$args['tkmailtmpl']       = $app->input->get('tkmailtmpl', '', 'string');
		$args['tkadminmailtmpl']  = $app->input->get('tkadminmailtmpl', '', 'string');
		$args['tkcancmailtmpl']   = $app->input->get('tkcancmailtmpl', '', 'string');
		$args['tkreviewmailtmpl'] = $app->input->get('tkreviewmailtmpl', '', 'string');
		$args['tkstockmailtmpl']  = $app->input->get('tkstockmailtmpl', '', 'string');

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
	 * Redirects the users to the take-away configuration page.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=editconfigtk');
	}
}
