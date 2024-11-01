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
 * VikRestaurants media upload view.
 *
 * @since 1.3
 */
class VikRestaurantsViewnewmedia extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		// set the toolbar
		$this->addToolBar();
	
		$prop = VikRestaurants::getMediaProperties();

		$this->properties = $prop;

		/**
		 * Check if we should prompt a message to
		 * guide the user about chaning the default
		 * size of the thumbnails.
		 *
		 * @since 1.8.2
		 */
		$this->showHelp = $app->input->getBool('configure');

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
	private function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLENEWMEDIA'), 'vikrestaurants');
		
		if (JFactory::getUser()->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('media.save', JText::translate('VRSAVE'));
		}
		
		JToolbarHelper::cancel('media.cancel');
	}
}
