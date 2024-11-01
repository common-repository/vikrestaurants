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
 * Vikestaurants media management view.
 *
 * @since 1.3
 */
class VikRestaurantsViewmanagemedia extends JViewVRE
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
		
		// set the toolbar
		$this->addToolBar();
		
		$filename = $input->get('cid', array(''), 'string');
		$filename = $filename[0];

		if (empty($filename) || !file_exists(VREMEDIA . DIRECTORY_SEPARATOR . $filename))
		{
			$app->redirect('index.php?option=com_vikrestaurants&view=media');
			exit;
		}

		$this->media = VikRestaurantsHelper::getFileProperties(VREMEDIA . DIRECTORY_SEPARATOR . $filename);
		$this->thumb = VikRestaurantsHelper::getFileProperties(VREMEDIA_SMALL . DIRECTORY_SEPARATOR . $filename);

		// fetch media attributes
		$attrs = JModelVRE::getInstance('media')->getItem($this->media['name'], $new = true);

		// inject media attributes within the array
		foreach ($attrs as $k => $v)
		{
			// do not overwrite an existing attribute
			if (!isset($media[$k]))
			{
				$this->media[$k] = $v;
			}
		}

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

		// display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	private function addToolBar() {
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLEEDITMEDIA'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('media.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('media.saveclose', JText::translate('VRSAVEANDCLOSE'));

			if ($user->authorise('core.create', 'com_vikrestaurants'))
			{
				JToolbarHelper::save2new('media.savenew', JText::translate('VRSAVEANDNEW'));
			}
		}
		
		JToolbarHelper::cancel('media.cancel', 'JTOOLBAR_CLOSE');
	}
}
