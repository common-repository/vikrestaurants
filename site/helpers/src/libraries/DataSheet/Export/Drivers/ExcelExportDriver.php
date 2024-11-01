<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Export\Drivers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DataSheet\DataSheet;

/**
 * Exports a datasheet in CSV format compatible with Microsoft Excel.
 * 
 * @since 1.9
 */
class ExcelExportDriver extends CSVExportDriver
{
    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getName()
    {
        return \JText::translate('VRE_ORDER_EXPORT_DRIVER_EXCEL');
    }

    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getDescription()
    {
        return \JText::translate('VRE_ORDER_EXPORT_DRIVER_EXCEL_DESC');
    }

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
    protected function prepareDownload(string $filename)
    {
        // prepare headers
        $this->app->setHeader('Cache-Control', 'no-store, no-cache');
        // Excel uses a different encoding (UTF-16LE) than CSV (UTF-8)
        $this->app->setHeader('Content-Type', 'text/csv; charset=UTF-16LE');
        $this->app->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }

    /**
     * @inheritDoc
     */
    protected function output(DataSheet $dataSheet, $handle)
    {
        if (!$handle)
        {
            throw new \RuntimeException('Invalid resource for Excel generation');
        }

        // UTF-8 BOM at the beginning of the file should not be needed,
        // since we already converted the whole contents
        // fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // insert role to inform Excel what's the character used to
        // separate the values (use a semicolon by default)
        fputs($handle, "sep=;\n");

        // output through parent method
        parent::output($dataSheet, $handle);
    }

    /**
     * @inheritDoc
     */
    protected function putRow($handle, $row, $delimiter = null, $enclosure = null)
    {
        // iterate all the cells to switch from UTF-8 encoding to
        // UTF-16LE encoding, which seems to be mandatory for Excel
        foreach ($row as $k => $v)
        {
            // Transliterate € symbol because MS Excel seems to have
            // problems with the UTF-16LE encoded version...
            $v = preg_replace("/€/", 'EUR', (string) $v);

            // switch encoding
            $row[$k] = mb_convert_encoding($v, 'UTF-16LE', 'UTF-8');
        }

        // put row through parent by forcing semicolon as separator
        // and double quotes as enclosure
        parent::putRow($handle, $row, ";", "\"");
    }
}
