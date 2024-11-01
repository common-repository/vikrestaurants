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
 * VikRestaurants language menu management view.
 *
 * @since 1.5
 */
class VikRestaurantsViewmanagelangmenu extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();
		
		$id_menu = $input->get('id_menu', 0, 'uint');
		
		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langmenu')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_menu)
		{
			// retrieve menu ID from translation object
			$id_menu = $this->translation->id_menu;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langmenu.data');

		// load original menu details
		$this->menu = JModelVRE::getInstance('menu')->getItem($id_menu);
		
		if (!$this->menu)
		{
			throw new RuntimeException('Record [' . $id_menu . '] not found', 404);
		}

		// obtain sections translations

		$q = $dbo->getQuery(true)
			->select($dbo->qn(['s.id', 's.name', 's.description']))
			->select($dbo->qn('sl.id', 'lang_id'))
			->select($dbo->qn('sl.name', 'lang_name'))
			->select($dbo->qn('sl.description', 'lang_description'))
			->from($dbo->qn('#__vikrestaurants_menus_section', 's'))
			->leftjoin(
				$dbo->qn('#__vikrestaurants_lang_menus_section', 'sl') 
				. ' ON ' . $dbo->qn('sl.id_section') . ' = ' . $dbo->qn('s.id')
				. ' AND ' . $dbo->qn('sl.id_parent') . ' = ' . (int) $this->translation->id
			)
			->where($dbo->qn('s.id_menu') . ' = ' . $this->menu->id)
			->order($dbo->qn('s.ordering') . ' ASC');

		$dbo->setQuery($q);
		$this->sections = $dbo->loadObjectList();

		// display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @param 	string  $type  The view type ('edit' or 'new').
	 *
	 * @return 	void
	 */
	private function addToolBar($type)
	{
		// add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolbarHelper::title(JText::translate('VRE_TRX_EDIT_TITLE'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRE_TRX_NEW_TITLE'), 'vikrestaurants');
		}
		
		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants')
			|| $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('langmenu.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langmenu.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langmenu.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langmenu.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
