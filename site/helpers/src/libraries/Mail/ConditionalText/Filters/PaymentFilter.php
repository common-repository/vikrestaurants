<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextFilterAware;
use E4J\VikRestaurants\Mail\ConditionalText\Helpers\CountableItemsSummaryTrait;

/**
 * Applies the conditional text only to those restaurant reservations and/or
 * take-away orders with a payment method contained by the supported ones.
 *
 * @since 1.9
 */
class PaymentFilter extends ConditionalTextFilterAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * A list of allowed payment methods.
			 * 
			 * @var string[]
			 */
			'payments' => [
				'type'  => 'groupedlist',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_PAYMENT_METHODS'),
				'value' => $this->options->get('payments', []),
				'multiple' => true,
				'options' => \JHtml::fetch('vrehtml.admin.payments', $group = null, $blank = false, $categorize = true),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-credit-card';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		// obtain the details of the payments that have been selected by this filter
		$payments = \E4J\VikRestaurants\Payment\PaymentsCollection::getInstance()
			->filter(new \E4J\VikRestaurants\Collection\Filters\ArrayFilter('id', $this->options->get('payments', [])));

		/** @var E4J\VikRestaurants\Collection\Item[] $payments */

		$items = [];

		// iterate all the payment methods
		foreach ($payments as $payment)
		{
			// register name only
			$items[] = $payment->get('name');
		}

		/** @see CountableItemsSummaryTrait */
		return $this->createSummary($items);
	}

	/**
	 * @inheritDoc
	 */
	public function isEligible(string $templateId, array $data)
	{
		$order = $data[0] ?? null;

		if (!$order instanceof \VREOrderWrapper)
		{
			// the provided e-mail template is not observable
			return false;
		}

		// fetch selected payments
		$payments = $this->options->get('payments', []);

		// compare selected payment (if any) with the selected ones
		return (!$payments && !$order->payment) || ($order->payment && in_array($order->payment->id, $payments));
	}
}
