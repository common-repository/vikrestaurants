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
 * Implement the wizard step used to create the tables
 * of the restaurant.
 *
 * @since 1.9
 */
class RestaurantTablesStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'tables';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMENUTABLES');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_TABLES_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-th"></i>';
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
		// the step is completed after creating at least a table
		return (bool) $this->getTables();
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		// get room dependency
		$dep = $this->getDependency('rooms');

		if ($dep)
		{
			// get rooms
			$rooms = $dep->getRooms();
		}
		else
		{
			$rooms = array();
		}

		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants&task=map.edit' . ($rooms ? '&selectedroom=' . $rooms[0]->id : '') . '&wizard=1" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
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
	 * Returns a list of created tables.
	 *
	 * @return  array  A list of tables.
	 */
	public function getTables()
	{
		static $tables = null;

		// get tables only once
		if (is_null($tables))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['name', 'published']))
				->from($db->qn('#__vikrestaurants_table'))
				->order($db->qn('id') . ' ASC');

			$db->setQuery($q);
			$tables = $db->loadObjectList();
		}

		return $tables;
	}
}
