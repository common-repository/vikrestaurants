<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Taxing\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Taxing\TaxRule;

/**
 * Tax rule VAT calculator (included taxes).
 *
 * @since 1.9
 */
class VatRule extends TaxRule
{
	/**
	 * @inheritDoc
	 */
	public function calculate($total, $data, array $options = [])
	{
		// get specified tax amount
		$tax = (float) $this->get('amount', 0.0);

		// DO NOT take care of "apply" parameter, because
		// inclusive taxes can be calculated only on the
		// base total gross

		// calculate resulting VAT
		$tax = round($data->gross - $data->gross / (1 + $tax / 100), 2, PHP_ROUND_HALF_UP);

		// make sure the calculated taxes do not exceed
		// the specified threshold (TAX CAP)
		$cap = (float) $this->get('cap', 0);

		if ($cap > 0)
		{
			$tax = min($tax, $cap);
		}

		// sub to taxes
		$data->tax += $tax;
		// subtract from net
		$data->net -= $tax;

		// merge breakdown
		$this->addBreakdown($tax, $data->breakdown);
	}
}
