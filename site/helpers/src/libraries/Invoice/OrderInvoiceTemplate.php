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
 * Invoice template order abstraction.
 *
 * @since 1.9
 */
abstract class OrderInvoiceTemplate extends InvoiceTemplate
{
	/**
	 * @inheritDoc
	 */
	protected function parseTemplate(string $tmpl, array &$data)
	{
		// let the parent starts the template parsing
		$tmpl = parent::parseTemplate($tmpl, $data);

		// use default system timezone for dates
		$tz = \JFactory::getApplication()->get('offset', 'UTC');

		// get config
		$config = \VREFactory::getConfig();

		if (empty($this->params->date))
		{
			switch ($this->params->datetype)
			{
				case 2:
					// booking date
					$date = $this->order->created_on;
					break;

				case 3:
					// reservation check-in
					$date = $this->order->checkin_ts;
					break;

				default:
					// current date
					$date = \VikRestaurants::now();
			}
		}
		else
		{
			// directly use the specified date (expressed in UTC)
			$date = $this->params->date;
		}

		// register date within invoice data
		$data['inv_date'] = $date;

		if (is_numeric($date))
		{
			// timestamp received
			$date = \JHtml::fetch('date', $date, $config->get('dateformat'), date_default_timezone_get());
		}
		else
		{
			// date received
			$date = \JHtml::fetch('date', $date, $config->get('dateformat'), $tz);
		}

		// register formatted date within invoice data
		$data['inv_formatted_date'] = $date;

		// inject invoice date in template
		$tmpl = str_replace('{invoice_date}', $date, $tmpl);

		// customer info
		$custinfo = '';

		if (!empty($this->order->displayFields))
		{
			foreach ($this->order->displayFields as $k => $v)
			{
				// add colon as separator only in case the label doesn't
				// end with a punctuation
				if (preg_match("/[.,:;?!_\-]$/", $k))
				{
					// ends with a punctuation, do not use separator
					$sep = '';
				}
				else
				{
					$sep = ':';
				}

				$custinfo .= $k . $sep . ' ' . $v . "<br/>\n";
			}
		}
		else
		{
			$parts = [];

			// in case of empty custom fields, display the purchaser details
			$parts[] = $this->order->billing->billing_name  ?? $this->order->purchaser_nominative;
			$parts[] = $this->order->billing->billing_mail  ?? $this->order->purchaser_mail;
			$parts[] = $this->order->billing->billing_phone ?? $this->order->purchaser_phone;

			// remove blank info
			$parts = array_values(array_filter($parts));

			$custinfo = implode("<br />\n", $parts) . "<br />\n";
		}

		$tmpl = str_replace('{customer_info}', $custinfo, $tmpl);

		// billing info
		$billing_info = '';

		if ($this->order->billing)
		{
			$parts = [];

			// VAT and company name
			$company_info = [];

			if (!empty($this->order->billing->company))
			{
				$company_info[] = $this->order->billing->company;
			}

			if (!empty($this->order->billing->vatnum))
			{
				$company_info[] = $this->order->billing->vatnum;
			}

			if ($company_info)
			{
				$parts[] = implode(' ', $company_info);
			}

			// Address information
			$address_info = [];

			if (!empty($this->order->billing->billing_address))
			{
				$address_info[] = $this->order->billing->billing_address;
			}

			if (!empty($this->order->billing->billing_address_2))
			{
				$address_info[] = $this->order->billing->billing_address_2;
			}

			if ($address_info)
			{
				$parts[] = implode(', ', $address_info);
			}
			
			// City information
			$city_info = [];

			if (!empty($this->order->billing->billing_city))
			{
				$city_info[] = $this->order->billing->billing_city;
			}

			if (!empty($this->order->billing->billing_zip))
			{
				$city_info[] = $this->order->billing->billing_zip;
			}

			if (!empty($this->order->billing->billing_state))
			{
				$city_info[] = $this->order->billing->billing_state;
			}

			if ($city_info)
			{
				$parts[] = implode(', ', $city_info);
			}

			// build details
			$billing_info = implode("<br />\n", $parts);
		}

		$tmpl = str_replace('{billing_info}', $billing_info, $tmpl);

		return $tmpl;
	}

	/**
	 * @inheritDoc
	 */
	public function getRecipient()
	{
		return $this->order->purchaser_mail;
	}

	/**
	 * @inheritDoc
	 */
	public function getInvoicePath()
	{
		return \JPath::clean($this->getInvoiceFolderPath() . '/' . $this->order->id . '-' . $this->order->sid . '.pdf');
	}
}
