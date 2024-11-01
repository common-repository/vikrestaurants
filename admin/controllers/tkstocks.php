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
 * VikRestaurants take-away items stock overrides/refills controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerTkstocks extends VREControllerAdmin
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

		$args = [];
		$args['eid']      = $input->get('id_product', [], 'uint');
		$args['oid']      = $input->get('id_option', [], 'uint');
		$args['original'] = $input->get('original_stock', [], 'uint');
		$args['override'] = $input->get('stock_override', [], 'uint');
		$args['factor']   = $input->get('stock_factor', [], 'int');

		$map = [];

		for ($i = 0; $i < count($args['eid']); $i++)
		{
			// exclude in case the override was not specified
			if ($args['override'][$i] > 0)
			{
				// use entry-option key to avoid duplicate
				// queries in case of chained variations
				$key = $args['eid'][$i] . '-' . $args['oid'][$i];

				// create/replace map record
				$map[$key] = array(
					'id_takeaway_entry'  => $args['eid'][$i],
					'id_takeaway_option' => $args['oid'][$i],
					'items_available'    => $args['override'][$i] * $args['factor'][$i],
					'items_in_stock'     => $args['original'][$i],
				);
			}
		}

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') && !$user->authorise('core.access.tkorders', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// iterate maps
		foreach ($map as $data)
		{
			// refill stock (do not care of errors)
			$this->getModel('tkstock')->refill($data);
		}
		
		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect to edit page
		$this->setRedirect('index.php?option=com_vikrestaurants&view=tkstocks');

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_vikrestaurants&view=tkreservations');
	}
}
