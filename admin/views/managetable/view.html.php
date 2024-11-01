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
 * VikRestaurants table management view.
 *
 * @since 1.0
 */
class VikRestaurantsViewmanagetable extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		$this->rooms = JHtml::fetch('vikrestaurants.rooms');

		if (!$this->rooms)
		{
			// you should create a room first
			$app->enqueueMessage(JText::translate('VRROOMMISSINGERROR'), 'warning');
			$app->redirect('index.php?option=com_vikrestaurants&task=room.add');
			exit;
		}
		
		$ids  = $app->input->getUint('cid', []);
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->table = JModelVRE::getInstance('table')->getItem($ids ? $ids[0] : 0, $blank = true);

		// use table data stored in user state
		$this->injectUserStateData($this->table, 'vre.table.data');

		$alltables = array();

		// get list of tables
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->select($db->qn('id_room'))
			->select($db->qn('name'))
			->from($db->qn('#__vikrestaurants_table'))
			->where($db->qn('id') . ' <> ' . (int) $this->table->id);

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $t)
		{
			if (!isset($alltables[$t->id_room]))
			{
				$alltables[$t->id_room] = array();
			}

			$alltables[$t->id_room][] = JHtml::fetch('select.option', $t->id, $t->name);
		}
		
		$this->allTables = $alltables;

		if (!$this->table->id_room && $this->allTables)
		{
			// auto-select the first available room
			$this->table->id_room = key($this->allTables);
		}

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

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
			JToolbarHelper::title(JText::translate('VRMAINTITLEEDITTABLE'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLENEWTABLE'), 'vikrestaurants');
		}

		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants')
			|| $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('table.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('table.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('table.savenew', JText::translate('VRSAVEANDNEW'));
		}
		
		if ($type == 'edit' && $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2copy('table.savecopy', JText::translate('VRSAVEASCOPY'));
		}
		
		JToolbarHelper::cancel('table.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
