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
 * VikRestaurants plugin Shortcodes controller.
 *
 * @since 1.0
 */
class VikRestaurantsControllerShortcodes extends VREControllerAdmin
{
	/**
	 * Task used to access the creation page of a new record.
	 *
	 * @return 	boolean
	 */
	public function add()
	{
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			wp_die(
				'<h1>' . JText::translate('FATAL_ERROR') . '</h1>' .
				'<p>' . JText::translate('RESOURCE_AUTH_ERROR') . '</p>',
				403
			);
		}

		$app = JFactory::getApplication();

		$encoded = $app->input->getBase64('return', '');

		$this->setRedirect('admin.php?page=vikrestaurants&view=shortcode' . ($encoded ? '&return=' . $encoded : ''));

		return true;
	}

	/**
	 * Task used to access the management page of an existing record.
	 *
	 * @return 	boolean
	 */
	public function edit()
	{
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			wp_die(
				'<h1>' . JText::translate('FATAL_ERROR') . '</h1>' .
				'<p>' . JText::translate('RESOURCE_AUTH_ERROR') . '</p>',
				403
			);
		}

		$app = JFactory::getApplication();

		$cid = $app->input->getUint('cid', [0]);

		$encoded = $app->input->getBase64('return', '');

		$this->setRedirect('admin.php?page=vikrestaurants&view=shortcode&cid[]=' . $cid[0] . ($encoded ? '&return=' . $encoded : ''));

		return true;
	}

	/**
	 * Deletes a list of records set in the request.
	 *
	 * @return 	boolean
	 */
	public function delete()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// missing CSRF token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			$this->cancel();
			return false;
		}

		$cid = $app->input->getUint('cid', []);

		$this->model->delete($cid);

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
		$app = JFactory::getApplication();

		$encoded = $app->input->getBase64('return', '');

		$this->setRedirect('admin.php?page=vikrestaurants&view=shortcodes' . ($encoded ? '&return=' . $encoded : ''));
	}

	/**
	 * Goes back to the specified return URL.
	 *
	 * @return 	void
	 */
	public function back()
	{
		$app = JFactory::getApplication();

		$return = $app->input->getBase64('return', '');

		if ($return)
		{
			$return = base64_decode($return);
		}

		$this->setRedirect($return);
	}
}
