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
 * and the products of the take-away.
 *
 * @since 1.9
 */
class TakeAwayMenusStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'tkmenus';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKMENUS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKMENUS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-pizza-slice"></i>';
	}

	/**
	 * @inheritDoc
	 */
	public function getGroup()
	{
		// belongs to TAKEAWAY group
		return \JText::translate('VRMENUTITLEHEADER5');
	}

	/**
	 * @inheritDoc
	 */
	public function isCompleted()
	{
		// the step is completed after creating at least a menu,
		// which must own at least a product
		foreach ($this->getMenus() as $menu)
		{
			if ($menu->products)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		$menus = $this->getMenus();

		if ($menus)
		{
			// create product
			$task = 'tkentry.add&id_takeaway_menu=' . $menus[0]->id;
		}
		else
		{
			// create menu
			$task = 'tkmenu.add';
		}

		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants&task=' . $task . '" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
	}

	/**
	 * @inheritDoc
	 */
	public function isIgnored()
	{
		// get sections dependency
		$sections = $this->getDependency('sections');

		// make sure the take-away section is enabled
		if ($sections && $sections->isTakeAway() == false)
		{
			// take-away disabled, auto-ignore this step
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
				->select($db->qn(['m.id', 'm.title', 'm.published']))
				->select('COUNT(1) AS ' . $db->qn('products'))
				->from($db->qn('#__vikrestaurants_takeaway_menus', 'm'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('e.id_takeaway_menu'))
				->group($db->qn('m.id'))
				->order($db->qn('m.ordering') . ' ASC');

			$db->setQuery($q);
			$menus = $db->loadObjectList();
		}

		return $menus;
	}
}
