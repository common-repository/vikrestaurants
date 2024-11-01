<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants mail conditional text model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMailtext extends JModelVRE
{
    /**
     * Basic item loading implementation.
     *
     * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
     *                         If not set the instance property value is used.
     * @param   boolean  $new  True to return an empty object if missing.
     *
     * @return  mixed    The record object on success, null otherwise.
     */
    public function getItem($pk, $new = false)
    {
        $mailtext = parent::getItem($pk, $new);

        if (!$mailtext)
        {
            return null;
        }

        // decode filters and actions
        $mailtext->filters = $mailtext->filters ? (array) json_decode($mailtext->filters) : [];
        $mailtext->actions = $mailtext->actions ? (array) json_decode($mailtext->actions) : [];

        return $mailtext;
    }

	/**
     * Basic save implementation.
     *
     * @param   mixed  $data  Either an array or an object of data to save.
     *
     * @return  mixed  The ID of the record on success, false otherwise.
     */
    public function save($data)
    {
        $data = (array) $data;

        if (isset($data['filters']))
        {
            // iterate all the provided filters
            foreach ($data['filters'] as $i => $filter)
            {
                if (is_string($filter))
                {
                    // JSON given, decode it
                    $data['filters'][$i] = json_decode($filter, true);
                }
            }
        }

        if (isset($data['actions']))
        {
            // iterate all the provided actions
            foreach ($data['actions'] as $i => $action)
            {
                if (is_string($action))
                {
                    // JSON given, decode it
                    $data['actions'][$i] = json_decode($action, true);
                }
            }
        }

        // attempt to save the record
        return parent::save($data);
    }
}
