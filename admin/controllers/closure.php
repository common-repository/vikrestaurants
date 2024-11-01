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
 * VikRestaurants closure controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerClosure extends VREControllerAdmin
{
	/**
	 * Task used to save a reservation closure.
	 *
	 * @param 	boolean  $ajax  True if the request has been made via AJAX.
	 *
	 * @return 	boolean
	 */
	public function save($ajax = false)
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			if ($ajax)
			{
				// raise error
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
			}
			else
			{
				// back to main list, missing CSRF-proof token
				$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
				$this->cancel();

				return false;
			}
		}
		
		$args = [];
		$args['id'] = $app->input->get('id', 0, 'uint');

		if ($app->input->getBool('reopen'))
		{
			// permanently delete closure in case "RE-OPEN" checkbox was checked
			$this->getModel()->reopen($args['id']);
			$this->cancel();
			return true;
		}

		// load closure data from request
		$args['date']      = $app->input->get('date', '', 'string');
		$args['hourmin']   = $app->input->get('hourmin', '', 'string');
		$args['hour']	   = $app->input->get('hour', '', 'string');
		$args['min']	   = $app->input->get('min', '', 'string');
		$args['id_table']  = $app->input->get('id_table', 0, 'string');
		$args['notes']     = $app->input->get('notes', '', 'raw');
		$args['stay_time'] = $app->input->get('stay_time', 0, 'uint');

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants') || !$user->authorise('core.access.reservations', 'com_vikrestaurants'))
		{
			if ($ajax)
			{
				// raise error
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}
			else
			{
				// back to main list, not authorised to create/edit records
				$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
				$this->cancel();

				return false;
			}
		}

		// get record table
		$closure = $this->getModel();

		// try to save arguments
		$id = $closure->save($args);

		if (!$id)
		{
			// get string error
			$error = $closure->getError();

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			if ($ajax)
			{
				// raise error
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(
					$error->getCode(),
					JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage())
				);
			}
			else
			{
				// display error message
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error->getMessage()), 'error');
				$this->cancel();
				return false;
			}
		}

		if ($ajax)
		{
			$this->sendJSON($id);
		}

		$this->cancel();
		return true;
	}

	/**
	 * AJAX end-point used to save a reservation closure.
	 *
	 * @return 	void
	 */
	public function saveajax()
	{
		$this->save(true);
	}

	/**
	 * Redirects the users to the main records list.
	 *
	 * @return 	void
	 */
	public function cancel()
	{
		// recover incoming view
		$from = JFactory::getApplication()->input->get('from', null);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=' . ($from ? $from : 'reservations'));
	}
}
