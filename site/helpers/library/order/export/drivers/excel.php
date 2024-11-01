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

VRELoader::import('library.order.export.drivers.csv');

/**
 * Driver class used to export the orders/reservations in a format
 * readable by Microsoft Excel. This object inherits the CSV class
 * because this driver just needs to encode the rows fetched to
 * build a CSV file.
 *
 * @since 1.8.5
 */
class VREOrderExportDriverExcel extends VREOrderExportDriverCSV
{
	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		// get parent configuration
		$form = parent::getForm();

		// unset delimiter and enclosure options because we
		// are going to use always the same ones
		unset($form['delimiter']);
		unset($form['enclosure']);

		return $form;
	}

	/**
	 * @inheritDoc
	 */
	protected function getDriver()
	{
		if ($this->driver === null)
		{
			// create CSV-Excel export driver
			$this->driver = new E4J\VikRestaurants\DataSheet\Export\Drivers\ExcelExportDriver;
		}

		return $this->driver;
	}
}
