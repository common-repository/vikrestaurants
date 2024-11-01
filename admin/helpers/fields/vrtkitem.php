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

jimport('joomla.form.field.groupedlist');

/**
 * Take-away item SQL list.
 *
 * @since 1.6
 * @since 1.9.1  The class now extends the native "groupedlist" field.
 */
class JFormFieldVrtkitem extends JFormFieldGroupedlist
{
	/**
     * Method to get the field option groups.
     *
     * @return  array[]  The field option objects as a nested array in groups.
     *
     * @since   1.9.1
     */
	function getGroups()
	{
		// invoke parent first
		$groups = parent::getGroups();
		
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('m.id', 'id_menu'))
			->select($dbo->qn('m.title', 'menu_title'))
			->select($dbo->qn('e.id', 'id'))
			->select($dbo->qn('e.name', 'name'))
			->from($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'e'))
			->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $dbo->qn('m.id') . ' = ' . $dbo->qn('e.id_takeaway_menu'))
			->order($dbo->qn('m.ordering') . ' ASC')
			->order($dbo->qn('e.ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $r)
		{
			if (!isset($groups[$r->menu_title]))
			{
				$groups[$r->menu_title] = [];
			}

			if (!empty($r->id))
			{
				$groups[$r->menu_title][] = JHtml::fetch('select.option', $r->id, $r->name);
			}
		}

		return $groups;
	}
}
