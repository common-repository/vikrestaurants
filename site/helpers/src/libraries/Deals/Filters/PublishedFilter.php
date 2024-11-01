<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Filters\XORFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\Deals\Deal;

/**
 * Filters the deals to obtain/exclude only the ones that have been published.
 * 
 * @since 1.9
 */
class PublishedFilter extends XORFilter
{
    /**
     * @inheritDoc
     * 
     * @throws  \InvalidArgumentException  Only Deal instances are accepted.
     */
    protected function isSatisfied(Item $item)
    {
        if (!$item instanceof Deal)
        {
            // can handle only objects that inherit the Deal class
            throw new \InvalidArgumentException('Deal item expected, ' . get_class($item) . ' given');
        }

        // take only published items
        return (int) $item->get('published') === 1;
    }
}
