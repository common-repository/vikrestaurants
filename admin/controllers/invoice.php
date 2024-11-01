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
 * VikRestaurants invoice controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerInvoice extends VREControllerAdmin
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
		$month = $app->input->getUint('month');
		$year  = $app->input->getUint('year');

		if (!is_null($group))
		{
			$data['group'] = $group;
		}

		if ($month)
		{
			$data['month'] = $month;
		}

		if ($year)
		{
			$data['year'] = $year;
		}

		// unset user state for being recovered again
		$app->setUserState('vre.invoice.data', $data);

		// check user permissions
		if (!$user->authorise('core.create', 'com_vikrestaurants') || !$user->authorise('core.access.invoices', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$this->setRedirect('index.php?option=com_vikrestaurants&view=manageinvoice');

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
		$app->setUserState('vre.invoice.data', []);

		// check user permissions
		if (!$user->authorise('core.edit', 'com_vikrestaurants') || !$user->authorise('core.access.invoices', 'com_vikrestaurants'))
		{
			// back to main list, not authorised to edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		$cid = $app->input->getUint('cid', [0]);

		$this->setRedirect('index.php?option=com_vikrestaurants&view=manageinvoice&cid[]=' . $cid[0]);

		return true;
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

			$url = 'index.php?option=com_vikrestaurants&task=invoice.add';

			// recover data from request
			$group = $input->getUint('group');
			$month = $input->getUint('month');
			$year  = $input->getUint('year');

			if (!is_null($group))
			{
				$url .= '&group=' . $group;
			}

			if ($month)
			{
				$url .= '&month=' . $month;
			}

			if ($year)
			{
				$url .= '&year=' . $year;
			}

			$this->setRedirect($url);
		}
	}

	/**
	 * Task used to save the record data set in the request.
	 * After saving, the user is redirected to the management
	 * page of the record that has been saved.
	 *
	 * @param 	array  $data  An array of invoice parameters to be used for the generation.
	 * 						  If not specified, the parameters in the request will be used.
	 *
	 * @return 	bool
	 */
	public function save(array $data = [])
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

		if ($data)
		{
			$args = $data;
		}
		else
		{
			$args['id']        = $input->get('id', 0, 'uint');
			$args['group']     = $input->get('group', 0, 'string');
			$args['overwrite'] = $input->get('overwrite', 0, 'uint');
			$args['notify']    = $input->get('notifycust', 0, 'uint');
			$args['month']     = $input->get('month', 1, 'uint');
			$args['year']      = $input->get('year', 0, 'uint');
			$args['cid']       = $input->get('cid', [], 'uint');

			$args['params'] = [];
			$args['params']['number']    = $input->get('number', [], 'string');
			$args['params']['suffix']    = $input->get('suffix', [], 'string');
			$args['params']['datetype']  = $input->get('datetype', 0, 'uint');
			$args['params']['date']      = $input->get('date', null, 'string');
			$args['params']['legalinfo'] = $input->get('legalinfo', '', 'string');

			if ($args['params']['date'])
			{
				$args['params']['date'] = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($args['params']['date']);
			}

			// settings
			$args['constraints']['pageOrientation'] = $input->get('pageorientation', '', 'string');
			$args['constraints']['pageFormat']      = $input->get('pageformat', '', 'string');
			$args['constraints']['unit']            = $input->get('unit', '', 'string');
			$args['constraints']['imageScaleRatio'] = abs($input->get('scale', 100, 'float')) / 100;

			// layout
			$args['constraints']['font']        = $input->get('font', 'courier', 'string');
			$args['constraints']['fontSizes']   = $input->get('fontsizes', [], 'array');
			$args['constraints']['headerTitle'] = '';
			$args['constraints']['showFooter']  = $input->get('showfooter', false, 'bool');

			if ($input->getBool('showheader'))
			{
				$args['constraints']['headerTitle'] = $input->get('headertitle', '', 'string');
			}

			// margins
			$args['constraints']['margins'] = $input->get('margins', [], 'array');
		}

		$rule = 'core.' . ($args['id'] > 0 ? 'edit' : 'create');

		// check user permissions
		if (!$user->authorise($rule, 'com_vikrestaurants'))
		{
			// back to main list, not authorised to create/edit records
			$app->enqueueMessage(JText::translate('JERROR_ALERTNOAUTHOR'), 'error');
			$this->cancel();

			return false;
		}

		// get invoice model
		$model = $this->getModel();

		// update existing invoice
		if ($args['id'])
		{
			// try to save arguments
			if (!$model->save($args))
			{
				// get string error
				$error = $model->getError(null, true);

				// display error message
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');

				$url = 'index.php?option=com_vikrestaurants&view=manageinvoice&cid[]=' . $args['id'];

				// redirect to edit page
				$this->setRedirect($url);
					
				return false;
			}

			// display generic successful message
			$app->enqueueMessage(JText::plural('VRINVGENERATEDMSG', 1));

			if ($model->isNotified())
			{
				// invoice notified, display message
				$app->enqueueMessage(JText::plural('VRINVMAILSENT', 1));
			}
		}
		// invoices mass creation
		else
		{
			// mass-save the matching records
			$result = $model->saveMass($args);

			if ($result['generated'])
			{
				// display number of generated invoices
				$app->enqueueMessage(JText::plural('VRINVGENERATEDMSG', $result['generated']));

				if ($result['notified'])
				{
					// display number of notified customers
					$app->enqueueMessage(JText::plural('VRINVMAILSENT', $result['notified']));
				}
			}
			else
			{
				// no generated invoices
				$app->enqueueMessage(JText::translate('VRNOINVOICESGENERATED'), 'warning');

				// save invoice data to keep changed settings
				$model->createGenerator($args)->save();
			}
		}

		// always redirect to invoices list when generating the invoices
		$this->cancel();

		return true;
	}

	/**
	 * Generates an invoice for the specified reservations.
	 *
	 * @return 	boolean
	 */
	public function generate()
	{
		$input = JFactory::getApplication()->input;

		// create array with required attributes
		$data = [];
		$data['id']     = 0;
		$data['cid']    = $input->get('cid', [], 'uint');
		$data['group']  = $input->get('group', 0, 'uint');
		$data['notify'] = $input->get('notifycust', 0, 'uint');

		// generate invoice
		return $this->save($data);
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
		if (!$user->authorise('core.delete', 'com_vikrestaurants') || !$user->authorise('core.access.invoices', 'com_vikrestaurants'))
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
		$input = JFactory::getApplication()->input;

		$group = $input->get('group', null, 'uint');
		$year  = $input->get('year', 0, 'uint');
		$month = $input->get('month', 0, 'uint');

		$url = 'index.php?option=com_vikrestaurants&view=invoices';

		if (!is_null($group))
		{
			$url .= '&group=' . $group;
		}

		if ($year)
		{
			$url .= '&year=' . $year;
		}

		if ($month)
		{
			$url .= '&month=' . $month;
		}

		$this->setRedirect($url);
	}

	/**
	 * Downloads one or more selected invoices.
	 * In case of single selection, the invoice will be
	 * directly downloaded in PDF format. Otherwise a
	 * ZIP archive will be given.
	 *
	 * @return 	void
	 */
	public function download()
	{
		$app = JFactory::getApplication();

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

		$ids = $app->input->get('cid', [], 'uint');

		if (!$ids)
		{
			// no IDs provided, download by month
			$ids = $app->input->get('range', null, 'string');
		}

		// get invoice model
		$model = $this->getModel();

		// get path to download
		$path = $model->download($ids);

		if (!$path)
		{
			// retrieve fetched error message
			$error = $model->getError();

			if ($error)
			{
				// raise error
				$app->enqueueMessage($error, 'error');
			}
			else
			{
				// no error fetched, probably the list of IDs was empty
				$app->enqueueMessage(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 'warning');
			}

			// back to main list
			$this->cancel();

			// do not go ahead
			return true;
		}

		$unlink = false;

		$app->setHeader('Content-Disposition', 'attachment; filename=' . basename($path));
		$app->setHeader('Content-Length', filesize($path));

		// check if we have a PDF file or a ZIP archive
		if (preg_match("/\.pdf$/", $path))
		{
			// download PDF file
			$app->setHeader('Content-Type', 'application/pdf');
		}
		else
		{
			$app->setHeader('Content-Type', 'application/zip');
			$unlink = true;
		}

		$app->sendHeaders();

		// use fopen to properly download large files
		$handle = fopen($path, 'rb');

		// read 1MB per cycle
		$chunk_size = 1024 * 1024;

		while (!feof($handle))
		{
			echo fread($handle, $chunk_size);
			ob_flush();
			flush();
		}

		fclose($handle);

		if ($unlink)
		{
			// delete package once its contents have been buffered
			unlink($path);
		}

		// break process to complete download
		$app->close();
	}
}
