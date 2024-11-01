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
 * Implement the wizard step used to configure the
 * payment gateways.
 *
 * @since 1.9
 */
class PaymentsStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMENUPAYMENTS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_PAYMENTS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-credit-card"></i>';
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
	public function isCompleted()
	{
		// the step is completed after publishing at least a payment
		foreach ($this->getPayments() as $payment)
		{
			if ($payment->published)
			{
				// payment published
				return true;
			}
		}

		// no published payments
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		// get payments list
		$payments = $this->getPayments();

		if ($payments)
		{
			// point to the controller for editing an existing payment
			return '<a href="index.php?option=com_vikrestaurants&task=payment.edit&cid[]=' . $payments[0]->id . '" class="btn btn-success">' . \JText::translate('VREDIT') . '</a>';
		}

		// point to the controller for creating a new payment
		return '<a href="index.php?option=com_vikrestaurants&task=payment.add" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
	}

	/**
	 * @inheritDoc
	 */
	public function canIgnore()
	{
		return true;
	}

	/**
	 * Returns a list of created payments.
	 *
	 * @return  array  A list of payments.
	 */
	public function getPayments()
	{
		static $payments = null;

		// get payments only once
		if (is_null($payments))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['id', 'name', 'file', 'group', 'published']))
				->from($db->qn('#__vikrestaurants_gpayments'))
				->order($db->qn('ordering') . ' ASC');

			$db->setQuery($q);
			$payments = $db->loadObjectList();
		}

		return $payments;
	}
}
