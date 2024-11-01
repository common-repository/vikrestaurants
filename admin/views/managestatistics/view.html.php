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
 * VikRestaurants statistics management view.
 *
 * @since 1.8
 */
class VikRestaurantsViewmanagestatistics extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();

		// recover user state
		$data = $app->getUserState('vre.statistics.data', []);

		if (!empty($data['group']))
		{
			// get group from user state
			$this->group = $data['group'];
		}
		else
		{
			// get group from request
			$this->group = $app->input->get('group', 'restaurant', 'string');
		}

		if (!empty($data['location']))
		{
			// get location from user state
			$this->location = $data['location'];
		}
		else
		{
			// get location from request
			$this->location = $app->input->get('location', 'statistics', 'string');
		}
		
		// set the toolbar
		$this->addToolBar();

		VRELoader::import('library.statistics.factory');

		// load active widgets
		$this->dashboard = VREStatisticsFactory::getDashboard($this->group, $this->location);

		// get supported widgets
		$this->supported = VREStatisticsFactory::getSupportedWidgets($this->group);

		// get supported positions
		$this->positions = VREStatisticsFactory::getSupportedPositions($this->group, $this->location);

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();
		
		// display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return  void
	 */
	private function addToolBar()
	{
		// fetch page title
		if ($this->location == 'dashboard')
		{
			if ($this->group == 'restaurant')
			{
				$title = JText::translate('VRMAINTITLEVIEWDASHBOARDRS');
			}
			else
			{
				$title = JText::translate('VRMAINTITLEVIEWDASHBOARDTK');
			}
		}
		else
		{
			if ($this->group == 'restaurant')
			{
				$title = JText::translate('VRMAINTITLEVIEWSTATISTICS');
			}
			else
			{
				$title = JText::translate('VRMAINTITLEVIEWTKSTATISTICS');
			}
		}

		// add menu title and some buttons to the page
		JToolbarHelper::title($title, 'vikrestaurants');

		/**
		 * Calculate the ACL rule according to
		 * the specified request data.
		 *
		 * @since 1.8.3
		 */
		$acl = $this->getACL();

		if (JFactory::getUser()->authorise($acl, 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('statistics.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('statistics.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		JToolbarHelper::cancel('statistics.cancel', 'JTOOLBAR_CLOSE');
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

		if ($this->location == 'dashboard')
		{
			// allow dashboard management
			$acl = 'core.access.dashboard';
		}
		else if ($this->location == 'statistics')
		{
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
		}

		return $acl;
	}
}
