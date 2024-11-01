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
 * VikRestaurants code hub controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerCodehub extends VREControllerAdmin
{
	/**
	 * Task used to save a list of code blocks.
	 *
	 * @return 	void
	 */
	public function save()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// fetch code blocks
		$filter = $app->input->get('filter', [], 'string');
		$blocks = $app->input->get('blocks', [], 'array');

		try
		{
			// attempt to save the code blocks
			VREFactory::getCodeHub()->save($blocks, $filter);
		}
		catch (Exception $e)
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode() ?: 500, $e->getMessage());
		}

		$this->sendJSON(1);
	}

	/**
	 * Generates a unique ID for the newly created code block.
	 * 
	 * @return  void
	 */
	public function generateid()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken())
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// fetch code block
		$block = $app->input->get('block', [], 'array');
		// wrap data in a code block
		$block = new E4J\VikRestaurants\CodeHub\CodeBlock($block);

		// send identifier to caller
		$this->sendJSON(json_encode($block->getID()));
	}

	/**
	 * Imports the selected code block from .json format.
	 * 
	 * @return  void
	 */
	public function import()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// fetch temporary folder
		$path = $app->get('tmp_path', VRE_CUSTOM_CODE_FOLDER);

		// upload file data
		$resp = VikRestaurants::uploadFile('file', $path, 'json', $overwrite = false);

		if (!$resp->status)
		{
			// unable to upload the image, abort
			if ($resp->errno == 1)
			{
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::sprintf('VRCONFIGFILETYPEERRORWHO', $resp->mimeType));
			}
			else
			{
				E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, JText::translate('VRCONFIGUPLOADERROR'));
			}

			return false;
		}

		// load JSON data from uploaded file
		$data = json_decode((string) file_get_contents($resp->path));

		// remove uploaded file
		JFile::delete($resp->path);

		if (!$data)
		{
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(500, 'Malformed JSON file provided');
		}

		// wrap data in a code block
		$block = new E4J\VikRestaurants\CodeHub\CodeBlock($data);

		// send code block to caller
		$this->sendJSON($block);
	}

	/**
	 * Exports (and downloads) the selected code block in .json format.
	 * 
	 * @return  void
	 */
	public function export()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get selected block code
		$id = $app->input->get('id', '', 'string');

		// load all the code blocks
		$blocks = VREFactory::getCodeHub()->load();

		foreach ($blocks as $block)
		{
			// search for a matching code block
			if ($block->getID() !== $id)
			{
				continue;
			}

			try
			{
				// download code block
				$this->downloadCodeBlock($block);
			}
			catch (Exception $e)
			{
				// an error has occurred
				E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
			}

			// terminate the session
			$app->close();
		}

		// code block not found
		E4J\VikRestaurants\Http\Document::getInstance($app)->close(404, JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
	}

	/**
	 * Downloads the provided code block in .json format.
	 * 
	 * @param   CodeBlock  $block
	 * 
	 * @return  void
	 */
	private function downloadCodeBlock($block)
	{
		$app = JFactory::getApplication();

		// fetch temporary folder
		$path = $app->get('tmp_path', VRE_CUSTOM_CODE_FOLDER);

		// set up tmp file name
		$path = JPath::clean($path . '/' . $block->getID() . '.json');

		// save the code block manifest within the file
		$bytes = file_put_contents($path, json_encode($block, JSON_PRETTY_PRINT));

		if (!$bytes)
		{
			throw new Exception('Unable to write into: ' . $path, 500);
		}

		// prepare headers
		$app->setHeader('Cache-Control', 'no-store, no-cache');
		$app->setHeader('Content-Type', 'application/json; charset=UTF-8');
		$app->setHeader('Content-Disposition', 'attachment; filename="' . basename($path) . '"');
		$app->sendHeaders();

		// read file using a buffer
		$handle = fopen($path, 'r');

		while (!feof($handle))
		{
			echo fread($handle, 8192);
		}

		fclose($handle);

		// remove tmp file after download
		JFile::delete($path);
	}
}
