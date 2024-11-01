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
 * VikRestaurants restaurant reservation confirmation controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerConfirmres extends VREControllerAdmin
{
	/**
	 * Saves the table booking that have been registered within the cart.
	 *
	 * @return 	boolean
	 */
	public function saveorder()
	{
		$app = JFactory::getApplication();

		$itemid = $app->input->getUint('Itemid');

		$args = [];
		$args['date']    = $app->input->getString('date'); 
		$args['hourmin'] = $app->input->getString('hourmin');
		$args['people']  = $app->input->getUint('people');
		$args['table']   = $app->input->getUint('table');

		// prepare redirect URL
		$this->setRedirect(JRoute::rewrite('index.php?option=com_vikrestaurants&view=confirmres&' . http_build_query($args) . ($itemid ? '&Itemid=' . $itemid : ''), false));

		/**
		 * Validate session token before to proceed.
		 *
		 * @since 1.8
		 */
		if (!JSession::checkToken())
		{
			// invalid token, back to confirm page
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}

		$vik = VREApplication::getInstance();

		/**
		 * Validate ReCaptcha before processing the reservation request.
		 * The ReCaptcha is never asked to registered customers.
		 *
		 * @since 1.8.2
		 */
		if (JFactory::getUser()->guest && $vik->isGlobalCaptcha() && !$vik->reCaptcha('check'))
		{
			// invalid captcha
			$app->enqueueMessage(JText::translate('PLG_RECAPTCHA_ERROR_EMPTY_SOLUTION'), 'error');
			return false;
		}

		// load arguments from request
		$args['id_payment'] = $app->input->getUint('id_payment', 0);
		$args['itemid']     = $itemid;

		// get view model
		$model = $this->getModel();

		// try to save the reservation and get landing page
		$url = $model->save($args);

		// make sure we haven't faced any errors		
		if (!$url)
		{
			// get all registered errors
			$errors = $model->getErrors();

			foreach ($errors as $err)
			{
				// enqueue error message
				$app->enqueueMessage($err instanceof Exception ? $err->getMessage() : (string) $err, 'error');
			}

			return false;
		}

		// update redirect URL to reach the landing page
		$this->setRedirect($url);
		return true;
	}

	/**
	 * End-point used to redeem the coupon code.
	 *
	 * @return 	boolean
	 */
	public function redeemcoupon()
	{
		$app = JFactory::getApplication();

		$args = [];
		$args['date']    = $app->input->getString('date'); 
		$args['hourmin'] = $app->input->getString('hourmin');
		$args['people']  = $app->input->getUint('people');
		$args['table']   = $app->input->getUint('table');

		$itemid = $app->input->getUint('Itemid');

		$url = 'index.php?option=com_vikrestaurants&view=confirmres&date=' . $args['date'] . '&hourmin=' . $args['hourmin'] . '&people=' . $args['people'];

		if ($args['table'])
		{
			// concat table only if the user selected it
			$url .= '&table=' . $args['table'];
		}

		// prepare redirect URL
		$this->setRedirect(JRoute::rewrite($url . ($itemid ? '&Itemid=' . $itemid : ''), false));

		if (!JSession::checkToken())
		{
			// direct access attempt
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}
		
		// get coupon key from POST
		$coupon = $app->input->post->getString('couponkey', '');

		// get cart model
		$model = $this->getModel('rescart');

		// try to redeem the coupon code
		$res = $model->redeemCoupon($coupon, $args);

		if (!$res)
		{
			// get last error registered by the model
			$error = $model->getError($index = null, $string = true);
			// propagate error or use the default one
			$app->enqueueMessage($error ? $error : JText::translate('VRCOUPONNOTVALID'), 'error');
			return false;
		}

		// coupon applied successfully
		$app->enqueueMessage(JText::translate('VRCOUPONFOUND'));
		return true;
	}
}
