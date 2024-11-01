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
use E4J\VikRestaurants\PDF\PDFConstraints;
use E4J\VikRestaurants\PDF\PDFFonts;

/**
 * Exports a datasheet in PDF format.
 * 
 * @since 1.9
 */
class PDFExportDriver extends HTMLExportDriver
{
    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getName()
    {
        return 'PDF';
    }

    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getDescription()
    {
        return \JText::translate('VRE_ORDER_EXPORT_DRIVER_PDF_DESC');
    }

    /**
     * @inheritDoc
     */
    public function getForm()
    {
        return array_merge([
            /**
             * The separator for the "Settings" section.
             * 
             * @var separator
             */
            'pdf_separator' => [
                'type'        => 'separator',
                'label'       => \JText::translate('VRE_SETTINGS_FIELDSET'),
                'hiddenLabel' => true,
            ],

            /**
             * The page orientation (landscape or portrait).
             *
             * @var select
             */
            'page_orientation' => [
                'type'    => 'select',
                'name'    => 'args[page_orientation]',
                'label'   => \JText::translate('VRMANAGEINVOICE8'),
                'default' => PDFConstraints::PAGE_ORIENTATION_LANDSCAPE,
                'options' => [
                    PDFConstraints::PAGE_ORIENTATION_PORTRAIT  => \JText::translate('VRINVOICEPAGEORIOPT1'),
                    PDFConstraints::PAGE_ORIENTATION_LANDSCAPE => \JText::translate('VRINVOICEPAGEORIOPT2'),
                ],
            ],

            /**
             * The page format (A4, A5 or A6).
             *
             * @var select
             */
            'page_format' => [
                'type'    => 'select',
                'name'    => 'args[page_format]',
                'label'   => \JText::translate('VRMANAGEINVOICE9'),
                'default' => PDFConstraints::PAGE_FORMAT_A4,
                'options' => [
                    PDFConstraints::PAGE_FORMAT_A4 => 'A4',
                    PDFConstraints::PAGE_FORMAT_A5 => 'A5',
                    PDFConstraints::PAGE_FORMAT_A6 => 'A6',
                ],
            ],

            /**
             * Whether the page should display the footer or not
             *
             * @var checkbox
             */
            'show_footer' => [
                'type'    => 'checkbox',
                'name'    => 'args[show_footer]',
                'label'   => \JText::translate('VRMANAGEINVOICE17'),
                'default' => false,
            ],
        ], parent::getForm());
    }

    /**
     * @inheritDoc
     * 
     * @see ExportDriver
     */
    public function generate(DataSheet $dataSheet)
    {
        // use DejavuSans font by default for UTF-8 compliance
        $font = 'dejavusans';

        // check if the selected font is supported
        if (!PDFFonts::isSupported($font))
        {
             // fallback to Courier default font
             $font = 'courier';  
        }

        // load TCPDF only if missing, because it might have been already
        // loaded by a different plugin
        if (!class_exists('TCPDF'))
        {
            \VRELoader::import('pdf.tcpdf.tcpdf');
        }
        
        // create PDF instance
        $pdf = new \TCPDF(
            $this->options->get('page_orientation', PDFConstraints::PAGE_ORIENTATION_LANDSCAPE),
            'mm',
            $this->options->get('page_format', PDFConstraints::PAGE_FORMAT_A4),
            true,
            'UTF-8',
            false
        );

        // nothing to display in header, hide it
        $pdf->SetPrintHeader(false);

        // margins (left | top | right)
        $pdf->SetMargins(5, 5, 5);

        $pdf->SetAutoPageBreak(true, 5);
        $pdf->SetFont($font, '', 13);

        // check if we should display the footer
        if ($this->options->get('show_footer', false))
        {
            // show footer
            $pdf->SetPrintFooter(true);

            // set footer font
            $pdf->setFooterFont([$font, '', 11]);

            // set footer margin
            $pdf->SetFooterMargin(5);
        }
        else
        {
            // hide footer otherwise
            $pdf->SetPrintFooter(false);
        }

        /**
         * Render HTML through parent.
         * 
         * @see HTMLExportProvider::createTable()
         */
        $html = $this->createTable($dataSheet);

        // add page
        $pdf->addPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // return PDF as a string
        return $pdf->Output('', 'S');
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
        
        // output PDF
        echo $this->generate($dataSheet);
    }

    /**
     * @inheritDoc
     */
    protected function prepareDownload(string $filename)
    {
        // prepare headers
        $this->app->setHeader('Cache-Control', 'no-store, no-cache');
        $this->app->setHeader('Content-Type', 'application/pdf');
        $this->app->setHeader('Content-Transfer-Encoding', 'binary');
        $this->app->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');
    }
}
