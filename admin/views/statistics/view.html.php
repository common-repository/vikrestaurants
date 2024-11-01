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
 * VikRestaurants reservations/orders statistics view.
 *
 * @since 1.5
 */
class VikRestaurantsViewstatistics extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();

		// get group
		$this->group = $app->input->get('group', 'restaurant', 'string');

		// search for a layout mode
		$this->layout = $app->input->get('layoutmode', 'floating', 'string');
		
		// set the toolbar
		$this->addToolBar();

		VRELoader::import('library.statistics.factory');

		// load active widgets
		$this->dashboard = VREStatisticsFactory::getDashboard($this->group, 'statistics');

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
		if ($this->group == 'restaurant')
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWSTATISTICS'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWTKSTATISTICS'), 'vikrestaurants');
		}

		/**
		 * Calculate the ACL rule according to
		 * the specified request data.
		 *
		 * @since 1.8.3
		 */
		$acl = $this->getACL();

		if (JFactory::getUser()->authorise($acl, 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('statistics.add', JText::translate('VRE_TOOLBAR_NEW_WIDGET'));
		}

		if ($this->group == 'restaurant')
		{
			JToolbarHelper::cancel('reservation.cancel', 'JTOOLBAR_CLOSE');
		}
		else
		{
			JToolbarHelper::cancel('tkreservation.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	/**
	 * Calculate the ACL rule according to the specified request data.
	 *
	 * @return 	string  The related ACL rule.
	 *
	 * @since 	1.8.3
	 */
	protected function getACL()
	{
		// default super user
		$acl = 'core.admin';

		if ($this->group == 'restaurant')
		{
			// allow reservations management
			$acl = 'core.access.reservations';
		}
		else if ($this->group == 'takeaway')
		{
			// allow orders management
			$acl = 'core.access.tkorders';
		}

		return $acl;
	}
}
