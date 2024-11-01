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
 * VikRestaurants dashboard view.
 *
 * @since 1.0
 */
class VikRestaurantsViewrestaurant extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$wizard = VREApplication::getInstance()->getWizard();

		if ($wizard->isDone())
		{
			VRELoader::import('library.statistics.factory');

			// load active widgets
			$this->dashboard = [
				'restaurant' => VREStatisticsFactory::getDashboard('restaurant', 'dashboard'),
				'takeaway'   => VREStatisticsFactory::getDashboard('takeaway', 'dashboard'),
			];
			
			$this->layout = 'dashboard';
		}
		else
		{
			/**
			 * Added support for wizard page.
			 *
			 * @since 1.8.3
			 */
			$this->setLayout('wizard');

			$this->wizard = $wizard;
		}

		// set the toolbar
		$this->addToolBar();

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();
		
		// display the template (default.php)
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
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWDASHBOARD'), 'vikrestaurants');

		if (JFactory::getUser()->authorise('core.access.dashboard', 'com_vikrestaurants'))
		{
			if (isset($this->dashboard))
			{
				// add button to manage dashboard widgets
				JToolbarHelper::addNew('statistics.add', JText::translate('VRE_TOOLBAR_NEW_WIDGET'));
			}
			else
			{
				// add button to dismiss the wizard
				JToolbarHelper::custom('wizard.done', 'cancel', 'cancel', JText::translate('VRWIZARDBTNDONE'), false);
			}

			JToolbarHelper::preferences('com_vikrestaurants');
		}
	}
}
