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
 * VikRestaurants take-away item controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerTakeawayitem extends VREControllerAdmin
{
	/**
	 * Task used to register a product review left by a customer.
	 *
	 * @return  bool
	 *
	 * @since 1.7
	 * @since 1.9 Renamed from "submit_review", declared by main controller.
	 */
	function review()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		// build return URL
		$url =  'index.php?option=com_vikrestaurants&view=revslist';

		// append item ID
		$itemid = $app->input->get('Itemid', null, 'uint');

		if ($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}
		
		// append request filters
		foreach ($app->input->get('request', [], 'array') as $k => $v)
		{
			if (!empty($k))
			{
				$url .= '&' . $k . '=' . $v;
			}
		}

		// prepare error url
		$this->setRedirect(JRoute::rewrite($url . '&submit_rev=1', false));

		/**
		 * Prevent direct access to this task.
		 * Submit is allowed only if the form
		 * to leave a review is visited.
		 *
		 * @since 1.8
		 */
		if (!JSession::checkToken())
		{
			// invalid session token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}

		$vik = VREApplication::getInstance();

		/**
		 * Added support for ReCaptcha validation.
		 * The ReCaptha is displayed only if it has been globally configured.
		 *
		 * @since 1.8
		 * 
		 * @since 1.9  Skip captcha validation in case the user is logged in.
		 */
		if ($user->guest && ($vik->isGlobalCaptcha() && !$vik->reCaptcha('check')))
		{
			// invalid captcha
			$app->enqueueMessage(JText::translate('PLG_RECAPTCHA_ERROR_EMPTY_SOLUTION'), 'error');
			return false;
		}

		/**
		 * Retrieve review fields by using the related helper.
		 *
		 * @since 1.9
		 */
		$leaveReviewFieldsProvider = new E4J\VikRestaurants\CustomFields\Providers\LeaveReviewFieldsProvider([
			// use the name and email field only to guest users
			'logged' => !$user->guest,
		]);

		$leaveReviewFields = new E4J\VikRestaurants\CustomFields\FieldsCollection($leaveReviewFieldsProvider);

		// create requestor for the review fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor($leaveReviewFields);

		try
		{
			// load fields
			$args = $requestor->loadForm($fieldsData, $strict = true);
		}
		catch (Exception $e)
		{
			// invalid fields, raise error message
			$app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		// bind data before starting checking the request,
		// so that we can recover the filled details
		$app->setUserState('vre.review.data', $args);
		
		// fetch review data
		$data = [];
		$data['title']               = $args['review_title'];
		$data['comment']             = $args['review_comment'];
		$data['rating']              = $args['review_rating'];
		$data['id_takeaway_product'] = $app->input->getUint('id_tk_prod');

		if (!empty($args['review_user_name']))
		{
			$data['name'] = $args['review_user_name'];
		}

		if (!empty($args['review_user_mail']))
		{
			$data['email'] = $args['review_user_mail'];
		}

		// inject relevant fields data within the array to save
		$data['fieldsData'] = $fieldsData;

		$review = $this->getModel('review');

		// try to leave the review
		if (!$review->leave($data))
		{
			// an error occurred
			$app->enqueueMessage($review->getError(null, true), 'error');
			return false;
		}

		// clear user state on success
		$app->setUserState('vre.review.data', []);

		// refresh saved data
		$data = $review->getData();
		
		if ($data['published'])
		{
			// review approved
			$app->enqueueMessage(JText::translate('VRPOSTREVIEWCREATEDCONF'));
		}
		else
		{
			// waiting for approval
			$app->enqueueMessage(JText::translate('VRPOSTREVIEWCREATEDPEND'));
		}

		// change landing page on success
		$this->setRedirect(JRoute::rewrite($url, false));
		return true;
	}
}
