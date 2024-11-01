<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Invoice\Generators;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Invoice\InvoiceGenerator;
use E4J\VikRestaurants\PDF\PDFConstraints;
use E4J\VikRestaurants\PDF\PDFFonts;

/**
 * Invoices generator abstraction.
 *
 * @since 1.9
 */
class PDFGenerator extends InvoiceGenerator
{
	/**
	 * @inheritDoc
	 */
	protected function bind($data)
	{
		$data = (object) $data;

		// bind data through parent first
		parent::bind($data);

		// check if the constraints was set in the stored JSON
		if (empty($data->constraints))
		{
			// no constraints, use empty array to load default settings
			$data->constraints = [];
		}

		// create new constraints instance with stored data
		$this->data->constraints = new PDFConstraints($data->constraints);

		return $this;
	}
	
	/**
	 * Returns an object containing the invoice properties.
	 *
	 * @return  object  The invoice properties.
	 */
	public function getConstraints()
	{
		return $this->data->constraints;
	}

	/**
	 * Overwrites the invoice constraints.
	 *
	 * @param   object  $settings  The constraints to set.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setConstraints($settings)
	{
		// DO NOT cast the settings to array/object because
		// a PDFConstraints object might be passed.
		// In that case, an iterator should be returned.

		foreach ($settings as $k => $v)
		{
			$this->data->constraints->{$k} = $v;
		}

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getData(bool $array = false)
	{
		// obtain data through parent first
		$data = parent::getData($array);

		if ($array)
		{
			// register constraints as an array
			$data['constraints'] = $this->data->constraints->toArray();
		}

		return $data;
	}

	/**
	 * @inheritDoc
	 */
	protected function issue()
	{
		if (!$this->invoice)
		{
			return false;
		}

		// prepare resulting data array
		$data = [];

		// get invoice path
		$data['path'] = $this->invoice->getInvoicePath();

		if (\JFile::exists($data['path']))
		{
			// unlink pdf if already exists
			\JFile::delete($data['path']);
		}

		if (!empty($this->data->constraints->font))
		{
			// use specified font
			$font = $this->data->constraints->font;
		}
		else
		{
			// use DejavuSans font by default for UTF-8 compliance
			$font = 'dejavusans';
		}

		// check if the selected font is supported
		if (!PDFFonts::isSupported($font))
		{
			 // fallback to Courier default font
			 $font = 'courier';  
		}

		/**
		 * Parse the invoice template.
		 * Pass the array data to let the invoice handler fill it with the resulting info.
		 */
		$pages = $this->invoice->createDocument($data);

		if (!is_array($pages))
		{
			$pages = array($pages);
		}

		// load TCPDF only if missing, because it might have been already
		// loaded by a different plugin
		if (!class_exists('TCPDF'))
		{
			\VRELoader::import('pdf.tcpdf.tcpdf');
		}
		
		$pdf = new \TCPDF($this->data->constraints->pageOrientation, $this->data->constraints->unit, $this->data->constraints->pageFormat, true, 'UTF-8', false);

		// get title from constraints
		$title = !empty($this->data->constraints->headerTitle) ? $this->data->constraints->headerTitle : null;

		if ($title)
		{
			// replace tags with fetched data
			$title = str_ireplace('{number}', $data['inv_number']        , $title);
			$title = str_ireplace('{date}'  , $data['inv_formatted_date'], $title);

			// set page title
			$pdf->SetTitle($title);

			// show header
			$pdf->SetHeaderData('', 0, $title, '');

			// set header font
			$pdf->setHeaderFont([$font, '', $this->data->constraints->fontSizes->header]);

			// set header margin
			$pdf->SetHeaderMargin((int) $this->data->constraints->margins->header);
		}
		else
		{
			// nothing to display in header, hide it
			$pdf->SetPrintHeader(false);
		}	

		// margins
		$pdf->SetMargins($this->data->constraints->margins->left, $this->data->constraints->margins->top, $this->data->constraints->margins->right);

		$pdf->SetAutoPageBreak(true, $this->data->constraints->margins->bottom);
		$pdf->setImageScale($this->data->constraints->imageScaleRatio);
		$pdf->SetFont($font, '', $this->data->constraints->fontSizes->body);

		// check if we should display the footer
		if (!empty($this->data->constraints->showFooter))
		{
			// show footer
			$pdf->SetPrintFooter(true);

			// set footer font
			$pdf->setFooterFont([$font, '', $this->data->constraints->fontSizes->footer]);

			// set footer margin
			$pdf->SetFooterMargin($this->data->constraints->margins->footer);
		}
		else
		{
			// hide footer otherwise
			$pdf->SetPrintFooter(false);
		}

		// add pages
		foreach ($pages as $page)
		{
			$pdf->addPage();
			$pdf->writeHTML($page, true, false, true, false, '');
		}
		
		// write file
		$pdf->Output($data['path'], 'F');

		// check if the file has been created
		if (!\JFile::exists($data['path']))
		{
			return false;
		}

		return $data;
	}
}
