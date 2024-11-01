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
 * take-away orders with a status contained by the supported ones.
 *
 * @since 1.9
 */
class StatusFilter extends ConditionalTextFilterAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		$statusCodes = [];

		// obtain all the status codes
		foreach (\JHtml::fetch('vrehtml.status.find', ['code', 'name'], []) as $status)
		{
			$statusCodes[$status->code] = $status->name;
		}

		return [
			/**
			 * A list of allowed status codes.
			 * 
			 * @var string[]
			 */
			'statuses' => [
				'type'  => 'select',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_STATUS_LIST'),
				'value' => $this->options->get('statuses', []),
				'multiple' => true,
				'options' => $statusCodes,
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-thumbtack';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		$statusCodes = [];

		// create status codes lookup
		foreach (\JHtml::fetch('vrehtml.status.find', ['code', 'name'], []) as $status)
		{
			$statusCodes[$status->code] = $status->name;
		}

		$items = [];

		foreach ($this->options->get('statuses', []) as $code)
		{
			if (!isset($statusCodes[$code]))
			{
				// status code no longer available
				continue;
			}

			// register the status name only
			$items[] = $statusCodes[$code];
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

		// make sure the status of the reservation/order is supported
		return in_array($order->status, $this->options->get('statuses', []));
	}
}
