<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Item;

/**
 * VikRestaurants conditional text holder.
 *
 * @since 1.9
 */
class ConditionalText extends Item
{
    /** @var array */
    protected $_filterInstances = null;

    /** @var array */
    protected $_actionInstances = null;

    /**
     * inheritDoc
     */
    public function __construct($data)
    {
        parent::__construct($data);

        // get filters
        $filters = $this->get('filters', '');

        if (is_string($filters))
        {
            // JSON decode filters
            $this->set('filters', (array) json_decode($filters));
        }

        // get actions
        $actions = $this->get('actions', '');

        if (is_string($actions))
        {
            // JSON decode actions
            $this->set('actions', (array) json_decode($actions));
        }
    }

    /**
     * Converts the filters of a conditional text into an array of instances.
     * 
     * @return  array
     */
    public function getFilters()
    {
        if (is_null($this->_filterInstances))
        {
            // access the global conditional text factory class
            $factory = Factory::getInstance();

            $this->_filterInstances = [];

            foreach ($this->get('filters', []) as $filter)
            {
                $filter = (object) $filter;

                try
                {
                    /** @var E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextFilter */
                    $filterInstance = $factory->getFilter($filter->id, $filter->options);

                    // register filter instance
                    $this->_filterInstances[$filter->id] = $filterInstance;
                }
                catch (\Exception $e)
                {
                    // filter not found, ignore
                }
            }
        }

        return $this->_filterInstances;
    }

    /**
     * Converts the actions of a conditional text into an array of instances.
     * 
     * @return  array
     */
    public function getActions()
    {
        if (is_null($this->_actionInstances))
        {
            // access the global conditional text factory class
            $factory = Factory::getInstance();

            $this->_actionInstances = [];

            foreach ($this->get('actions', []) as $action)
            {
                $action = (object) $action;

                try
                {
                    /** @var E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextAction */
                    $actionInstance = $factory->getAction($action->id, $action->options);

                    if (!isset($this->_actionInstances[$action->id]))
                    {
                        // create slot for this ID because a conditional text may support
                        // the same type of action more than once
                        $this->_actionInstances[$action->id] = [];
                    }

                    // register action instance
                    $this->_actionInstances[$action->id][] = $actionInstance;
                }
                catch (\Exception $e)
                {
                    // action not found, ignore
                }
            }
        }

        return $this->_actionInstances;
    }
}
