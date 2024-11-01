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
 * Invoices generator abstraction.
 *
 * @since 1.9
 */
abstract class InvoiceGenerator
{
	/**
	 * The invoice instance.
	 *
	 * @var InvoiceTemplate
	 */
	protected $invoice;

	/**
	 * The invoice parameters and settings.
	 *
	 * @var object
	 */
	protected $data;

	/**
	 * Class constructor.
	 *
	 * @param  InvoiceTemplate  $invoice  The invoice instance to generate. If not
	 *                                    specified, it will be possible to set it
	 *                                    in a second time.
	 * @param  object           $data     An object containing the invoice arguments.
	 *                                    Leave empty to autoload them.
	 */
	public function __construct(InvoiceTemplate $invoice = null, $data = null)
	{
		if (!$data || !property_exists((object) $data, 'params'))
		{
			// load data from configuration
			$data = \VREFactory::getConfig()->getObject('invoiceobj', null);
		}
		
		// bind provided parameters
		$this->bind($data);

		if ($invoice)
		{
			$this->setInvoice($invoice);
		}
	}

	/**
	 * Loads the invoice settings.
	 * 
	 * @param   array|object  $data  The data to bind.
	 *
	 * @return  self  This object to support chaining.
	 */
	protected function bind($data)
	{
		// convert data into an object
		$data = (object) $data;

		$this->data = new \stdClass;

		if (!isset($data->params) || !is_object($data->params) || !get_object_vars($data->params))
		{
			// get system timezone
			$tz = \JFactory::getApplication()->get('offset', 'UTC');

			// create parameters for the first time
			$this->data->params = new \stdClass;
			$this->data->params->number      = 1;
			$this->data->params->suffix      = (int) \JHtml::fetch('date', 'now', 'Y', $tz);
			$this->data->params->datetype    = 1; // 1: today, 2: booking date, 3: check-in date
			$this->data->params->date        = null;
			$this->data->params->legalinfo   = '';
			$this->data->params->sendinvoice = 0;
		}
		else
		{
			$this->data->params = (object) $data->params;

			// always unset last stored date
			$this->data->params->date = null;
		}

		return $this;
	}

	/**
	 * Sets the invoice into the internal state.
	 *
	 * @param   InvoiceTemplate  $invoice  The invoice instance.
	 *
	 * @return  self             This object to support chaining.
	 */
	public function setInvoice(InvoiceTemplate $invoice)
	{
		// set invoice and force it to use our internal parameters
		$this->invoice = $invoice;
		$this->invoice->setParams($this->data->params);

		return $this;
	}

	/**
	 * Returns an array containing the invoice arguments.
	 *
	 * @return  object  The invoice arguments.
	 */
	public function getParams()
	{
		return $this->data->params;
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
		$params = (object) $params;

		foreach ($params as $k => $v)
		{
			if (property_exists($this->data->params, $k))
			{
				$this->data->params->{$k} = $v;
			}
		}

		if (!empty($params->inv_number))
		{
			list($this->data->params->number, $this->data->params->suffix) = explode('/', $params->inv_number);
		}

		return $this;
	}

	/**
	 * Returns an object containing the invoice parameters.
	 *
	 * @param   bool   $array  True to return the data as array.
	 *
	 * @return  mixed
	 */
	public function getData(bool $array = false)
	{
		if (!$array)
		{
			return clone $this->data;
		}
		else
		{
			$data = [
				'params' => (array) $this->data->params,
			];
		}

		return $data;
	}

	/**
	 * Generates the invoices related to the specified order.
	 *
	 * @param   bool   $increase  True to increase the invoice number
	 *                            by one step after generation.
	 *
	 * @return  mixed  The invoice array data on success, otherwise false.
	 */
	public function generate(bool $increase = true)
	{
		if (!$this->invoice)
		{
			// invoice not yet set
			return false;
		}

		// get current language tag
		$lang = \JFactory::getLanguage()->getTag();

		// always load the template by using the default language of the website
		\VikRestaurants::loadLanguage(\VikRestaurants::getDefaultLanguage(), JPATH_SITE);

		// generate file
		$path = $this->issue();

		// restore previous language
		\VikRestaurants::loadLanguage($lang, 'auto');

		if (!$path)
		{
			// something went wrong
			return false;
		}

		if ($increase)
		{
			// increase invoice number in case we are generating progressively
			$this->increaseNumber();
		}

		return $path;
	}

	/**
	 * Sends the invoice via e-mail to the customer.
	 *
	 * @param   string  $path  The invoice path, which will be 
	 *                         included as attachment within the e-mail.
	 *
	 * @return  bool    True on success, otherwise false.
	 */
	public function send(string $path)
	{
		if (!$this->invoice || !\JFile::exists($path))
		{
			// invoice not yet set
			return false;
		}

		$to = $this->invoice->getRecipient();

		if (!$to)
		{
			return false;
		}

		$sendermail = \VikRestaurants::getSenderMail();
		$fromname   = \VREFactory::getConfig()->get('restname');

		$id = basename($path);
		$id = substr($id, 0, strrpos($id, '.'));

		// get current language tag
		$lang = \JFactory::getLanguage()->getTag();

		// always load the mail contents by using the default language of the website
		\VikRestaurants::loadLanguage(\VikRestaurants::getDefaultLanguage(), JPATH_SITE);
		
		// fetch mail subject
		$subject = \JText::sprintf('VRINVMAILSUBJECT', $fromname, $id);
		
		// added message to e-mail
		$content = \JText::sprintf('VRINVMAILCONTENT', $fromname, $id);

		// restore previous language
		\VikRestaurants::loadLanguage($lang);
	
		// send notification e-mail
		return \VREApplication::getInstance()->sendMail(
			$sendermail,
			$fromname,
			$to,
			$sendermail,
			$subject,
			$content,
			[$path],
			$isHtml = false
		);
	}

	/**
	 * Method used to save the current parameters and settings.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function save()
	{	
		// update invoice data
		\VREFactory::getConfig()->set('invoiceobj', $this->getData());

		return $this;
	}

	/**
	 * Increase the invoice number after a successful generation.
	 *
	 * @return  void
	 */
	protected function increaseNumber()
	{
		// increase number by one
		$this->data->params->number++;
		// store parameters
		$this->save();
	}

	/**
	 * Children classes should implement this method in order to complete
	 * the generation of the invoice.
	 * 
	 * @return  mixed
	 */
	abstract protected function issue();
}
