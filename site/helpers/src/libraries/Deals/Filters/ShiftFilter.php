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
 * Filters the deals to obtain only the ones published for the specified time.
 * 
 * @since 1.9
 */
class ShiftFilter implements CollectionFilter
{
    /** @var int */
    protected $time;

    /**
     * Class contructor.
     * 
     * @param  mixed  $time  Either a unix timestamp or a time string.
     */
    public function __construct($time = null)
    {
        if (!$time)
        {
            // get current day
            $time = \VikRestaurants::now();
        }

        if (is_numeric($time))
        {
            // timestamp provided, extract time
            $time = date('H:i', $time);
        }

        // convert time string in seconds
        $this->time = \JHtml::fetch('vikrestaurants.time2min', $time);
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

        $shifts = $item->get('shifts', []);

        if (!$shifts)
        {
            // published for any shift
            return true;
        }

        // iterate working shifts
        foreach ($shifts as $shiftId)
        {
            // get working shift details
            $shift = \JHtml::fetch('vikrestaurants.timeofshift', $shiftId);

            // make sure the working shift exists and contains the selected time
            if ($shift && $shift->from <= $this->time && $this->time <= $shift->to)
            {
                // shift found
                return true;
            }
        }

        // no matching working shifts
        return false;
    }
}
