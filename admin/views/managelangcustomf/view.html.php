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
 * VikRestaurants language custom field management view.
 *
 * @since 1.7
 */
class VikRestaurantsViewmanagelangcustomf extends JViewVRE
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

		$id_customf = $input->get('id_customf', 0, 'uint');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langcustomf')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_customf)
		{
			// retrieve custom field ID from translation object
			$id_customf = $this->translation->id_customf;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langcustomf.data');
		
		// load original custom field details
		$this->field = JModelVRE::getInstance('customf')->getItem($id_customf);
		
		if (!$this->field)
		{
			throw new RuntimeException('Record [' . $id_customf . '] not found', 404);
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
			JToolbarHelper::apply('langcustomf.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langcustomf.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langcustomf.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langcustomf.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
