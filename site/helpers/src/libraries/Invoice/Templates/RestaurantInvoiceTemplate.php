<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Invoice\Templates;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Invoice\OrderInvoiceTemplate;

/**
 * Invoice template for restaurant reservations.
 *
 * @since 1.9
 */
class RestaurantInvoiceTemplate extends OrderInvoiceTemplate
{
	/**
	 * @inheritDoc
	 */
	final protected function getPageTemplate()
	{
		$data = [
			'order'     => $this->order,
			'breakdown' => $this->getBreakdown(),
			'usetaxbd'  => \VREFactory::getConfig()->getBool('usetaxbd', false),
		];

		// create layout file (always read from the site folder of VikRestaurants)
		$layout = new \JLayoutFile('templates.invoice.restaurant', null, [
			'component' => 'com_vikrestaurants',
			'client'    => 'site',
		]);

		return $layout->render($data);
	}

	/**
	 * @inheritDoc
	 */
	protected function parseTemplate($tmpl, &$data)
	{
		// let the parent starts the template parsing
		$tmpl = parent::parseTemplate($tmpl, $data);

		$currency = \VREFactory::getCurrency();
		
		// totals
		$tmpl = str_replace(   '{invoice_totalnet}', $currency->format($this->order->total_net + $this->order->discount_val), $tmpl);
		$tmpl = str_replace(   '{invoice_totaltax}', $currency->format($this->order->total_tax)                             , $tmpl);
		$tmpl = str_replace(   '{invoice_totaltip}', $currency->format($this->order->tip_amount)                            , $tmpl);
		$tmpl = str_replace( '{invoice_grandtotal}', $currency->format($this->order->bill_value)                            , $tmpl);
		$tmpl = str_replace(  '{invoice_paycharge}', $currency->format($this->order->payment_charge)                        , $tmpl);
		$tmpl = str_replace('{invoice_discountval}', $currency->format($this->order->discount_val * -1)                     , $tmpl);
		
		return $tmpl;
	}

	/**
	 * Extracts an overall breakdown from the order.
	 *
	 * @return  array
	 */
	protected function getBreakdown()
	{
		$arr = [];

		// group all breakdowns at the same level
		foreach ($this->order->items as $item)
		{
			if ($item->tax_breakdown)
			{
				$arr = array_merge($arr, $item->tax_breakdown);
			}
		}

		$breakdown = [];

		// iterate breakdowns
		foreach ($arr as $bd)
		{
			if (!isset($breakdown[$bd->name]))
			{
				$breakdown[$bd->name] = 0;
			}

			$breakdown[$bd->name] += $bd->tax;
		}

		// check if we have a tax for the payment
		if ($this->order->payment_tax > 0)
		{
			// manually register payment tax within the breakdown
			$breakdown[\JText::translate('VRINVPAYTAX')] = $this->order->payment_tax;
		}

		// make sure that the sum of all the registered BD taxes are not lower 
		// that the bill total taxes
		if (($sum = array_sum($breakdown)) < $this->order->total_tax)
		{
			// include the remaining amount under the "Other Taxes" line
			$breakdown[\JText::translate('VRINVOTHERTAX')] = $this->order->total_tax - $sum;
		}

		return $breakdown;
	}
}
