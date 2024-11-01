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
 * VikRestaurants plugin Shortcode controller.
 *
 * @since 1.0
 */
class VikRestaurantsControllerShortcode extends VREControllerAdmin
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
			// get return URL
			$encoded = JFactory::getApplication()->input->getBase64('return', '');

			// back to the main list
			$this->setRedirect('admin.php?page=vikrestaurants&view=shortcodes' . ($encoded ? '&return=' . $encoded : ''));
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
			// get return URL
			$encoded = JFactory::getApplication()->input->getBase64('return', '');

			// go to new record
			$this->setRedirect('admin.php?page=vikrestaurants&task=shortcodes.add' . ($encoded ? '&return=' . $encoded : ''));
		}
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @return 	boolean
	 * 
	 * @since   1.3.2  This task now supports also AJAX requests.
	 */
	public function save()
	{
		$app = JFactory::getApplication();

		// get return URL
		$encoded = $app->input->getBase64('return', '');

		// set up redirect url in case of error
		$this->setRedirect('admin.php?page=vikrestaurants&view=shortcodes' . ($encoded ? '&return=' . $encoded : ''));

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			if (wp_doing_ajax())
			{
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
			}
			
			// missing CSRF token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}

		// make sure the user is authorised to change shortcodes
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			if (wp_doing_ajax())
			{
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
			}
			
			// not authorized
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		// get item from request
		$data = $this->model->getFormData();

		// dispatch model to save the item
		$id = $this->model->save($data);

		if (!$id)
		{
			// get string error
			$error = $this->model->getError(null, true);
			$error = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error);

			if (wp_doing_ajax())
			{
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, $error);
			}

			// display error message
			$app->enqueueMessage($error, 'error');

			$url = 'admin.php?page=vikrestaurants&view=shortcode';

			if ($data->id)
			{
				$url .= '&cid[]=' . $data->id;
			}

			// redirect to new/edit page
			$this->setRedirect($url);
				
			return false;
		}

		if (wp_doing_ajax())
		{
			$this->sendJSON($this->model->getItem($id));
		}

		// display generic successful message
		$app->enqueueMessage(JText::translate('JLIB_APPLICATION_SAVE_SUCCESS'));

		// save and stay in edit page
		$this->setRedirect('admin.php?page=vikrestaurants&task=shortcodes.edit&cid[]=' . $id . ($encoded ? '&return=' . $encoded : ''));

		return true;
	}

	/**
	 * AJAX end-point used to retrieve the parameters of the
	 * specified view/shortcode.
	 *
	 * @return 	void
	 */
	public function params()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		$id   = $app->input->getInt('id', 0);
		$type = $app->input->getString('type', '');

		// dispatch model to get the item (an empty ITEM if not exists)
		$item = $this->model->getItem($id);

		// inject the type to load the right form
		$item->type = $type;

		// obtain the type form
		$form = $this->model->getTypeForm($item);

		// if the form doesn't exist, the type is probably empty
		if (!$form)
		{
			// return an empty HTML
			$this->sendJSON(json_encode(''));
		}
		
		// render the form and encode the response
		$args = json_decode($item->json);
		$this->sendJSON(json_encode($form->renderForm($args)));
	}

	/**
	 * Creates a page on WordPress with the requested Shortcode inside it.
	 * This is useful to automatically link Shortcodes in pages with no manual actions.
	 *
	 * @return 	void
	 */
	public function addpage()
	{
		$app = JFactory::getApplication();

		// get return URL
		$encoded = $app->input->getBase64('return', '');

		// always back to shortcodes list
		$this->setRedirect('admin.php?page=vikrestaurants&view=shortcodes' . ($encoded ? '&return=' . $encoded : ''));
		
		/**
		 * Added token validation.
		 *
		 * @since 1.3
		 */
		if (!JSession::checkToken())
		{
			// missing CSRF token
			$app->enqueueMessage(JText::translate('JINVALID_TOKEN'), 'error');
			return false;
		}

		// make sure the user is authorised to change shortcodes
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants'))
		{
			// not authorized
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		// get selected shortcodes
		$cid = $app->input->getUint('cid', []);

		// attempt to assign the shortcodes to a page
		if ($this->model->addPage($cid))
		{
			// add success message and redirect
			$app->enqueueMessage(JText::translate('VRE_SHORTCODE_CREATE_PAGE_SUCCESS'));
		}

		// fetch all registered errors (if any)
		$errors = $this->model->getErrors();

		foreach ($errors as $error)
		{
			if ($error instanceof Exception)
			{
				$error = $error->getMessage();
			}

			// enqueue error message
			$app->enqueueMessage($error, 'error');
		}

		return true;
	}
}
