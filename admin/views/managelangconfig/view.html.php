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
 * VikRestaurants language configuration management view.
 *
 * @since 1.8
 */
class VikRestaurantsViewmanagelangconfig extends JViewVRE
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
		
		$param = $input->get('param', '', 'string');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langconfig')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->param)
		{
			// retrieve configuration parameter from translation object
			$param = $this->translation->param;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langconfig.data');
		
		// load original configuration record
		$this->setting = JModelVRE::getInstance('configuration')->getItem([
			'param' => $param,
		]);
		
		if (!$this->setting)
		{
			throw new RuntimeException('Record [' . $param . '] not found', 404);
		}

		$this->return = $input->get('return');

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
			JToolbarHelper::apply('langconfig.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langconfig.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langconfig.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langconfig.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
