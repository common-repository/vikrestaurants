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
 * VikRestaurants language take-away deal management view.
 *
 * @since 1.6
 */
class VikRestaurantsViewmanagelangtkdeal extends JViewVRE
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
		
		$id_deal = $input->get('id_deal', 0, 'uint');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langtkdeal')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_deal)
		{
			// retrieve deal ID from translation object
			$id_deal = $this->translation->id_deal;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langtkdeal.data');
		
		// load original deal details
		$this->deal = JModelVRE::getInstance('tkdeal')->getItem($id_deal);
		
		if (!$this->deal)
		{
			throw new RuntimeException('Record [' . $id_deal . '] not found', 404);
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
			JToolbarHelper::apply('langtkdeal.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langtkdeal.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langtkdeal.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langtkdeal.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
