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
 * Exports a datasheet in HTML format.
 * 
 * @since 1.9
 */
class HTMLExportDriver implements ExportDriver, ConfigurableDriver
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
        return 'HTML';
    }

    /**
     * @inheritDoc
     * 
     * @see ConfigurableDriver
     */
    public function getDescription()
    {
        return \JText::translate('VRE_ORDER_EXPORT_DRIVER_HTML_DESC');
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
             * The separator for the "Text" section.
             * 
             * @var separator
             */
            'text_separator' => [
                'type'        => 'separator',
                'label'       => \JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_TAB'),
                'hiddenLabel' => true,
            ],

            /**
             * The horizontal alignment of the text.
             *
             * @var select
             */
            'align' => [
                'type'    => 'select',
                'name'    => 'args[align]',
                'label'   => \JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_ALIGN'),
                'default' => 'center',
                'options' => [
                    'left' => \JText::translate('VRE_CUSTOMIZER_PARAM_LEFT'),
                    'center' => \JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER'),
                    'right' => \JText::translate('VRE_CUSTOMIZER_PARAM_RIGHT'),
                ],
            ],
            
            /**
             * The cell padding.
             *
             * @var text
             */
            'padding' => [
                'type'    => 'number',
                'name'    => 'args[padding]',
                'label'   => \JText::translate('VRE_CUSTOMIZER_PARAM_PADDING'),
                'default' => 4,
                'min'     => 0,
                'step'    => 1,
            ],

            /**
             * The separator for the "Border" section.
             * 
             * @var separator
             */
            'border_separator' => [
                'type'        => 'separator',
                'label'       => \JText::translate('VRE_CUSTOMIZER_PARAM_BORDER'),
                'hiddenLabel' => true,
            ],

            /**
             * The border width.
             *
             * @var text
             */
            'border_width' => [
                'type'    => 'number',
                'name'    => 'args[border_width]',
                'label'   => \JText::translate('VRE_CUSTOMIZER_PARAM_WIDTH'),
                'default' => 1,
                'min'     => 0,
                'step'    => 1,
            ],

            /**
             * The border style.
             *
             * @var select
             */
            'border_style' => [
                'type'    => 'select',
                'name'    => 'args[border_style]',
                'label'   => \JText::translate('VRE_CUSTOMIZER_FIELDS_STYLE'),
                'default' => 'solid',
                'options' => [
                    'solid'  => \JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_SOLID'),
                    'dashed' => \JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DASHED'),
                    'dotted' => \JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DOTTED'),
                ],
            ],

            /**
             * The border color.
             *
             * @var color
             */
            'border_color' => [
                'type'    => 'color',
                'name'    => 'args[border_color]',
                'label'   => \JText::translate('VRE_CUSTOMIZER_PARAM_COLOR'),
                'default' => 'DDDDDD',
                'preview' => true,
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
        // return an HTML blank page
        return \JLayoutHelper::render(
            'document.blankpage',
            [
                'title' => $dataSheet->getTitle(),
                'body'  => $this->createTable($dataSheet),
            ],
            VREBASE . DIRECTORY_SEPARATOR . 'layouts'
        );
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
        
        // output HTML
        echo $this->generate($dataSheet);
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
        $this->app->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->app->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
    }

    /**
     * Creates the HTML of the table starting from the received datasheet.
     * 
     * @param   DataSheet  $dataSheet
     * 
     * @return  string
     */
    protected function createTable(DataSheet $dataSheet)
    {
        if ($this->options->get('border_width', 1))
        {
            // display border property only in case the width is higher than 0
            $border = sprintf(' border: %dpx %s #%s;',
                ((int) $this->options->get('border_width', 1)),
                $this->options->get('border_style', 'solid'),
                ltrim($this->options->get('border_color', 'DDDDDD'), '#')
            );
        }
        else
        {
            $border = '';
        }

        $table = '';

        // make sure the head exists
        if ($head = $dataSheet->getHead())
        {
            $table .= '<thead><tr>';

            foreach ($head as $column)
            {
                $table .= '<th style="text-align: ' . $this->options->get('align', 'left') . ';' . $border . '">' . $column . '</th>';
            }

            $table .= '</tr></thead>';
        }

        $table .= '<tbody>';

        // iterate all the body rows
        foreach ($dataSheet->getBody() as $row)
        {
            $table .= '<tr>';

            foreach ($row as $column)
            {
                $table .= '<td style="text-align: ' . $this->options->get('align', 'left') . ';' . $border . '">' . $column . '</td>';
            }

            $table .= '</tr>';
        }

        $table .= '</tbody>';

        // make sure the footer exists
        if ($footer = $dataSheet->getFooter())
        {
            $table .= '<tfoot><tr>';

            foreach ($footer as $column)
            {
                $table .= '<td style="text-align: ' . $this->options->get('align', 'left') . ';' . $border . '">' . $column . '</td>';
            }

            $table .= '</tr></tfoot>';
        }

        return '<table cellpadding="' . $this->options->get('padding', 4) . '" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">' . $table . '</table>';
    }
}
