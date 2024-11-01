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
 * VikRestaurants take-away menu stocks controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerTkmenustocks extends VREControllerAdmin
{
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

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') && !$user->authorise('core.access.tkmenus', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// fetch products data
		$entry_id     = $input->get('product_id', [], 'uint');
		$entry_stock  = $input->get('product_items_in_stock', [], 'uint');
		$entry_notify = $input->get('product_notify_below', [], 'uint');

		// fetch products variations data
		$option_id      = $input->get('option_id', [], 'array');
		$option_enabled = $input->get('option_stock_enabled', [], 'array');
		$option_stock   = $input->get('option_items_in_stock', [], 'array');
		$option_notify  = $input->get('option_notify_below', [], 'array');

		// create models
		$entryModel  = $this->getModel('tkentry');
		$optionModel = $this->getModel('tkentryoption');

		for ($i = 0; $i < count($entry_id); $i++)
		{
			$data = [
				'id'             => $entry_id[$i],
				'items_in_stock' => $entry_stock[$i],
				'notify_below'   => $entry_notify[$i],
			];

			$entryId = $entryModel->save($data);

			// make sure the options have been assigned to the entry
			if (isset($option_id[$entryId]))
			{
				// save variations
				for ($j = 0; $j < count($option_id[$entryId]); $j++)
				{
					$data = [
						'id'             => (int) $option_id[$entryId][$j],
						'stock_enabled'  => (int) $option_enabled[$entryId][$j],
						'items_in_stock' => (int) $option_stock[$entryId][$j],
						'notify_below'   => (int) $option_notify[$entryId][$j],
					];

					$optionModel->save($data);
				}
			}
		}
		
		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect to edit page
		$this->setRedirect('index.php?option=com_vikrestaurants&view=tkmenustocks&id_menu=' . $input->getUint('id_menu'));

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=tkmenus');
	}
}
