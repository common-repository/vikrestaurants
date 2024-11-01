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

/**
 * Driver class used to export the take-away orders and the
 * restaurant reservations in CSV format.
 *
 * @since 1.8
 */
class VREOrderExportDriverCsv extends VREOrderExportDriver
{
	/** @var E4J\VikRestaurants\DataSheet\DataSheet */
	protected $datasheet;

	/** @var E4J\VikRestaurants\DataSheet\Export\ExportDriver */
	protected $driver;

	/**
	 * @inheritDoc
	 */
	protected function buildForm()
	{
		return [
			/**
			 * Choose whether only the confirmed reservations
			 * will be retrieved. Closures are never retrieved
			 * even this option is turned off.
			 *
			 * @var checkbox
			 */
			'confirmed' => [
				'type'    => 'checkbox',
				'label'   => JText::translate('VRE_EXPORT_DRIVER_CSV_CONFIRMED_STATUS_FIELD'),
				'help'    => JText::translate('VRE_EXPORT_DRIVER_CSV_CONFIRMED_STATUS_FIELD_HELP'),
				'default' => 1,
			],

			/**
			 * Choose whether the reservations items should be
			 * retrieved and included within the CSV.
			 *
			 * @var checkbox
			 */
			'useitems' => [
				'type'    => 'checkbox',
				'label'   => JText::translate('VRE_EXPORT_DRIVER_CSV_USE_ITEMS_FIELD'),
				'help'    => JText::translate('VRE_EXPORT_DRIVER_CSV_USE_ITEMS_FIELD_HELP'),
				'default' => 0,
			],

			/**
			 * The separator character that will be used to separate
			 * the value of the columns.
			 *
			 * @var select
			 */
			'delimiter' => [
				'type'    => 'select',
				'label'   => JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD'),
				'help'    => JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_HELP'),
				'default' => ',',
				'options' => [
					',' => JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_OPT_COMMA'),
					';' => JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_OPT_SEMICOLON'),
				],
			],

			/**
			 * The enclosure character that will be used to wrap,
			 * and escape, the value of the columns.
			 *
			 * @var select
			 */
			'enclosure' => [
				'type'    => 'select',
				'label'   => JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD'),
				'help'    => JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_HELP'),
				'default' => '"',
				'options' => [
					'"'  => JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_OPT_DOUBLE_QUOTE'),
					'\'' => JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_OPT_SINGLE_QUOTE'),
				],
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function export()
	{
		return $this->getDriver()->generate($this->getDatasheet());
	}

	/**
	 * @inheritDoc
	 */
	public function download($filename = null)
	{
		if ($filename)
		{
			// strip file extension
			$this->getDatasheet()->setTitle(preg_replace("/\.csv$/i", '', (string) $filename));
		}

		return $this->getDriver()->download($this->getDatasheet());
	}

	/**
	 * Returns the real driver used to export the datasheet.
	 * 
	 * @return  E4J\VikRestaurants\DataSheet\Export\ExportDriver
	 */
	protected function getDriver()
	{
		if ($this->driver === null)
		{
			// create CSV export driver
			$this->driver = new E4J\VikRestaurants\DataSheet\Export\Drivers\CSVExportDriver([
				'delimiter' => $this->getOption('delimiter'),
				'enclosure' => $this->getOption('enclosure'),
			]);
		}

		return $this->driver;
	}

	/**
	 * Returns the datasheet holding the data to export.
	 * 
	 * @return  E4J\VikRestaurants\DataSheet\DataSheet
	 */
	protected function getDatasheet()
	{
		if ($this->datasheet === null)
		{
			// construct datasheet configuration
			$options = [
				// system
				'fromdate' => $this->getOption('fromdate'),
				'todate'   => $this->getOption('todate'),
				'cid'      => $this->getOption('cid'),
				'raw'      => false,
				// datasheet
				'confirmed' => $this->getOption('confirmed'),
				'useitems'  => $this->getOption('useitems'),
			];

			if ($this->isGroup('restaurant'))
			{
				// create restaurant reservations datasheet
				$this->datasheet = new E4J\VikRestaurants\DataSheet\Export\Models\RestaurantReservationsDataSheet($options);
			}
			else
			{
				// create take-away orders datasheet
				$this->datasheet = new E4J\VikRestaurants\DataSheet\Export\Models\TakeAwayOrdersDataSheet($options);
			}

			// make datasheet editable
			$this->datasheet = new E4J\VikRestaurants\DataSheet\EditableDataSheet($this->datasheet);
		}

		return $this->datasheet;
	}
}
