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
 * VikRestaurants translation status code management view.
 *
 * @since 1.9
 */
class VikRestaurantsViewmanagelangstatuscode extends JViewVRE
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
		
		$id_status_code = $input->getUint('id_status_code', 0);
		
		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';

		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langstatuscode')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_status_code)
		{
			// retrieve status code ID from translation object
			$id_status_code = $this->translation->id_status_code;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langstatuscode.data');
		
		// load original status code details
		$this->status = JModelVRE::getInstance('statuscode')->getItem($id_status_code);
		
		if (!$this->status)
		{
			throw new RuntimeException('Record [' . $id_status_code . '] not found', 404);
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
			JToolbarHelper::apply('langstatuscode.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langstatuscode.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langstatuscode.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langstatuscode.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
