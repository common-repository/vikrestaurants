<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\CustomFields\Field;

/**
 * Filters the custom fields to obtain only the ones belonging to the restaurant group.
 * 
 * @since 1.9
 */
class RestaurantGroupFilter implements CollectionFilter
{
    /**
     * @inheritDoc
     * 
     * @throws  \InvalidArgumentException  Only Field instances are accepted.
     */
    public function match(Item $item)
    {
        if (!$item instanceof Field)
        {
            // can handle only objects that inherit the Field class
            throw new \InvalidArgumentException('Field item expected, ' . get_class($item) . ' given');
        }

        // take only the fields belonging to the restaurant group
        return $item->get('group', null) == 0;
    }
}
