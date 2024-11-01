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

use E4J\VikRestaurants\Collection\Filters\XORFilter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Filters the payment gateways to obtain/exclude only the ones that have been published.
 * 
 * @since 1.9
 */
class PublishedFilter extends XORFilter
{
	/**
	 * @inheritDoc
	 */
	protected function isSatisfied(Item $item)
	{
		// take only published items
		return (int) $item->get('published') === 1;
	}
}
