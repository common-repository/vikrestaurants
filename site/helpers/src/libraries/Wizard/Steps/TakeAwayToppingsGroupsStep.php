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
 * Implement the wizard step used to create the groups
 * of toppings for take-away products.
 *
 * @since 1.9
 */
class TakeAwayToppingsGroupsStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'tkgroups';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKGROUPS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKGROUPS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-layer-group"></i>';
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
		// the step is completed after creating at least a group,
		// which must own at least a toppings
		foreach ($this->getToppingsGroups() as $group)
		{
			if ($group->toppings)
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
		// get menus dependency
		$menus = $this->getDependency('tkmenus');

		if ($menus)
		{
			// get menus list
			$menus = $menus->getMenus();

			// go to products list
			$task = '&view=tkproducts&id_takeaway_menu=' . $menus[0]->id;
		}
		else
		{
			$task = '';
		}

		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants' . $task . '" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
	}

	/**
	 * @inheritDoc
	 */
	public function canIgnore()
	{
		// get toppings dependency
		$toppings = $this->getDependency('tktoppings');

		if ($toppings)
		{
			// step can be ignored only in case of no toppings
			return count($toppings->getToppings()) == 0;
		}

		return true;
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
	 * Returns a list of created groups.
	 *
	 * @return  array  A list of groups.
	 */
	public function getToppingsGroups()
	{
		static $groups = null;

		// get groups only once
		if (is_null($groups))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['g.id', 'g.title']))
				->select('COUNT(1) AS ' . $db->qn('toppings'))
				->from($db->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g'))
				->leftjoin($db->qn('#__vikrestaurants_takeaway_group_topping_assoc', 'a') . ' ON ' . $db->qn('g.id') . ' = ' . $db->qn('a.id_group'))
				->group($db->qn('g.id'))
				->order($db->qn('g.id') . ' ASC');

			$db->setQuery($q);
			$groups = $db->loadObjectList();
		}

		return $groups;
	}
}
