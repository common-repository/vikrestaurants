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
 * VikRestaurants language take-away menu entry management view.
 *
 * @since 1.6
 */
class VikRestaurantsViewmanagelangtkproduct extends JViewVRE
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
		
		$id_entry = $input->get('id_entry', 0, 'uint');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langtkentry')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_entry)
		{
			// retrieve product ID from translation object
			$id_entry = $this->translation->id_entry;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langtkentry.data');

		// load original product details
		$this->entry = JModelVRE::getInstance('tkentry')->getItem($id_entry);
		
		if (!$this->entry)
		{
			throw new RuntimeException('Record [' . $id_entry . '] not found', 404);
		}

		// obtain variations translations

		$q = $dbo->getQuery(true)
			->select($dbo->qn(['o.id', 'o.name', 'o.alias']))
			->select($dbo->qn('ol.id', 'lang_id'))
			->select($dbo->qn('ol.name', 'lang_name'))
			->select($dbo->qn('ol.alias', 'lang_alias'))
			->from($dbo->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o'))
			->leftjoin(
				$dbo->qn('#__vikrestaurants_lang_takeaway_menus_entry_option', 'ol') 
				. ' ON ' . $dbo->qn('ol.id_option') . ' = ' . $dbo->qn('o.id')
				. ' AND ' . $dbo->qn('ol.id_parent') . ' = ' . (int) $this->translation->id
			)
			->where($dbo->qn('o.id_takeaway_menu_entry') . ' = ' . $this->entry->id)
			->order($dbo->qn('o.ordering') . ' ASC');

		$dbo->setQuery($q);
		$this->variations = $dbo->loadObjectList();

		// obtain toppings groups translations

		$q = $dbo->getQuery(true)
			->select($dbo->qn(['g.id', 'g.title', 'g.description']))
			->select($dbo->qn('gl.id', 'lang_id'))
			->select($dbo->qn('gl.name', 'lang_name'))
			->select($dbo->qn('gl.description', 'lang_description'))
			->from($dbo->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g'))
			->leftjoin(
				$dbo->qn('#__vikrestaurants_lang_takeaway_menus_entry_topping_group', 'gl') 
				. ' ON ' . $dbo->qn('gl.id_group') . ' = ' . $dbo->qn('g.id')
				. ' AND ' . $dbo->qn('gl.id_parent') . ' = ' . (int) $this->translation->id
			)
			->where($dbo->qn('g.id_entry') . ' = ' . $this->entry->id)
			->order($dbo->qn('g.ordering') . ' ASC');

		$dbo->setQuery($q);
		$this->groups = $dbo->loadObjectList();

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
			JToolbarHelper::apply('langtkentry.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langtkentry.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langtkentry.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langtkentry.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
