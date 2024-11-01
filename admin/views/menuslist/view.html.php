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
 * VikRestaurants menus list preview.
 *
 * @since 1.3
 */
class VikRestaurantsViewmenuslist extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$dbo   = JFactory::getDbo();

		// force tmpl=component in request
		$input->set('tmpl', 'component');
		
		$id = $input->get('id', 0, 'uint');

		/** @var stdClass */
		$this->specialDay = JModelVRE::getInstance('specialday')->getItem($id);

		if (!$this->specialDay)
		{
			throw new Exception(sprintf('Special day [%d] not found', $id), 404);
		}

		$q = $dbo->getQuery(true)
			->select($dbo->qn('m.id'))
			->from($dbo->qn('#__vikrestaurants_sd_menus', 'a'))
			->where($dbo->qn('id_spday') . ' = ' . $id);

		if ($this->specialDay->group == 1)
		{
			$q->select($dbo->qn('m.name'));
			$q->select($dbo->qn('m.image'));
			$q->leftjoin($dbo->qn('#__vikrestaurants_menus', 'm') . ' ON ' . $dbo->qn('m.id') . ' = ' . $dbo->qn('a.id_menu'));
		}
		else
		{
			$q->select($dbo->qn('m.title', 'name'));
			// take-away menus don't support an image, always use NULL
			$q->select('NULL AS ' . $dbo->qn('image'));
			$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $dbo->qn('m.id') . ' = ' . $dbo->qn('a.id_menu'));
		}
		
		$dbo->setQuery($q);
		$this->menus = $dbo->loadObjectList();

		// display the template
		parent::display($tpl);
	}
}
