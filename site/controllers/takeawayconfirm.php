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
 * VikRestaurants take-away order confirmation controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerTakeawayconfirm extends VREControllerAdmin
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
		$args['date']    = $app->input->getString('date', '');
		$args['hourmin'] = $app->input->getString('hourmin', '');
		$args['service'] = $app->input->getString('service', '');
		$args['asap']    = $app->input->getBool('asap', false);

		// prepare redirect URL
		$this->setRedirect(JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayconfirm&' . http_build_query($args) . ($itemid ? '&Itemid=' . $itemid : ''), false));

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
		 * Validate ReCaptcha before processing the order request.
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
		$args['gratuity']   = $app->input->getFloat('gratuity', 0);
		$args['itemid']     = $itemid;

		// get view model
		$model = $this->getModel();

		// try to save the order and get landing page
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
	 * Removes the selected item from the cart.
	 *
	 * @return 	void
	 */
	public function removefromcart()
	{
		$app = JFactory::getApplication();

		$itemid = $app->input->get('Itemid', null, 'uint');
		
		// always back to confirmation page
		$this->setRedirect(JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayconfirm' . ($itemid ? '&Itemid=' . $itemid : ''), false));
		
		if (!JSession::checkToken('get'))
		{
			// direct access attempt
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}

		$index = $app->input->getUint('index');

		// get take-away cart model
		$model = $this->getModel('tkcart');

		// try to remove the item at the provided index
		$response = $model->removeItem($index);

		if (!$response)
		{
			// fetch last registered error message
			$error = $model->getError($last = null, $string = true);

			// register error message in the user state
			$app->enqueueMessage($error, 'error');
			return false;
		}

		// item removed successfully
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
		$args['service'] = $app->input->getString('service');

		$itemid = $app->input->getUint('Itemid');

		$url = 'index.php?option=com_vikrestaurants&view=takeawayconfirm&date=' . $args['date'] . '&hourmin=' . $args['hourmin'] . '&service=' . $args['service'];

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
		$model = $this->getModel('tkcart');

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

	/**
	 * AJAX End-point used to return the delivery information of
	 * the specified coordinates/ZIP.
	 *
	 * @return 	void
	 */
	public function getlocationinfo()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// direct access attempt
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));	
		}

		// get request data
		$query = $app->input->get('query', [], 'array');

		/** @var E4J\VikRestaurants\DeliveryArea\AreasCollection */
		$zones = E4J\VikRestaurants\DeliveryArea\AreasCollection::getInstance()
			->filter(new E4J\VikRestaurants\DeliveryArea\Filters\PublishedFilter());

		// prepare search query
		$query = new E4J\VikRestaurants\DeliveryArea\DeliveryQuery($query);

		if (count($zones))
		{
			/** @var E4J\VikRestaurants\DeliveryArea\Area|null */
			$area = (new E4J\VikRestaurants\DeliveryArea\DeliveryChecker($zones))->search($query);

			if (!$area)
			{
				// clear delivery address from session
				$this->getModel()->setDeliveryAddress(null);

				// cannot deliver to the provided address
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(406, JText::translate('VRTKDELIVERYLOCNOTFOUND'));			
			}
		}
		else
		{
			// areas not configured, delivery always allowed
			$area = null;
		}

		// return information in JSON format
		$response = new stdClass;
		
		// set up address information
		$response->coordinates = $query->getCoordinates();
		$response->zip         = $query->getZipCode();
		$response->city        = $query->getCity();
		$response->address     = $query->getAddress();
		$response->query       = $query;

		if ($area)
		{
			$currency = VREFactory::getCurrency();

			// set up area details
			$response->area = (object) $area->getProperties();

			// set up formatted texts
			$response->texts = new stdClass;
			$response->texts->charge  = ($area->charge > 0 ? '+ ' : '') . $currency->format($area->charge);
			$response->texts->minCost = $currency->format($area->min_cost);
		}

		// register details in session
		$this->getModel()->setDeliveryAddress($response);

		// send response to caller
		$this->sendJSON($response);
	}

	/**
	 * Calculates the service charge/discount and the updated cart totals.
	 * 
	 * @return  void
	 */
	public function getservicecharge()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// direct access attempt
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));	
		}

		$service = $app->input->getString('service', 'delivery');
		$areaId  = $app->input->getUint('area', 0);

		// recalculate service totals
		$response = $this->getModel('tkcart')->updateServiceTotals($service, $areaId);

		// return to caller
		$this->sendJSON($response);
	}
}
