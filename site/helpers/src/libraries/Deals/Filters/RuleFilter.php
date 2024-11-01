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
 * Filters the deals to obtain only the ones that match the specified rule(s).
 * 
 * @since 1.9
 */
class RuleFilter implements CollectionFilter
{
    /** @var string[] */
    protected $rules;

    /**
     * Class contructor.
     * 
     * @param  mixed  $rules  Either a string or an array of rules
     */
    public function __construct($rules)
    {
        if (!$rules)
        {
            throw new \InvalidArgumentException('The rule to filter the deals cannot be empty');
        }

        $this->rules = array_map('strtolower', (array) $rules);
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
        return in_array(strtolower($item->get('type', '')), $this->rules);
    }
}
