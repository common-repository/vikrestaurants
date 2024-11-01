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
 * Implement the wizard step used to create the attributes
 * assignable to the take-away products.
 *
 * @since 1.9
 */
class TakeAwayAttributesStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'tkattributes';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMANAGETKMENU18');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKATTR_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-carrot"></i>';
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
		// get list of attributes
		$attributes = $this->getAttributes();

		// take last created attribute
		$last = $attributes[count($attributes) - 1];

		// The step is completed after creating at least an attribute.
		// Rely on ID because we need to exclude the pre-installed ones.
		return $last->id > 3;
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants&view=tkmenuattr" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
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
	 * Returns a list of created attributes.
	 *
	 * @return  array  A list of attributes.
	 */
	public function getAttributes()
	{
		static $attributes = null;

		// get attributes only once
		if (is_null($attributes))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['id', 'name', 'published']))
				->from($db->qn('#__vikrestaurants_takeaway_menus_attribute'))
				->order($db->qn('id') . ' ASC');

			$db->setQuery($q);
			$attributes = $db->loadObjectList();
		}

		return $attributes;
	}
}
