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
 * Tax rule percentage SUM calculator.
 *
 * @since 1.9
 */
class AddRule extends TaxRule
{
	/**
	 * @inheritDoc
	 */
	public function calculate($total, $data, array $options = [])
	{
		// check whether we should apply the calculation
		// to the base cost (1) or on cascade (2)
		$apply = (int) $this->get('apply');

		if ($apply == 2)
		{
			// apply to total gross
			$total = $data->gross;
		}

		// get specified tax amount
		$tax = (float) $this->get('amount', 0.0);

		// calculate resulting taxes
		$tax = round($total * $tax / 100, 2, PHP_ROUND_HALF_UP);

		// make sure the calculated taxes do not exceed
		// the specified threshold (TAX CAP)
		$cap = (float) $this->get('cap', 0);

		if ($cap > 0)
		{
			$tax = min($tax, $cap);
		}

		// update resulting data
		$data->tax   += $tax;
		$data->gross += $tax;

		// merge breakdown
		$this->addBreakdown($tax, $data->breakdown);
	}
}
