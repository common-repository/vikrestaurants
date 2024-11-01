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
 * Implement the wizard step used to choose the active services
 * of the take-away: delivery, pickup or both.
 *
 * @since 1.9
 */
class TakeAwayServicesStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'tkservices';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMANAGETKRES4');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKSERVICES_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-truck fa-flip-horizontal"></i>';
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
	 * @inheritDoc
	 */
	protected function doExecute($data)
	{
		$config = \VREFactory::getConfig();

		$delivery = (int) $data->get('delivery');
		$pickup   = (int) $data->get('pickup');

		if ($delivery && $pickup)
		{
			// activate both services
			$service = 2;
		}
		else if ($delivery)
		{
			// activate delivery only
			$service = 1;
		}
		else
		{
			// activate pickup only
			$service = 0;
		}

		// update configuration value
		$config->set('deliveryservice', $service);

		return true;
	}

	/**
	 * Checks whether the delivery service has been disabled.
	 *
	 * @return  bool  True if enabled, false otherwise.
	 */
	public function isDelivery()
	{
		return \VREFactory::getConfig()->getUint('deliveryservice') != 0;
	}

	/**
	 * Checks whether the pickup service has been disabled.
	 *
	 * @return  bool  True if enabled, false otherwise.
	 */
	public function isPickup()
	{
		return \VREFactory::getConfig()->getUint('deliveryservice') != 1;
	}
}
