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

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\Deals\Deal;
use E4J\VikRestaurants\Helpers\DateHelper;

/**
 * Filters the deals to obtain only the ones published for the specified day of the week.
 * 
 * @since 1.9
 */
class WeekdayFilter implements CollectionFilter
{
    /** @var int */
    protected $day;

    /**
     * Class contructor.
     * 
     * @param  int  $day  Either a unix timestamp or a day of the week.
     */
    public function __construct(int $day = null)
    {
        if (!$day)
        {
            // get current day
            $day = \VikRestaurants::now();
        }

        if ($day > 6)
        {
            // timestamp provided, extract day
            $day = (int) date('w', $day);
        }

        $this->day = $day;
    }

    /**
     * @inheritDoc
     * 
     * @throws  \InvalidArgumentException  Only Deal instances are accepted.
     */
    public function match(Item $item)
    {
        if (!$item instanceof Deal)
        {
            // can handle only objects that inherit the Deal class
            throw new \InvalidArgumentException('Deal item expected, ' . get_class($item) . ' given');
        }

        // make sure the deal rule is contained within the provided list
        return in_array($this->day, $item->getDays());
    }
}
