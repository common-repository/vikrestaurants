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
 * VikRestaurants menus product management view.
 *
 * @since 1.4
 */
class VikRestaurantsViewmanagemenusproduct extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$ids  = $app->input->getUint('cid', []);
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->product = JModelVRE::getInstance('menusproduct')->getItem($ids ? $ids[0] : 0, $blank = true);

		// use product data stored in user state
		$this->injectUserStateData($this->product, 'vre.menusproduct.data');

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
			JToolbarHelper::title(JText::translate('VRMAINTITLEEDITMENUSPRODUCT'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLENEWMENUSPRODUCT'), 'vikrestaurants');
		}

		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants')
			|| $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('menusproduct.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('menusproduct.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('menusproduct.savenew', JText::translate('VRSAVEANDNEW'));
		}

		if ($type == 'edit' && $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2copy('menusproduct.savecopy', JText::translate('VRSAVEASCOPY'));
		}
		
		JToolbarHelper::cancel('menusproduct.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
