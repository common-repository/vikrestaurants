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

use E4J\VikRestaurants\Helpers\DateHelper;

/**
 * VikRestaurants coupon controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerCoupon extends VREControllerAdmin
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

		if (!is_null($group))
		{
			$data['group'] = $group;
		}

		$id_category = $app->input->getUint('id_category');

		if (!is_null($id_category))
		{
			$data['id_category'] = $id_category;
		}

		// unset user state for being recovered again
		$app->setUserState('vre.coupon.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.coupons', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managecoupon');

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
		$app->setUserState('vre.coupon.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.coupons', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=managecoupon&cid[]=' . $cid[0]);

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
			$input = JFactory::getApplication()->input;

			$url = 'index.php?option=com_vikrestaurants&task=coupon.add';

			// recover group from request
			$group = $input->getUint('group');

			if (!is_null($group))
			{
				$url .= '&group=' . $group;
			}

			// recover category from request
			$id_category = $input->getUint('id_category');

			if (!is_null($id_category))
			{
				$url .= '&id_category=' . $id_category;
			}

			$this->setRedirect($url);
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
		$args['code']             = $input->get('code', '', 'string');
		$args['type']             = $input->get('type', 1, 'uint');
		$args['percentot']        = $input->get('percentot', 1, 'uint');
		$args['value']            = $input->get('value', 0.0, 'float');
		$args['start_publishing'] = $input->get('start_publishing', '', 'string');
		$args['end_publishing']   = $input->get('end_publishing', '', 'string');
		$args['mincost']          = $input->get('mincost', 0.0, 'float');
		$args['minpeople']        = $input->get('minpeople', 0, 'uint');
		$args['usages']           = $input->get('usages', 0, 'uint');
		$args['maxusages']        = $input->get('maxusages', 1, 'uint');
		$args['maxperuser']       = $input->get('maxperuser', 0, 'uint');
		$args['remove_gift']      = $input->get('remove_gift', 0, 'uint');
		$args['group']            = $input->get('group', 0, 'int');
		$args['notes']            = $input->get('notes', '', 'string');
		$args['id_category']      = $input->get('id_category', 0, 'uint');
		$args['id']               = $input->get('id', 0, 'int');

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.coupons', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		/**
		 * Convert timestamp from local timezone to UTC.
		 *
		 * @since 1.9
		 */
		$args['start_publishing'] = DateHelper::getSqlDateLocale($args['start_publishing']);
		$args['end_publishing']   = DateHelper::getSqlDateLocale($args['end_publishing']);

		// try to auto-create a new category before saving the coupon
		if ($args['id_category'] == 0 && ($categoryName = $input->getString('category_name')))
		{
			// make sure the user is authorised
			if ($user->authorise('core.create', 'com_vikrestaurants'))
			{
				$category = $this->getModel('couponcategory');

				// attempt to save category
				$id_category = $category->save(['name' => $categoryName]);
				
				if ($id_category)
				{
					// overwrite the category ID
					$args['id_category'] = $id_category;
				}
			}
		}

		// get coupon model
		$coupon = $this->getModel();

		// try to save arguments
		$id = $coupon->save($args);

		if (!$id)
		{
			// get string error
			$error = $coupon->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=managecoupon';

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
		$this->setRedirect('index.php?option=com_vikrestaurants&task=coupon.edit&cid[]=' . $id);

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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.coupons', 'com_vikrestaurants'))
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
		$this->setRedirect('index.php?option=com_vikrestaurants&view=coupons');
	}
}
