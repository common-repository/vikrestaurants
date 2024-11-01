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
 * VikRestaurants operator coupon controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerOpcoupon extends VREControllerAdmin
{
	/**
	 * Task used to access the creation page of a new record.
	 *
	 * @return 	boolean
	 */
	public function add()
	{
		$app = JFactory::getApplication();

		// unset user state for being recovered again
		$app->setUserState('vre.coupon.data', []);

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}

		$url = 'index.php?option=com_vikrestaurants&view=opmanagecoupon';

		$itemid = $app->input->get('Itemid', 0, 'uint');

		if ($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}

		$this->setRedirect(JRoute::rewrite($url, false));

		return true;
	}

	/**
	 * Task used to access the management page of an existing record.
	 *
	 * @return 	boolean
	 */
	public function edit()
	{
		$app = JFactory::getApplication();

		// unset user state for being recovered again
		$app->setUserState('vre.coupon.data', []);

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=opmanagecoupon&cid[]=' . $cid[0]);

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
			$itemid = JFactory::getApplication()->input->get('Itemid', 0, 'uint');

			$url = 'index.php?option=com_vikrestaurants&task=opcoupon.add' . ($itemid ? '&Itemid=' . $itemid : '');

			$this->setRedirect(JRoute::rewrite($url, false));
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
		$app = JFactory::getApplication();

		// get current operator
		$operator = VikRestaurants::getOperator();

		// make sure the user is an operator and it is
		// allowed to access the private area
		if (!$operator || !$operator->canLogin())
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel('oversight');

			return false;
		}
		
		$args = [];
		$args['code']             = $app->input->getString('code');
		$args['type']             = $app->input->getUint('type', 1);
		$args['percentot']        = $app->input->getUint('percentot', 1);
		$args['value']            = $app->input->getFloat('value');
		$args['start_publishing'] = $app->input->getString('start_publishing', '');
		$args['end_publishing']   = $app->input->getString('end_publishing', '');
		$args['minpeople']        = $app->input->getUint('minpeople', 1);
		$args['mincost']          = $app->input->getFloat('mincost', 0);
		$args['group']            = $app->input->getUint('group');
		$args['id']               = $app->input->getInt('id', 0);

		// check user permissions
		if (!$operator->canManage('coupon'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		if ($args['group'] == 0 && !$operator->isRestaurantAllowed())
		{
			// restaurant not supported, back to take-away
			$args['group'] = 1;
		}

		if ($args['group'] == 1 && !$operator->isTakeawayAllowed())
		{
			// take-away not supported, back to restaurant
			$args['group'] = 0;
		}

		/**
		 * Convert timestamp from local timezone to UTC.
		 *
		 * @since 1.9
		 */
		$args['start_publishing'] = E4J\VikRestaurants\Helpers\DateHelper::getSqlDateLocale($args['start_publishing']);
		$args['end_publishing']   = E4J\VikRestaurants\Helpers\DateHelper::getSqlDateLocale($args['end_publishing']);

		$itemid = $app->input->get('Itemid', 0, 'uint');

		$coupon = $this->getModel('coupon');

		// try to save arguments
		$id = $coupon->save($args);
		
		if (!$id)
		{
			// get string error
			$error = $coupon->getError(null, true);

			// display error message
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

			$url = 'index.php?option=com_vikrestaurants&view=opmanagecoupon' . ($itemid ? '&Itemid=' . $itemid : '');

			if ($coupon->id)
			{
				$url .= '&cid[]=' . $id;
			}

			// redirect to new/edit page
			$this->setRedirect(JRoute::rewrite($url, false));
				
			return false;
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		$url = 'index.php?option=com_vikrestaurants&task=opcoupon.edit&cid[]=' . $id . ($itemid ? '&Itemid=' . $itemid : '');

		// redirect to edit page
		$this->setRedirect(JRoute::rewrite($url, false));

		return true;
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @param 	string  $view  The return view.
	 *
	 * @return 	void
	 */
	public function cancel($view = null)
	{
		$itemid = JFactory::getApplication()->input->get('Itemid', 0, 'uint');

		$url = 'index.php?option=com_vikrestaurants' . ($itemid ? '&Itemid=' . $itemid : '');

		if (is_null($view))
		{
			$url .= '&view=opcoupons';
		}
		else
		{
			$url .= '&view=' . $view;
		}

		$this->setRedirect(JRoute::rewrite($url, false));
	}
}
