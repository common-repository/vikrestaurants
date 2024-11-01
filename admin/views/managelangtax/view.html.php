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
 * VikRestaurants translation tax management view.
 *
 * @since 1.9
 */
class VikRestaurantsViewmanagelangtax extends JViewVRE
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
		
		$id_tax = $input->get('id_tax', 0, 'uint');

		$ids  = $input->get('cid', [], 'uint');
		$type = $ids ? 'edit' : 'new';

		// set the toolbar
		$this->addToolBar($type);

		/** @var \stdClass */
		$this->translation = JModelVRE::getInstance('langtax')->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->translation->id_tax)
		{
			// retrieve tax ID from translation object
			$id_tax = $this->translation->id_tax;
		}

		// use translated data stored in user state
		$this->injectUserStateData($this->translation, 'vre.langtax.data');

		// load original tax details
		$this->tax = JModelVRE::getInstance('tax')->getItem($id_tax);
		
		if (!$this->tax)
		{
			throw new RuntimeException('Record [' . $id_tax . '] not found', 404);
		}

		// obtain rules translations

		$this->rules = [];

		$q = $dbo->getQuery(true)
			->select($dbo->qn(['r.id', 'r.name', 'r.breakdown']))
			->select($dbo->qn('rl.id', 'lang_id'))
			->select($dbo->qn('rl.name', 'lang_name'))
			->select($dbo->qn('rl.breakdown', 'lang_breakdown'))
			->from($dbo->qn('#__vikrestaurants_tax_rule', 'r'))
			->leftjoin(
				$dbo->qn('#__vikrestaurants_lang_tax_rule', 'rl') 
				. ' ON ' . $dbo->qn('rl.id_tax_rule') . ' = ' . $dbo->qn('r.id')
				. ' AND ' . $dbo->qn('rl.id_parent') . ' = ' . (int) $this->translation->id
			)
			->where($dbo->qn('r.id_tax') . ' = ' . $this->tax->id)
			->order($dbo->qn('r.ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $rule)
		{
			// decode original breakdown
			$rule->breakdown = $rule->breakdown ? json_decode($rule->breakdown) : [];

			// decode translation breakdown
			$rule->lang_breakdown = $rule->lang_breakdown ? json_decode($rule->lang_breakdown, true) : [];

			$this->rules[] = $rule;
		}

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
			JToolbarHelper::apply('langtax.save', JText::translate('VRSAVE'));
			JToolbarHelper::save('langtax.saveclose', JText::translate('VRSAVEANDCLOSE'));
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants')
			&& $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::save2new('langtax.savenew', JText::translate('VRSAVEANDNEW'));
		}

		JToolbarHelper::cancel('langtax.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
