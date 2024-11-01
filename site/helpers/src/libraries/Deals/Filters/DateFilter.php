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
 * Filters the deals to obtain only the ones published within the specified date.
 * 
 * @since 1.9
 */
class DateFilter implements CollectionFilter
{
    /** @var int */
    protected $date;

    /**
     * Class contructor.
     * 
     * @param  mixed  $date  Either a timestamp or a date string.
     */
    public function __construct($date = null)
    {
        if (!$date)
        {
            // get current time
            $date = \VikRestaurants::now();
        }

        if (!is_numeric($date))
        {
            // convert date into a timestamp
            $date = DateHelper::getTimestamp($date);
        }

        $this->date = $date;
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

        // validate start publishing
        if (!DateHelper::isNull($item->get('start_ts')) && $item->get('start_ts') > $this->date)
        {
            // deal not yet started
            return false;
        }

        // validate end publishing
        if (!DateHelper::isNull($item->get('end_ts')) && $item->get('end_ts') < $this->date)
        {
            // deal already expired
            return false;
        }

        return true;
    }
}
