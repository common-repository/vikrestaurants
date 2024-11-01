<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Filters\XORFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\DeliveryArea\Area;

/**
 * Filters the delivery areas to obtain/exclude only the ones that have been published.
 * 
 * @since 1.9
 */
class PublishedFilter extends XORFilter
{
    /**
     * @inheritDoc
     * 
     * @throws  \InvalidArgumentException  Only Area instances are accepted.
     */
    protected function isSatisfied(Item $item)
    {
        if (!$item instanceof Area)
        {
            // can handle only objects that inherit the Area class
            throw new \InvalidArgumentException('Area item expected, ' . get_class($item) . ' given');
        }

        // take only published items
        return (int) $item->get('published') === 1;
    }
}
