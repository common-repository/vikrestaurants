<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Invoice;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Invoice template abstraction.
 *
 * @since 1.9
 */
abstract class InvoiceTemplate
{
	/**
	 * The order details.
	 *
	 * @var object
	 */
	protected $order;

	/**
	 * The invoice arguments (e.g. increment number or legal info).
	 *
	 * @var object
	 */
	protected $params;

	/**
	 * Class constructor.
	 *
	 * @param  object  $order   The order details.
	 * @param  mixed   $params  The invoice details.
	 */
	public function __construct($order, $params = [])
	{
		$this->order = $order;
		$this->setParams($params);
	}
	
	/**
	 * Overwrites the invoice parameters.
	 *
	 * @param   object  $params  The parameters to set.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setParams($params)
	{
		$this->params = (object) $params;

		return $this;
	}

	/**
	 * Creates the invoice document.
	 * 
	 * @param   array   &$data  An array data to fill.
	 * 
	 * @return  string  The invoice document.
	 */
	final public function createDocument(array &$data)
	{
		// get invoice template
		$tmpl = $this->getPageTemplate();

		/**
		 * Parse the invoice template.
		 * Pass the array data to let the invoice handler fill it with the resulting info.
		 */
		return $this->parseTemplate($tmpl, $data);
	}

	/**
	 * Parses the given template to replace the placeholders
	 * with the values contained in the order details.
	 *
	 * @param   string  $tmpl   The template to parse.
	 * @param   array   &$data  An array data to fill.
	 *
	 * @return  string  The invoice document.
	 */
	protected function parseTemplate(string $tmpl, array &$data)
	{
		$config = \VREFactory::getConfig();

		$logo_name = $config->get('companylogo');

		// company logo
		if ($logo_name)
		{ 
			$logo_str = '<img src="' . VREMEDIA_URI . $logo_name . '" />';
		}
		else
		{
			$logo_str = '';
		}

		$tmpl = str_replace('{company_logo}', $logo_str, $tmpl);
		
		// company info
		$tmpl = str_replace('{company_info}', nl2br($this->params->legalinfo), $tmpl);
		
		// invoice details
		$suffix = '';

		if (!empty($this->params->suffix))
		{
			$suffix = '/' . $this->params->suffix;
		}

		$tmpl = str_replace('{invoice_number}', $this->params->number, $tmpl);
		$tmpl = str_replace('{invoice_suffix}', $suffix 			 , $tmpl);

		// register invoice number
		$data['inv_number'] = $this->params->number . $suffix;

		return $tmpl;
	}

	/**
	 * Returns the destination absolute path of the invoices folder.
	 * Inherit in children methods in case the path needs further
	 * subfolders.
	 *
	 * @return  string  The invoice folder path.
	 */
	public function getInvoiceFolderPath()
	{
		// use default path
		return VREINVOICE;
	}

	/**
	 * Returns the destination URI of the invoices folder.
	 * Inherit in children methods in case the path needs further
	 * subfolders.
	 *
	 * @return  string  The invoice folder URI.
	 */
	public function getInvoiceFolderURI()
	{
		// use default path
		return VREINVOICE_URI;
	}

	/**
	 * Returns the e-mail address of the user that should
	 * receive the invoice via mail.
	 *
	 * @return  string  The customer e-mail.
	 */
	abstract public function getRecipient();

	/**
	 * Returns the destination path of the invoice.
	 *
	 * @return  string  The invoice path.
	 */
	abstract public function getInvoicePath();

	/**
	 * Returns the page template that will be used to 
	 * generate the invoice.
	 *
	 * @return  string  The base HTML.
	 */
	abstract protected function getPageTemplate();
}
