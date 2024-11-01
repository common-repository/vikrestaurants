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
 * VikRestaurants menu management view.
 *
 * @since 1.0
 */
class VikRestaurantsViewmanagemenu extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		$ids  = $app->input->getUint('cid', []);
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		$model = JModelVRE::getInstance('menu');

		/** @var \stdClass */
		$this->menu = $model->getItem($ids ? $ids[0] : 0, $blank = true);

		/** @var array */
		$this->menu->sections = $model->getSections($this->menu->id);

		// use menu data stored in user state
		$this->injectUserStateData($this->menu, 'vre.menu.data');
		
		// get products
		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name', 'image', 'description', 'price')))
			->from($dbo->qn('#__vikrestaurants_section_product'))
			->where($dbo->qn('hidden') . ' = 0')
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		$this->products = $dbo->loadObjectList();

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
			JToolbarHelper::title(JText::translate('VRMAINTITLEEDITMENU'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLENEWMENU'), 'vikrestaurants');
		}
		
		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants')
			|| $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('menu.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('menu.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('menu.savenew', JText::translate('VRSAVEANDNEW'));
		}

		if ($type == 'edit' && $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2copy('menu.savecopy', JText::translate('VRSAVEASCOPY'));
		}
		
		JToolbarHelper::cancel('menu.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
