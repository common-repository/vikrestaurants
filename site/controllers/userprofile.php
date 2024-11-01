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
 * VikRestaurants user profile view controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerUserprofile extends VREControllerAdmin
{
	/**
	 * Task used to create a new user through the registration form
	 * used by VikRestaurants.
	 *
	 * @return  bool
	 */
	public function register()
	{
		$app = JFactory::getApplication();
		
		$return = base64_decode($app->input->getBase64('return'));

		if (empty($return))
		{
			$return = 'index.php';
		}
		
		// create successful return URL
		$okReturn = JRoute::rewrite($return, false);

		// create failure return URL
		$failReturn = JUri::getInstance($return);
		$failReturn->setVar('tab', 'registration');

		// set error redirect URL by default
		$this->setRedirect(JRoute::rewrite($failReturn, false));

		if (!JSession::checkToken())
		{
			// invalid session token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}

		if (!VREFactory::getConfig()->getBool('enablereg'))
		{
			// registration disabled
			$app->enqueueMessage(JText::translate('VRREGISTRATIONFAILED1'), 'error');
			return false;
		}

		$vik = VREApplication::getInstance();

		if ($vik->isCaptcha() && !$vik->reCaptcha('check'))
		{
			// invalid captcha
			$app->enqueueMessage(JText::translate('PLG_RECAPTCHA_ERROR_EMPTY_SOLUTION'), 'error');
			return false;
		}

		/**
		 * Retrieve registration fields by using the related helper.
		 *
		 * @since 1.9
		 */
		$userRegisterFieldsProvider = new E4J\VikRestaurants\CustomFields\Providers\UserRegisterFieldsProvider();
		$userRegisterFields = new E4J\VikRestaurants\CustomFields\FieldsCollection($userRegisterFieldsProvider);

		// create requestor for the registration fields
		$requestor = new E4J\VikRestaurants\CustomFields\FieldsRequestor($userRegisterFields);

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
		
		if (!VikRestaurants::checkUserArguments($args))
		{
			// missing required field (or the user was already logged in)
			$app->enqueueMessage(JText::translate('VRREGISTRATIONFAILED2'), 'error');
			return false;
		}
		
		// try to register a new user account
		$userid = VikRestaurants::createNewUserAccount($args);

		if (!$userid)
		{
			// an error occurred...
			return false;
		}

		// switch redirect URL on success
		$this->setRedirect($okReturn);
		
		if ($userid == 'useractivate' || $userid == 'adminactivate')
		{
			// registration requires a manual activation
			return true;
		}
		
		// successful registration, auto log-in
		$credentials = [
			'username' => $args['username'],
			'password' => $args['password'],
			'remember' => true,
		];
		
		$app->login($credentials);

		$user = JFactory::getUser();
		$user->setLastVisit();
		$user->set('guest', 0);
		
		return true;		
	}

	/**
	 * Task used to perform the logout of the current user.
	 * The user will be redirected to the "allorders" page.
	 *
	 * @return  void
	 */
	public function logout()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!$user->guest)
		{
			// complete log out in case the user is not a guest
			$app->logout($user->id);
		}

		// get return URL from request
		$return_url = $app->input->get('return', null, 'string');

		if (is_null($return_url))
		{
			// get return view
			$view   = $app->input->get('return_view', 'allorders');
			$itemid = $app->input->get('Itemid', null, 'uint');

			// build return URL
			$return_url = JRoute::rewrite('index.php?option=com_vikrestaurants&view=' . $view . ($itemid ? '&Itemid=' . $itemid : ''), false);
		}
		else
		{
			// decode return URL
			$return_url = base64_decode($return_url);
		}

		$this->setRedirect($return_url);
	}
}
