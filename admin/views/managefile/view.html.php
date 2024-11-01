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
 * VikRestaurants file management view.
 *
 * @since 1.3
 */
class VikRestaurantsViewmanagefile extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$input = JFactory::getApplication()->input;

		// check if we should use a blank component layout
		$this->blank = $input->get('tmpl') == 'component';

		if (!$this->blank)
		{
			// set the toolbar
			$this->addToolBar();
		}
		
		// get files
		$file = $input->get('cid', [''], 'string');

		/** @var \JModelLegacy */
		$model = JModelVRE::getInstance('file');

		/** @var \stdClass|null */
		$this->item = $model->getItem($file[0]);

		if (!$this->item)
		{
			$error = $model->getError();

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			// throw exception with error found
			throw $error;
		}

		// use file data stored in user state
		$this->injectUserStateData($this->item, 'vre.file.data');

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
	private function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title('VikRestaurants - Manage File', 'vikrestaurants');
		
		$user = JFactory::getUser();
		
		if ($user->authorise('core.admin', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('file.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('file.savecopy', JText::translate('VRSAVEASCOPY'));
		}
		
		JToolbarHelper::cancel('file.cancel', 'JTOOLBAR_CLOSE');
	}
}
