<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Wizard\Steps;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Wizard\WizardStep;

/**
 * Implement the wizard step used to create the menus
 * of the restaurant.
 *
 * @since 1.9
 */
class RestaurantMenusStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'menus';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMENUMENUS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_MENUS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-bars"></i>';
	}

	/**
	 * @inheritDoc
	 */
	public function getGroup()
	{
		// belongs to RESTAURANT group
		return \JText::translate('VRMENUTITLEHEADER1');
	}

	/**
	 * @inheritDoc
	 */
	public function isCompleted()
	{
		// the step is completed after creating at least a menu,
		// which must be assigned at least to a section
		return (bool) $this->getMenus();
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants&task=menu.add" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
	}

	/**
	 * @inheritDoc
	 */
	public function canIgnore()
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function isIgnored()
	{
		// get sections dependency
		$sections = $this->getDependency('sections');

		// make sure the restaurant section is enabled
		if ($sections && $sections->isRestaurant() == false)
		{
			// restaurant disabled, auto-ignore this step
			return true;
		}

		// otherwise lean on parent method
		return parent::isIgnored();
	}

	/**
	 * Returns a list of created menus.
	 *
	 * @return  array  A list of menus.
	 */
	public function getMenus()
	{
		static $menus = null;

		// get menus only once
		if (is_null($menus))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['m.id', 'm.name', 'm.published']))
				->from($db->qn('#__vikrestaurants_menus', 'm'))
				->from($db->qn('#__vikrestaurants_menus_section', 's'))
				->where($db->qn('m.id') . ' = ' . $db->qn('s.id_menu'))
				->group($db->qn('m.id'))
				->order($db->qn('m.ordering') . ' ASC');

			$db->setQuery($q);
			$menus = $db->loadObjectList();
		}

		return $menus;
	}
}
