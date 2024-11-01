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

/**
 * Filters the deals to obtain only the ones published for the specified service.
 * 
 * @since 1.9
 */
class ServiceFilter implements CollectionFilter
{
    /** @var int */
    protected $service;

    /**
     * Class contructor.
     * 
     * @param  mixed  $service  The service identifier as string (pickup, delivery) or int (0, 1).
     */
    public function __construct($service)
    {
        if (!is_numeric($service))
        {
            // string provided, convert it into an integer
            $service = $service === 'pickup' ? 0 : 1;
        }

        $this->service = (int) $service;
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

        // get selected service
        $service = (int) $item->get('service', 2);

        // check whether any service is accepted (2) or the selected
        // service is equals to the specified one
        return $service === 2 || $service === $this->service;
    }
}
