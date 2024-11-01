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
 * Implement the wizard step used to define the openings.
 *
 * @since 1.9
 */
class OpeningsStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRE_WIZARD_STEP_OPENINGS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_OPENINGS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-clock"></i>';
	}

	/**
	 * @inheritDoc
	 */
	public function getGroup()
	{
		// belongs to GLOBAL group
		return \JText::translate('VRMENUTITLEHEADER4');
	}

	/**
	 * @inheritDoc
	 */
	public function getProgress()
	{
		$progress = 100;

		if ($this->needShift('restaurant'))
		{
			// missing shift for restaurant section, decrease progress
			$progress -= 50;
		}

		if ($this->needShift('takeaway'))
		{
			// missing shift for take-away section, decrease progress
			$progress -= 50;
		}

		return $progress;
	}

	/**
	 * @inheritDoc
	 */
	public function isCompleted()
	{
		// look for 100% completion progress
		return $this->getProgress() == 100;
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		if ($this->needShift('restaurant'))
		{
			$group = 1;
		}
		else if ($this->needShift('takeaway'))
		{
			$group = 2;
		}
		else
		{
			$group = null;
		}

		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants&task=shift.add' . ($group ? '&group=' . $group : '') . '" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
	}

	/**
	 * Returns a list of created shifts.
	 *
	 * @return  array  A list of shifts.
	 */
	public function getShifts()
	{
		static $shifts = null;

		// get shifts only once
		if (is_null($shifts))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['name', 'group']))
				->from($db->qn('#__vikrestaurants_shifts'))
				->order($db->qn('id') . ' ASC');

			$db->setQuery($q);
			$shifts = $db->loadObjectList();
		}

		return $shifts;
	}

	/**
	 * Checks whether the specified group needs the
	 * creation of a working shift.
	 *
	 * @param   mixed  $group  Either the group ID or its alias.
	 *
	 * @return  bool   True if a shift is needed, false otherwise.
	 */
	public function needShift($group)
	{
		// the step is completed after creating at least a shift
		// for each active section
		$groups = array_map(function($shift)
		{
			return $shift->group;
		}, $this->getShifts());

		$lookup = [
			'restaurant' => 1,
			'takeaway'   => 2,
		];

		// try to route alias
		$group = isset($lookup[$group]) ? $lookup[$group] : $group;

		// get sections dependency
		$sections = $this->getDependency('sections');

		// check if the group is enabled
		switch ($group)
		{
			case 1:
				$enabled = $sections && $sections->isRestaurant();
				break;

			case 2:
				$enabled = $sections && $sections->isTakeAway();
				break;

			default:
				$enabled = false;
		}

		// in case the group is active, check whether the list
		// contains at list a shift for the specified group
		return $enabled && !in_array($group, $groups);
	}
}
