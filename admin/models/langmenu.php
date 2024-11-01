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
 * VikRestaurants language menu model.
 *
 * @since 1.9
 */
class VikRestaurantsModelLangmenu extends JModelVRE
{
    /**
     * Extend delete implementation to delete any related records
     * stored within a separated table.
     *
     * @param   mixed    $ids  Either the record ID or a list of records.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public function delete($ids)
    {
        // only int values are accepted
        $ids = array_map('intval', (array) $ids);

        // invoke parent first
        if (!parent::delete($ids))
        {
            // nothing to delete
            return false;
        }

        $db = JFactory::getDbo();

        // load any section translation
        $q = $db->getQuery(true)
            ->select($db->qn('id'))
            ->from($db->qn('#__vikrestaurants_lang_menus_section'))
            ->where($db->qn('id_parent') . ' IN (' . implode(',', $ids) . ')' );

        $db->setQuery($q);

        if ($languages = $db->loadColumn())
        {
            // delete all the translations that belong to the removed menu
            JModelVRE::getInstance('langmenusection')->delete($languages);
        }

        return true;
    }
}
