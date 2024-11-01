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
 * VikRestaurants configuration view.
 *
 * @since 1.0
 */
class VikRestaurantsVieweditconfig extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$db = JFactory::getDbo();	

		// set the toolbar
		$this->addToolBar();
		
		$this->params = [];
		
		$q = $db->getQuery(true)
			->select($db->qn(['param', 'setting']))
			->from($db->qn('#__vikrestaurants_config'));

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $row)
		{
			$this->params[$row->param] = $row->setting;
		}

		/**
		 * Added support for configuration translations.
		 *
		 * @since 1.8
		 */
		$defaultSiteLang = VikRestaurants::getDefaultLanguage();

		$this->translations = [
			'symbpos'          => [$defaultSiteLang],
			'currdecimalsep'   => [$defaultSiteLang],
			'currthousandssep' => [$defaultSiteLang],
			'policylink'       => [$defaultSiteLang],
		];

		$q = $db->getQuery(true)
			->select($db->qn(['param', 'tag']))
			->from($db->qn('#__vikrestaurants_lang_config'));
		
		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $t)
		{
			if (!isset($this->translations[$t->param]))
			{
				continue;
			}

			if (!in_array($t->tag, $this->translations[$t->param]))
			{
				$this->translations[$t->param][] = $t->tag;
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
	private function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLECONFIG'), 'vikrestaurants');

		if (JFactory::getUser()->authorise('core.access.config', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('configuration.save', JText::translate('VRSAVE'));
		}
	
		JToolbarHelper::cancel('dashboard.cancel', JText::translate('JTOOLBAR_CLOSE'));
	}
}
