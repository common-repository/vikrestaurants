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
 * VikRestaurants reservations/orders export view.
 *
 * @since 1.2
 */
class VikRestaurantsViewexportres extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		// get type set in request
		$this->data = new stdClass;
		$this->data->type     = $input->get('type', null, 'string');
		$this->data->cid      = $input->get('cid', [], 'uint');
		$this->data->fromdate = $input->get('fromdate', '', 'string');
		$this->data->todate   = $input->get('todate', '', 'string');

		// retrieve data from user state
		$this->injectUserStateData($this->data, 'vre.exportres.data');

		// make sure the group is supported
		$this->data->type = JHtml::fetch('vrehtml.admin.getgroup', $this->data->type, ['restaurant', 'takeaway']);
		
		// set the toolbar
		$this->addToolBar($this->data->type);

		VRELoader::import('library.order.export.factory');

		// get supported drivers
		$this->drivers = VREOrderExportFactory::getSupportedDrivers($this->data->type);

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

		// display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @param 	string 	$type  The export type ('restaurant' or 'takeaway').
	 *
	 * @return 	void
	 */
	private function addToolBar($type)
	{
		if ($type == 'restaurant')
		{
			$title = JText::translate('VRMAINTITLEEXPORTRES');
			$acl   = 'reservations';
		}
		else
		{
			$title = JText::translate('VRMAINTITLETKEXPORTRES');
			$acl   = 'tkorders';
		}
		
		JToolbarHelper::title($title, 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.access.' . $acl, 'com_vikrestaurants'))
		{
			JToolbarHelper::custom('exportres.save', 'download', 'download', JText::translate('VRDOWNLOAD'), false);
		}
		
		JToolbarHelper::cancel('exportres.cancel', 'JTOOLBAR_CANCEL');
	}
}
