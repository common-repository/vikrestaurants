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
 * Implement the wizard step used to choose the active sections
 * of the program: restaurant, take-away or both.
 *
 * @since 1.8.3
 */
class SectionsStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRE_WIZARD_STEP_SECTIONS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_SECTIONS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-sliders-h"></i>';
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
	protected function doExecute($data)
	{
		$config = \VREFactory::getConfig();

		// enable restaurant section according to the given value
		$config->set('enablerestaurant', (int) $data->get('restaurant'));
		// enable take-away section according to the given value
		$config->set('enabletakeaway', (int) $data->get('takeaway'));

		return true;
	}

	/**
	 * Checks whether the restaurant has been enabled.
	 *
	 * @return  bool  True if enabled, false otherwise
	 */
	public function isRestaurant()
	{
		return \VikRestaurants::isRestaurantEnabled();
	}

	/**
	 * Checks whether the take-away has been enabled.
	 *
	 * @return  bool  True if enabled, false otherwise
	 */
	public function isTakeAway()
	{
		return \VikRestaurants::isTakeAwayEnabled();
	}
}
