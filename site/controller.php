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

// import joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of VikRestaurants component.
 *
 * @since 1.0
 */
class VikRestaurantsController extends JControllerVRE
{
	/**
	 * Display task.
	 *
	 * @return  void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$input = JFactory::getApplication()->input;

		$view = strtolower($input->get('view', ''));

		// check if we should invoke a method before displaying the view
		if (method_exists($this, $view))
		{
			// invoke method
			$this->{$view}();
		}

		// forbid access to disabled areas
		switch ($view)
		{
			case 'restaurants':
			case 'search':
			case 'confirmres':
				if (!VikRestaurants::isRestaurantEnabled())
				{
					throw new Exception(JText::translate('VRRESTAURANTDISABLED'), 403);
				}
				break;

			case 'takeaway':
			case 'takeawayconfirm':
			case 'takeawayitem':
				if (!VikRestaurants::isTakeAwayEnabled())
				{
					throw new Exception(JText::translate('VRTAKEAWAYDISABLED'), 403);
				}
				break;
		}

		parent::display();
	}
	
	/**
	 * Validate the request made before checking
	 * for any available tables.
	 *
	 * @return 	void
	 */
	public function search()
	{
		$app = JFactory::getApplication();

		$args = [];
		$args['date']    = $app->input->getString('date'); 
		$args['hourmin'] = $app->input->getString('hourmin');
		$args['people']  = $app->input->getUint('people');

		$itemid = $app->input->get('Itemid', null, 'uint');

		// fetch error URL
		$error_url = 'index.php?option=com_vikrestaurants&view=restaurants';

		if ($args)
		{
			$error_url .= '&' . http_build_query($args);
		}

		if ($itemid)
		{
			$error_url .= '&Itemid=' . $itemid;
		}

		/**
		 * Flag used to check whether the customer already agreed
		 * that all the customers belong to the same family.
		 *
		 * @var   boolean
		 * @since 1.8
		 *
		 * @see   COVID-19
		 */
		$app->setUserState('vre.search.family', $app->input->getBool('family', false));

		$model = JModelVRE::getInstance('rescart');

		// validate request
		if (!$model->checkIntegrity($args))
		{
			// fetch last error
			$app->enqueueMessage($model->getError($last = null, $string = true), 'error');
			$app->redirect(JRoute::rewrite($error_url, false));
			return false;
		}
		
		/**
		 * Remove all the reservations that haven't been confirmed
		 * within the specified range of time (15 minutes by default).
		 *
		 * In this way, we can free the tables that were occupied
		 * before showing the availability to this customer.
		 * 
		 * @since 1.8
		 */
		VikRestaurants::removeRestaurantReservationsOutOfTime();
	}
	
	/**
	 * Performs additional checks before letting the 
	 * customers access the confirmation page.
	 *
	 * @return 	void
	 */
	public function confirmres()
	{	
		$app     = JFactory::getApplication();
		$session = JFactory::getSession();
		
		$args = [];
		$args['date']    = $app->input->getString('date'); 
		$args['hourmin'] = $app->input->getString('hourmin');
		$args['people']  = $app->input->getUint('people');
		$args['table']   = $app->input->getUint('table');
		// get list of selected menus, if requested
		$args['menus'] = $app->input->get('menus', [], 'array');

		// recover family flag from user state
		$family = $app->getUserState('vre.search.family', false);

		$itemid = $app->input->get('Itemid', null, 'uint');

		// fetch error URL
		$error_url = 'index.php?option=com_vikrestaurants&date=' . $args['date'] . '&hourmin=' . $args['hourmin'] . '&people=' . $args['people'];

		if ($itemid)
		{
			$error_url .= '&Itemid=' . $itemid;
		}

		$model = JModelVRE::getInstance('rescart');
		
		// validate request
		if (!$model->checkIntegrity($args))
		{
			// fetch last error
			$app->enqueueMessage($model->getError($last = null, $string = true), 'error');

			// back to restaurants view in case of malformed request
			$app->redirect(JRoute::rewrite($error_url . '&view=restaurants', false));
			return false;
		}

		// validate choosable menus
		if (!$model->validateMenus($args))
		{
			// fetch last error
			$app->enqueueMessage($model->getError($last = null, $string = true), 'error');

			// back to search view in case of missing menus selection
			$app->redirect(JRoute::rewrite($error_url . '&view=search&back=1' . ($family ? '&family=1' : ''), false));
			return false;
		}
	}

	/**
	 * Performs additional checks before letting the customers
	 * access the take-away order confirmation page.
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function takeawayconfirm()
	{	
		$app = JFactory::getApplication();

		$itemid = $app->input->get('Itemid', null, 'uint');
		
		$args = [];
		$args['date']    = $app->input->get('date', '', 'string');
		$args['hourmin'] = $app->input->get('hourmin', '', 'string');
		$args['service'] = $app->input->get('service', null, 'string');

		$model = JModelVRE::getInstance('tkcart');

		// validate request
		if (!$model->checkIntegrity($args))
		{
			// fetch last error
			$app->enqueueMessage($model->getError($last = null, $string = true), 'error');

			// back to restaurants view in case of malformed request
			$app->redirect(JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeaway' . ($itemid ? '&Itemid=' . $itemid : ''), false));
			return false;
		}

		// inject date, time and service in INPUT for being used later
		$app->input->set('date', $args['date']);
		$app->input->set('hourmin', $args['hourmin']);
		$app->input->set('service', $args['service']);

		/**
		 * Remove all the take-away orders that haven't been confirmed
		 * within the specified range of time (15 minutes by default).
		 *
		 * In this way, we can free the slots that were occupied
		 * before showing the availability to this customer.
		 * 
		 * @since 1.8
		 */
		VikRestaurants::removeTakeAwayOrdersOutOfTime();
	}

	/**
	 * AJAX end-point to obtain a list of available working
	 * shifts for the given date and group (1: restaurant, 2: take-away).
	 *
	 * @return 	void
	 *
	 * @since 	1.5
	 */
	public function get_working_shifts()
	{
		$app = JFactory::getApplication();
		
		$date  = $app->input->get('date', '', 'string');
		$group = $app->input->get('group', 1, 'uint');
		
		$shifts = JHtml::fetch('vikrestaurants.times', $group, $date);

		$html = '';
		
		foreach ($shifts as $optgroup => $options)
		{
			if ($optgroup)
			{
				$html .= '<optgroup label="' . $optgroup . '">';
			}

			foreach ($options as $opt)
			{
				$html .= '<option value="' . $opt->value . '">' . $opt->text . '</option>';
			}

			if ($optgroup)
			{
				$html .= '</optgroup>';
			}
		}
		
		E4J\VikRestaurants\Http\Document::getInstance($app)->json(json_encode($html));
	}

	/**
	 * AJAX end-point to access the details form of a
	 * product that is going to be added into the cart.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	public function tkadditem()
	{
		$app = JFactory::getApplication();

		// add support for both view and task
		$app->input->set('view', 'tkadditem');

		// get JSON response in case of blank layout
		if ($app->input->get('tmpl') == 'component')
		{
			// start output buffer
			ob_start();
			try
			{
				// display view
				parent::display();
			}
			catch (Exception $e)
			{
				// clear output buffer
				ob_end_clean();
				// raise error
				E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
			}

			// obtain view HTML from buffer
			$html = ob_get_contents();
			// clear output buffer
			ob_end_clean();

			// encode HTML in JSON to avoid encoding issues
			E4J\VikRestaurants\Http\Document::getInstance($app)->json(json_encode($html));
		}
	}

	/**
	 * Completes the cancellation of the specified
	 * restaurant reservation.
	 *
	 * @return 	void
	 *
	 * @since 1.8
	 * @deprecated 1.10  Use "reservation.cancel" task instead.
	 */
	public function cancel_reservation()
	{
		$app = JFactory::getApplication();
		
		$oid    = $app->input->getUint('oid', '');
		$sid    = $app->input->getAlnum('sid', '');
		$reason = trim($app->input->getString('reason', ''));
		$itemid = $app->input->getUint('Itemid');

		$url = sprintf(
			'index.php?option=com_vikrestaurants&task=reservation.cancel&oid=%d&sid=%s%s%s',
			$oid,
			$sid,
			$reason ? '&reason=' . $reason : '',
			$itemid ? '&Itemid=' . $itemid : ''
		);

		$app->redirect(JRoute::rewrite($url, false));
	}

	/**
	 * Completes the cancellation of the specified
	 * take-away order.
	 *
	 * @return 	void
	 *
	 * @since 1.3
	 * @deprecated 1.10  Use "order.cancel" task instead.
	 */
	public function cancel_order()
	{
		$app = JFactory::getApplication();
		
		$oid    = $app->input->getUint('oid', '');
		$sid    = $app->input->getAlnum('sid', '');
		$reason = trim($app->input->getString('reason', ''));
		$itemid = $app->input->getUint('Itemid');

		$url = sprintf(
			'index.php?option=com_vikrestaurants&task=order.cancel&oid=%d&sid=%s%s%s',
			$oid,
			$sid,
			$reason ? '&reason=' . $reason : '',
			$itemid ? '&Itemid=' . $itemid : ''
		);

		$app->redirect(JRoute::rewrite($url, false));
	}
	
	/**
	 * Task used to confirm the requested reservation/order.
	 *
	 * @return 	void
	 *
	 * @since 1.3
	 * @deprecated 1.10  Use the "reservation.approve" or "order.approve" tasks instead.
	 */
	function confirmord()
	{
		$app = JFactory::getApplication();

		$id       = $app->input->getUint('oid');
		$conf_key = $app->input->getAlnum('conf_key');
		$group    = $app->input->getUint('tid');
		$itemid   = $app->input->getUint('Itemid');

		$url = sprintf(
			'index.php?option=com_vikrestaurants&task=%s.approve&oid=%d&conf_key=%s%s',
			$group === 0 ? 'reservation' : 'order',
			$id,
			$conf_key,
			$itemid ? '&Itemid=' . $itemid : ''
		);

		$app->redirect(JRoute::rewrite($url, false));
	}

	/**
	 * Task used to approve the requested review.
	 *
	 * @return 	void
	 *
	 * @since 	1.6
	 */
	function approve_review()
	{
		$input = JFactory::getApplication()->input;

		$id       = $input->getUint('id');
		$conf_key = $input->getAlnum('conf_key');
		
		if (empty($conf_key))
		{
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFREVIEWNOROWS') . '</div>';
			return;
		}

		// initialize review handler
		$handler = new ReviewsHandler();

		try
		{
			// obtain the product review
			$review = $handler->takeaway()->getReview($id, array('conf_key' => $conf_key));
		}
		catch (Exception $e)
		{
			// review not found
			echo '<div class="vr-confirmpage order-error">' . JText::translate('VRCONFREVIEWNOROWS') . '</div>';
			return;
		}
		
		if ($review->published)
		{
			// review already approved
			echo '<div class="vr-confirmpage order-notice">' . JText::translate('VRCONFREVIEWISCONFIRMED') . '</div>';
			return;
		}
		
		// get review table
		JTableVRE::addIncludePath(VREADMIN . DIRECTORY_SEPARATOR . 'tables');
		$reviewTable = JTableVRE::getInstance('review', 'VRETable');

		$data = array(
			'id'        => $review->id,
			'published' => 1,
		);

		// approve review
		$reviewTable->save($data);
		
		echo '<div class="vr-confirmpage order-good">' . JText::translate('VRCONFREVIEWCOMPLETED') . '</div>';
	}

	/**
	 * ##########################
	 * #     APIs End-Point     #
	 * ##########################
	 * 
	 * This function is the end-point to dispatch events requested from external connections.
	 * It is required to specify all the following values:
	 *
	 * @param   string  username  The username for login.
	 * @param   string  password  The password for login.
	 * @param   string  event     The name of the event to dispatch.
	 * 
	 * It is also possible to pre-send certain arguments to dispatch within the event:
	 *
	 * @param   array  args  The arguments of the event (optional).
	 *                       All the specified values are cleansed with string filtering.
	 *
	 * @return  string  In case of error it is returned a JSON string with the code (errcode) 
	 *                  and the message of the error (error).
	 *                  In case of success the result may vary on the event dispatched.
	 *
	 * @since   1.7
	 * @since   1.9 Renamed from apis().
	 */
	public function api()
	{
		$app = JFactory::getApplication();

		/** @var E4J\VikRestaurants\API\API */
		$api = VREFactory::getAPI();

		// check if the API framework is enabled
		if (!$api->isEnabled())
		{
			/**
			 * Use a HTTP response in place of the JSON one.
			 *
			 * @since 1.8.4
			 */
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, 'API Framework is disabled');
		}

		// flush stored API logs
		JModelVRE::getInstance('apilog')->flush();

		// create a BASIC AUTH login for this user
		$login = new E4J\VikRestaurants\API\Framework\BasicAuthUser(
			$app->input->getString('username'),
			$app->input->getString('password'),
			$app->input->server->getString('REMOTE_ADDR')
		);

		// do login
		if (!$api->connect($login))
		{
			// user is not authorized to login
			$api->output($api->getError());

			// terminate the request
			$app->close();
		}

		// get event to dispatch
		$event = $app->input->get('event');

		/**
		 * Try to retrieve the plugin arguments from JSON body.
		 *
		 * @since 1.8.3
		 */
		$args = $app->input->json->getArray();

		if (!$args)
		{
			// arguments not found, try to retrieve them from the request
			$args = $app->input->get('args', [], 'array');
		}

		// user correctly logged in, dispatch the event
		$result = $api->trigger($event, $args);

		// always disconnect the user
		$api->disconnect();

		if (!$result)
		{
			// event error thrown
			$api->output($api->getError());
		}

		// terminate the request
		$app->close();
	}

	/**
	 * Proxy for `api()`. Keep it for backward compatibility.
	 *
	 * @see api()
	 * @since 1.7
	 * @deprecated 1.11  Use api() instead.
	 */
	function apis()
	{
		$this->api();
	}
}
