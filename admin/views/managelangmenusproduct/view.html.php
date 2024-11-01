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
 * VikRestaurants language menus product management view.
 *
 * @since 1.8
 */
class VikRestaurantsViewmanagelangmenusproduct extends JViewVRE
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
		$dbo   = JFactory::getDbo();
		
		$id_product = $input->get('id_product', 0, 'uint');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';
		
		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langmenusproduct')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_product)
		{
			// retrieve product ID from translation object
			$id_product = $this->translation->id_product;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langmenusproduct.data');

		// load original product details
		$this->product = JModelVRE::getInstance('menusproduct')->getItem($id_product);
		
		if (!$this->product)
		{
			throw new RuntimeException('Record [' . $id_product . '] not found', 404);
		}

		// obtain variations translations

		$q = $dbo->getQuery(true)
			->select($dbo->qn(['o.id', 'o.name']))
			->select($dbo->qn('ol.id', 'lang_id'))
			->select($dbo->qn('ol.name', 'lang_name'))
			->from($dbo->qn('#__vikrestaurants_section_product_option', 'o'))
			->leftjoin(
				$dbo->qn('#__vikrestaurants_lang_section_product_option', 'ol') 
				. ' ON ' . $dbo->qn('ol.id_option') . ' = ' . $dbo->qn('o.id')
				. ' AND ' . $dbo->qn('ol.id_parent') . ' = ' . (int) $this->translation->id
			)
			->where($dbo->qn('o.id_product') . ' = ' . $this->product->id)
			->order($dbo->qn('o.ordering') . ' ASC');

		$dbo->setQuery($q);
		$this->variations = $dbo->loadObjectList();

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
	private function addToolBar($type)
	{
		// add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolbarHelper::title(JText::translate('VRE_TRX_EDIT_TITLE'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRE_TRX_NEW_TITLE'), 'vikrestaurants');
		}
		
		$user = JFactory::getUser();
		
		if ($user->authorise('core.edit', 'com_vikrestaurants')
			|| $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::apply('langmenusproduct.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langmenusproduct.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langmenusproduct.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langmenusproduct.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
