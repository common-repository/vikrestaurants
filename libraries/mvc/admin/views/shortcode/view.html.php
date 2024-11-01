<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants shortcode view.
 * @wponly
 *
 * @since 1.0
 */
class VikRestaurantsViewShortcode extends JView
{
	/**
	 * @override
	 * View display method.
	 *
	 * @return 	void
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		$model = $this->getModel();

		$return = $input->getBase64('return', '');

		$shortcode = (array) $model->loadFormData();

		JLoader::import('adapter.filesystem.folder');

		// views

		$views = [];

		// get all the views that contain a default.xml file
		// [0] : base path
		// [1] : query
		// [2] : true for recursive search
		// [3] : true to return full paths
		$files = JFolder::files(VREBASE . DIRECTORY_SEPARATOR . 'views', 'default.xml', true, true);

		foreach ($files as $f)
		{
			// retrieve the view ID from the path: /views/[ID]/tmpl/default.xml
			if (preg_match("/[\/\\\\]views[\/\\\\](.*?)[\/\\\\]tmpl[\/\\\\]default\.xml$/i", $f, $matches))
			{
				$id = $matches[1];
				// load the XML form
				$form = JForm::getInstance($id, $f);
				// get the view title
				$views[$id] = array(
					'name' => (string) $form->getXml()->layout->attributes()->title,
					'desc' => (string) $form->getXml()->layout->message,
				);
			}
		}

		// obtain the type form
		$form = $model->getTypeForm($shortcode);
		
		$this->shortcode  = $shortcode;
		$this->views      = $views;
		$this->returnLink = $return;
		$this->form       = $form;

		$this->shortcodesList = JModelVRE::getInstance('shortcodes')->all();

		$this->addToolbar();

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();
		
		// display parent
		parent::display($tpl);
	}

	/**
	 * Helper method to setup the toolbar.
	 *
	 * @return 	void
	 */
	public function addToolbar()
	{
		if ($this->shortcode['id'] > 0)
		{
			JToolbarHelper::title(JText::translate('VREEDITSHORTCDMENUTITLE'));
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRENEWSHORTCDMENUTITLE'));
		}

		JToolbarHelper::apply('shortcode.save');
		JToolbarHelper::save('shortcode.saveclose');
		JToolbarHelper::save2new('shortcode.savenew');
		JToolbarHelper::cancel('shortcodes.cancel', $this->shortcode['id'] ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
