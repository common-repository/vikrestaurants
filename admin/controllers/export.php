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
 * VikRestaurants export datasheet controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerExport extends VREControllerAdmin
{
	/**
	 * Converts and downloads the datasheet into the selected format.
	 *
	 * @return 	void
	 */
	public function download()
	{
		$app = JFactory::getApplication();

		$type = $app->input->get('type', '');

		// propagate the list of selected records
		$cid = $app->input->getString('cid', []);

		// should export raw data?
		$raw = $app->input->getBool('raw', false);

		/** @var E4J\VikRestaurants\DataSheet\DataSheetFactory */
		$factory = VREFactory::getDataSheetFactory();

		/** @var E4J\VikRestaurants\DataSheet\DataSheet */
		$datasheet = $factory->getDataSheet($type, [
			'raw' => $raw,
			'cid' => $cid,
		]);

		// retrieve the file name to use for the download
		$filename = $app->input->getString('filename');

		// this is the list containing the columns to display
		$columns = $app->input->getUint('columns', []);

		// wrap the instance into an editable datasheet
		$datasheet = new E4J\VikRestaurants\DataSheet\EditableDataSheet($datasheet, $columns);
		$datasheet->setTitle($filename);
		
		// fetch the export driver
		$driverType = $app->input->getString('driver', '');

		// fetch the export driver configuration
		$args = $app->input->get('args', [], 'array');

		// create export driver
		$driver = $factory->getExportDriver($driverType, $args);

		// attempt to export and download the datasheet
		$driver->download($datasheet);

		// update configuration for this export driver only
		VREFactory::getConfig()->set('export_config_' . $driverType, $args);

		// terminate request
		$app->close();
	}

	/**
	 * Returns a form including the parameters of the requested export driver.
	 *
	 * @return 	void
	 */
	public function params()
	{
		$app = JFactory::getApplication();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		$driverType = $app->input->getString('driver');

		try
		{
			$driver = VREFactory::getDataSheetFactory()->getExportDriver($driverType);
		}
		catch (Exception $e)
		{
			// an error has occurred
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode() ?: 500, $e->getMessage());
		}

		$fields = [];

		if ($driver instanceof E4J\VikRestaurants\DataSheet\ConfigurableDriver)
		{
			$fields = $driver->getForm();
		}

		if ($fields)
		{
			$html = JLayoutHelper::render('form.fields', [
				'fields' => $fields,
				'params' => VREFactory::getConfig()->getArray('export_config_' . $driverType, []),
			]);
		}
		else
		{
			$html = '';
		}

		// send form to caller
		$this->sendJSON(json_encode($html));
	}
}
