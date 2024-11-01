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
 * VikRestaurants applications configuration view.
 *
 * @since 1.9
 */
class VikRestaurantsVieweditconfigapp extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();	

		$root = JUri::root();

		$cookie = $app->input->cookie;

		$this->filters = [
			'preview_status' => $cookie->getBool('vikrestaurants_customizer_preview_status', true),
			'preview_page'   => $cookie->getString('vikrestaurants_customizer_preview_page', $root),
		];

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
		 * Fetch all the supported backup export types.
		 * 
		 * @var E4J\VikRestaurants\Backup\Export\Type[]
		 * @since 1.9
		 */
		$this->backupExportTypes = JModelVRE::getInstance('backup')->getExportTypes();

		/**
		 * Fetch default country by using the custom fields helper.
		 *
		 * @since 1.8
		 */
		$this->defaultCountry = E4J\VikRestaurants\CustomFields\Helpers\FieldsHelper::getDefaultCountryCode();

		/**
		 * Added support for configuration translations.
		 *
		 * @since 1.8
		 */
		$defaultSiteLang = VikRestaurants::getDefaultLanguage();

		$this->translations = [
			'smstmplcust'   => [$defaultSiteLang],
			'smstmpltkcust' => [$defaultSiteLang],
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

		// create customizer model
		$this->customizerModel = JModelVRE::getInstance('customizer');

		// fetch variables tree of CSS customizer
		$this->customizerTree = $this->customizerModel->getVarsTree($maxLevels = 3);
		$this->customizerTree = $this->normalizeCustomizerForm($this->customizerTree);

		// fetch all the support pages
		$this->menuItems = [];

		if (VersionListener::isJoomla())
		{
			// get site menu
			$menu = JApplicationCms::getInstance('site')->getMenu()->getMenu();
			
			foreach ($menu as $item)
			{
				// build menu item name
				$text = str_repeat('- ', $item->level - 1) . $item->title;

				if ($item->language && $item->language !== '*')
				{
					// append language tag
					$text .= ' (' . $item->language . ')';
				}

				$value = $root;

				// exclude root in case of HOME
				if (!$item->home)
				{
					$uri = new JUri($item->link);

					if ($item->language && $item->language !== '*')
					{
						// inject the language tag within the plain URL of the menu item
						$uri->setVar('lang', $item->language);
					}

					// route the menu item uri
					$value = VREFactory::getPlatform()->getUri()->route((string) $uri, false);
				}

				// register menu item
				$this->menuItems[] = JHtml::fetch('select.option', $value, $text);
			}
		}
		else if (VersionListener::isWordpress())
		{
			// add empty option (HOME)
			$this->menuItems[] = JHtml::fetch('select.option', $root, JText::translate('JGLOBAL_SELECT_AN_OPTION'));

			// iterate all theme locations
			foreach (get_nav_menu_locations() as $l)
			{
				// iterate all menu items assigned to this location
				foreach (wp_get_nav_menu_items($l) as $item)
				{
					// register menu item
					$this->menuItems[] = JHtml::fetch('select.option', $item->url, $item->title);
				}
			}
		}

		/**
		 * Code hub instance.
		 * 
		 * @var E4J\VikRestaurants\CodeHub\CodeHub
		 * @since 1.9
		 */
		$this->codeHub = VREFactory::getCodeHub();

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

		// we need to load the front-end language to properly access the default text of the SMS templates
		VikRestaurants::loadLanguage(JFactory::getLanguage()->getTag());

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
		JToolbarHelper::title(JText::translate('VRMAINTITLECONFIGAPP'), 'vikrestaurants');

		if (JFactory::getUser()->authorise('core.access.config', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('configapp.save', JText::translate('VRSAVE'));
		}
	
		JToolbarHelper::cancel('dashboard.cancel', JText::translate('JTOOLBAR_CLOSE'));
	}

	/**
	 * Normalizes the levels of tree in order to construct a form fields.
	 * 
	 * @param   array  $tree
	 * 
	 * @return  array
	 */
	private function normalizeCustomizerForm(array $tree)
	{
		$form = [];

		// scan all elements
		foreach ($tree as $elementName => $element)
		{
			$form[$elementName] = [];

			// scan all fieldsets
			foreach ($element as $fieldsetName => $fields)
			{
				$form[$elementName][$fieldsetName] = [];

				// scan all fields
				foreach ($fields as $fieldName => $field)
				{
					if (isset($field['key']))
					{
						// field given
						$field['label'] = $fieldName;
						$form[$elementName][$fieldsetName][$fieldName] = $field;
					}
					else
					{
						// separator given
						$form[$elementName][$fieldsetName][$fieldName] = [
							'key'   => $fieldName,
							'val'   => null,
							'type'  => 'separator',
							'label' => $fieldName,
						];

						// scan all the fields of the separator
						foreach ($field as $subFieldName => $subField)
						{
							$subField['label'] = $subFieldName;
							$form[$elementName][$fieldsetName][$fieldName . '_' . $subFieldName] = $subField;
						}
					}
				}
			}
		}

		return $form;
	}
}
