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
 * VikRestaurants quick reservation (module) controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerQuickres extends VREControllerAdmin
{
	/**
	 * AJAX end-point used to retrieve a list of tables
	 * available for the searched arguments.
	 *
	 * @return 	void
	 */
	public function findtable()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$params = $this->getModuleParams();

		// get session lifetime specified in module configuration
		$session_lifetime = $params->get('session_lifetime', 15) * 60;
		
		// retrieve from session the last booking made
		$user_session = (int) $app->getUserState('vre.quickres.session', 0);

		// make sure the difference between the current time and the last booking time
		// if greater than the lifetime threshold
		if (!empty($user_session) && time() - $session_lifetime < $user_session)
		{
			// too many attempts
			$error = JText::sprintf('VRQRMOD_SPAMATTEMPT', ceil(($session_lifetime - (time() - $user_session)) / 60));
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, $error);
		}

		// get search arguments
		$args = [];
		$args['date']    = $app->input->get('date', '', 'string');
		$args['hourmin'] = $app->input->get('hourmin', '', 'string');
		$args['people']  = $app->input->get('people', 0, 'uint');

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

		/**
		 * Reuse the confirm reservation model to validate the search data.
		 * 
		 * @since 1.9
		 */
		$model = JModelVRE::getInstance('rescart');
		
		// validate request
		if (!$model->checkIntegrity($args))
		{
			// fetch last error
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}
		
		/**
		 * Remove all the reservations that haven't been confirmed
		 * within the specified range of time (15 minutes by default).
		 *
		 * In this way, we can free the tables that were occupied
		 * before showing the availability to this customer.
		 */
		VikRestaurants::removeRestaurantReservationsOutOfTime();

		/**
		 * Look for COVID19 prevention measures.
		 *
		 * @see COVID-19
		 */
		$people = VikRestaurants::getPeopleSafeDistance($args['people']);

		// instantiate availability search
		$search = new VREAvailabilitySearch($args['date'], $args['hourmin'], $people);

		// get all the tables available for the specified search arguments
		$tables = $search->getAvailableTables();

		if (!$tables)
		{
			// No available tables, elaborate time hints.
			// The method will return the first 2 available times before
			// the selected check-in time and the next 2. Such as:
			// 12:00 | 12:30 | CURRENT | 13:30 | 14:30
			// It is possible to pass a number to the function below
			// to increase/decrease the number of suggested times.
			$hints = $search->getSuggestedTimes();

			// make sure we have at least a valid hint
			if (!array_filter($hints))
			{
				// no available table for the selected date and time
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(404, JText::translate('VRRESNOSINGTABLEFOUND'));
			}

			// return hints list
			$this->sendJSON([-1, $hints]);
		}

		$config = VREFactory::getConfig();

		// create check-in timestamp
		$ts = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($args['date'] . ' ' . $args['hourmin']);

		// build summary text
		$date_str = JText::sprintf(
			'VRQRMOD_DATETIMESTR',
			date($config->get('dateformat'), $ts),
			date($config->get('timeformat'), $ts),
			$args['people']
		);

		// find all available rooms
		$rooms = [];

		foreach ($tables as $t)
		{
			if (!isset($rooms[$t->id_room]))
			{
				$rm = new stdClass;
				$rm->id   = $t->id_room;
				$rm->name = $t->room_name;
				// assign first available table too
				$rm->tid = $t->id;

				$rooms[$t->id_room] = $rm;
			}
		}

		// translate rooms
		VikRestaurants::translateRooms($rooms);

		$table = [
			'rid' => $tables[0]->id_room,
			'tid' => $tables[0]->id,
		];
		
		$app->setUserState('vre.quickres.reservation', [
			'args'  => $args,
			'rooms' => $rooms,
			'table' => $table,
		]);

		// do not preserve keys in JSON
		$rooms = array_values($rooms);

		if (count($rooms) == 1)
		{
			// auto-select the only one available
			$rooms[0]->str = JText::sprintf('VRQRMOD_ROOMSELSTR', $rooms[0]->name); 
		}
		
		$this->sendJSON([1, $date_str, $rooms]);
	}

	/**
	 * AJAX end-point used to pick a room.
	 *
	 * @return 	void
	 */
	public function selectroom()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// retrieve last search
		$search = $app->getUserState('vre.quickres.reservation', null);
		
		if (!$search)
		{
			// raise error, search not started yet
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get room ID
		$id_room = $app->input->getInt('id_room');

		if (!isset($search['rooms'][$id_room]))
		{
			// missing selection
			$this->sendJSON([0]);
		}

		$room = $search['rooms'][$id_room];

		$search['table']['rid'] = $room->id;
		$search['table']['tid'] = $room->tid;

		$app->setUserState('vre.quickres.reservation', $search);

		$this->sendJSON([1, JText::sprintf('VRQRMOD_ROOMSELSTR', $room->name)]);
	}

	/**
	 * AJAX end-point used to save a reservation.
	 *
	 * @return  void
	 */
	public function save()
	{
		$app = JFactory::getApplication();
		
		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// retrieve last search
		$search = $app->getUserState('vre.quickres.reservation', null);
		
		if (!$search)
		{
			// raise error, search not started yet
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get module parameters
		$params = $this->getModuleParams();

		$vik = VREApplication::getInstance();

		/**
		 * Validate ReCaptcha before saving the reservation.
		 * The ReCaptcha must be enabled globally and from the
		 * configuration of the module.
		 *
		 * @since 1.8.2
		 */
		if ($vik->isGlobalCaptcha() && $params->get('recaptcha') && !$vik->reCaptcha('check'))
		{
			// invalid captcha
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(400, JText::translate('PLG_RECAPTCHA_ERROR_EMPTY_SOLUTION'));
		}

		// obtain searched arguments
		$args = $search['args'];

		// register selected table
		$args['table'] = $search['table']['tid'];

		// inject module Item ID
		$args['itemid'] = $params->get('itemid');

		/**
		 * Reuse the confirm reservation model to validate the search data.
		 * 
		 * @since 1.9
		 */
		$model = JModelVRE::getInstance('confirmres');

		// try to save the reservation and get landing page
		$url = $model->save($args);

		// make sure we haven't faced any errors		
		if (!$url)
		{
			// fetch last error
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		// fetch reservation saved data
		$data = $model->getData();

		// fetch fields summary
		$custom_fields_summary = [
			$data['purchaser_nominative'],
			$data['purchaser_mail'],
			$data['purchaser_phone'],
		];

		$custom_fields_summary = implode(' ', array_filter($custom_fields_summary));
		
		// unset reservation details
		$app->setUserState('vre.quickres.reservation', null);
		// register last booking session
		$app->setUserState('vre.quickres.session', time());
		
		$this->sendJSON([1, $custom_fields_summary, $url]);
	}

	/**
	 * Returns the parameters of Quick Reservation module
	 * published on the currect Item ID.
	 *
	 * @return  JRegistry  The module parameters.
	 *
	 * @since   1.8.2
	 */
	protected function getModuleParams()
	{
		jimport('joomla.application.module.helper');
		$module = JModuleHelper::getModule('mod_vikrestaurants_quickres');

		return new JRegistry(json_decode($module->params));
	}
}
