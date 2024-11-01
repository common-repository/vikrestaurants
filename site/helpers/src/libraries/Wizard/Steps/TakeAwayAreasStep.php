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
 * Implement the wizard step used to create the
 * available delivery areas.
 *
 * @since 1.9
 */
class TakeAwayAreasStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'tkareas';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMENUTAKEAWAYDELIVERYAREAS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_TKAREAS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-map-marker-alt"></i>';
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
	public function getProgress()
	{
		$progress = 100;

		if (!$this->getGoogleAK())
		{
			// missing API Key, decrease progress
			$progress -= 50;
		}

		if (!$this->getAreas())
		{
			// missing delivery areas, decrease progress
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
		if ($this->getGoogleAK())
		{
			// point to controller to create a new delivery area
			return '<a href="index.php?option=com_vikrestaurants&task=tkarea.add" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
		}

		// use the default save button otherwise
		return parent::getExecuteButton();
	}

	/**
	 * @inheritDoc
	 */
	protected function doExecute($data)
	{
		$config = \VREFactory::getConfig();

		// update configuration value
		$config->set('googleapikey', $data->get('googleapikey'));

		return true;
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
		if ($sections && $sections->isCompleted() && $sections->isTakeAway() == false)
		{
			// take-away disabled, auto-ignore this step
			return true;
		}

		// get services dependency
		$services = $this->getDependency('tkservices');

		// make sure the delivery service is enabled
		if ($services && $services->isCompleted() && $services->isDelivery() == false)
		{
			// delivery service disabled, auto-ignore this step
			return true;
		}

		// otherwise lean on parent method
		return parent::isIgnored();
	}

	/**
	 * Returns a list of created delivery areas.
	 *
	 * @return  array  A list of delivery areas.
	 */
	public function getAreas()
	{
		static $areas = null;

		// get delivery areas only once
		if (is_null($areas))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['id', 'name', 'published']))
				->from($db->qn('#__vikrestaurants_takeaway_delivery_area'))
				->order($db->qn('ordering') . ' ASC');

			$db->setQuery($q);
			$areas = $db->loadObjectList();
		}

		return $areas;
	}

	/**
	 * Returns the configured Google API Key.
	 *
	 * @return  string
	 */
	public function getGoogleAK()
	{
		return \VREFactory::getConfig()->get('googleapikey');
	}
}
