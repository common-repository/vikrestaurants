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
 * VikRestaurants language take-away menus attribute management view.
 *
 * @since 1.6
 */
class VikRestaurantsViewmanagelangtkattribute extends JViewVRE
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
		
		$id_attribute = $input->get('id_attribute', 0, 'uint');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langtkattribute')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_attribute)
		{
			// retrieve attribute ID from translation object
			$id_attribute = $this->translation->id_attribute;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langtkattribute.data');
		
		// load original attribute details
		$this->attribute = JModelVRE::getInstance('tkattribute')->getItem($id_attribute);
		
		if (!$this->attribute)
		{
			throw new RuntimeException('Record [' . $id_attribute . '] not found', 404);
		}

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
			JToolbarHelper::apply('langtkattribute.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langtkattribute.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langtkattribute.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langtkattribute.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
