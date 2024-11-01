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
 * VikRestaurants take-away menu management view.
 *
 * @since 1.2
 */
class VikRestaurantsViewmanagetkmenu extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();
		
		$ids  = $app->input->getUint('cid', []);
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var JModelLegacy */
		$model = JModelVRE::getInstance('tkmenu');

		/** @var \stdClass */
		$this->menu = $model->getItem($ids ? $ids[0] : 0, $blank = true);

		/** @var object[] */
		$this->menu->products = $model->getProducts((int) $this->menu->id);

		// use take-away menu data stored in user state
		$this->injectUserStateData($this->menu, 'vre.tkmenu.data');

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
			JToolbarHelper::title(JText::translate('VRMAINTITLEEDITTKMENU'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLENEWTKMENU'), 'vikrestaurants');
		}
		
		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants')
			|| $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('tkmenu.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('tkmenu.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('tkmenu.savenew', JText::translate('VRSAVEANDNEW'));
		}
		
		if ($type == 'edit' && $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2copy('tkmenu.savecopy', JText::translate('VRSAVEASCOPY'));
		}
		
		JToolbarHelper::cancel('tkmenu.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
