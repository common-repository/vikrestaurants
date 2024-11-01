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

use E4J\VikRestaurants\DataSheet\ConfigurableDriver;
use E4J\VikRestaurants\DataSheet\DataSheet;
use E4J\VikRestaurants\DataSheet\Export\ExportDriver;

/**
 * Exports a datasheet in CSV format.
 * 
 * @since 1.9
 */
class CSVExportDriver implements ExportDriver, ConfigurableDriver
{
    /**
     * The driver configuration registry.
     * 
     * @var \JRegistry
     */
    protected $options;

    /**
     * The application used to set up the download headers.
     * 
     * @var \JApplicationCms
     */
    protected $app;

    /**
     * Class constructor.
     * 
     * @param  array|object  $options  The driver configuration.
     */
    public function __construct($options = [], $app = null)
    {
        $this->options = new \JRegistry($options);

        if ($app)
        {
            $this->app = $app;
        }
        else
        {
            $this->app = \JFactory::getApplication();
        }
    }

    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getName()
    {
        return 'CSV';
    }

    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getDescription()
    {
        return \JText::translate('VRE_ORDER_EXPORT_DRIVER_CSV_DESC');
    }

    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getForm()
    {
        return [
            /**
             * The separator character that will be used to separate
             * the value of the columns.
             *
             * @var select
             */
            'delimiter' => [
                'type'    => 'select',
                'name'    => 'args[delimiter]',
                'label'   => \JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD'),
                'help'    => \JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_HELP'),
                'default' => ',',
                'options' => [
                    ',' => \JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_OPT_COMMA'),
                    ';' => \JText::translate('VRE_EXPORT_DRIVER_CSV_DELIMITER_FIELD_OPT_SEMICOLON'),
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
                'name'    => 'args[enclosure]',
                'label'   => \JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD'),
                'help'    => \JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_HELP'),
                'default' => '"',
                'options' => [
                    '"'  => \JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_OPT_DOUBLE_QUOTE'),
                    '\'' => \JText::translate('VRE_EXPORT_DRIVER_CSV_ENCLOSURE_FIELD_OPT_SINGLE_QUOTE'),
                ],
            ],
        ];
    }

	/**
	 * @inheritDoc
     * 
     * @see ExportDriver
	 */
	public function generate(DataSheet $dataSheet)
    {
        // start catching output buffer
        ob_start();

        // open file resource pointing to PHP OUTPUT
        $handle = fopen('php://output', 'w');
        
        // output CSV to the given resource
        $this->output($dataSheet, $handle);

        // catch buffer
        $buffer = ob_get_contents();
        
        // close resource
        fclose($handle);

        // close output buffer
        ob_end_clean();

        // strip trailing new line and return CSV string
        return trim($buffer, "\n");
    }

    /**
     * @inheritDoc
     * 
     * @see ExportDriver
     */
    public function download(DataSheet $dataSheet)
    {
        // use the datasheet title
        $filename = $dataSheet->getTitle();

        if (!$filename)
        {
            // use current date time as name
            $filename = \JHtml::fetch('date', 'now', 'Y-m-d H-i-s');   
        }

        // make file safe
        $filename = \JFilterOutput::stringURLSafe($filename);

        // prepare headers
        $this->prepareDownload($filename);

        // send headers
        $this->app->sendHeaders();

        // open file resource pointing to PHP OUTPUT
        $handle = fopen('php://output', 'w');
        
        // output CSV to the given resource
        $this->output($dataSheet, $handle);
        
        // close resource
        fclose($handle);
    }

    /**
     * Prepares the application headers to start the download.
     *
     * @param   string  $filename  The name of the file that will be downloaded.
     *
     * @return  void
     */
    protected function prepareDownload(string $filename)
    {
        // prepare headers
        $this->app->setHeader('Cache-Control', 'no-store, no-cache');
        $this->app->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->app->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }

    /**
     * Generates the CSV structure by putting the fetched
     * bytes into the specified resource.
     *
     * @param   DataSheet  $dataSheet  The datasheet to export.
     * @param   mixed      $handle     The resource pointer created with fopen().
     *
     * @return  void
     */
    protected function output(DataSheet $dataSheet, $handle)
    {
        if (!$handle)
        {
            throw new \RuntimeException('Invalid resource for CSV generation');
        }

        // retrieve settings
        $delimiter = $this->options->get('delimiter', ',');
        $enclosure = $this->options->get('enclosure', '"');

        // make sure the head exists
        if ($head = $dataSheet->getHead())
        {
            // put head within the CSV
            $this->putRow($handle, $head, $delimiter, $enclosure);
        }

        // iterate all the body rows
        foreach ($dataSheet->getBody() as $row)
        {
            // put body row within the CSV
            $this->putRow($handle, $row, $delimiter, $enclosure);
        }

        // make sure the footer exists
        if ($footer = $dataSheet->getFooter())
        {
            // put footer within the CSV
            $this->putRow($handle, $footer, $delimiter, $enclosure);
        }
    }

    /**
     * Inserts the row within the CSV file.
     *
     * @param   mixed        $handle     The resource pointer created with fopen().
     * @param   array        $row        The row to include.
     * @param   string|null  $delimiter  The delimiter used to separate the columns
     * @param   string|null  $enclosure  The enclosure used to wrap the values.
     * 
     * @return  void
     */
    protected function putRow($handle, array $row, string $delimiter = null, string $enclosure = null)
    {
        fputcsv($handle, $row, $delimiter, $enclosure);
    }
}
