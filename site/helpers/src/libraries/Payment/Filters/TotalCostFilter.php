<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Payment\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Filters the payment gateways to obtain only the ones compliant with the current total cost.
 * 
 * @since 1.9
 */
class TotalCostFilter implements CollectionFilter
{
	/** @var float */
	protected $total;

	/**
	 * Class constructor.
	 * 
	 * @param  mixed  $total  The current cart total cost.
	 */
	public function __construct($total = null)
	{
		if (is_null($total))
		{
			// recover total from cart
			$this->total = \E4J\VikRestaurants\TakeAway\Cart::getInstance()->getTotalCost();   
		}
		else if ($total instanceof \E4J\VikRestaurants\TakeAway\Cart)
		{
			// extract total cost from cart
			$this->total = $total->getTotalCost();
		}
		else
		{
			$this->total = (float) $total;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		// get the provided threshold
		$threshold = (float) $item->get('enablecost', 0);

		if ($threshold == 0)
		{
			// total cost restriction disabled
			return true;
		}

		// Validate payment cost restrictions:
		// > 0, the total cost must be equals or higher than the specified threshold;
		// < 0, the total cost must be equals or lower than the specified threshold.
		return ($threshold > 0 &&      $threshold <= $this->total)
			|| ($threshold < 0 && abs($threshold) >= $this->total);
	}
}
