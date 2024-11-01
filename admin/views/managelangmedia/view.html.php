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
 * VikRestaurants translation media management view.
 *
 * @since 1.9
 */
class VikRestaurantsViewmanagelangmedia extends JViewVRE
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
		
		$image = $input->get('image', '', 'string');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langmedia')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->image)
		{
			// retrieve image from translation object
			$image = $this->translation->image;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langmedia.data');
		
		// load original media details
		$this->media = JModelVRE::getInstance('media')->getItem($image);
		
		if (!$this->media)
		{
			$app->enqueueMessage(JText::translate('VRMANAGEMEDIANOTRX'), 'warning');
			$app->redirect('index.php?option=com_vikrestaurants&task=media.edit&cid[]=' . $image);
			$app->close();
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
			JToolbarHelper::apply('langmedia.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langmedia.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langmedia.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langmedia.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
