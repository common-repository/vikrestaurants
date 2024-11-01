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
 * take-away orders with a matching reservation code.
 *
 * @since 1.9
 */
class ReservationCodeFilter extends ConditionalTextFilterAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		$codes = [];

		// obtain all the reservation codes for the restaurant
		$codes[\JText::translate('VRMENUTITLEHEADER1')] = \JHtml::fetch('vikrestaurants.rescodes', 1);

		// obtain all the reservation codes for the take-away
		$codes[\JText::translate('VRMENUTITLEHEADER5')] = \JHtml::fetch('vikrestaurants.rescodes', 2);

		return [
			/**
			 * A list of allowed reservation codes.
			 * 
			 * @var string[]
			 */
			'codes' => [
				'type'  => 'groupedlist',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_RESERVATIONCODE_LIST'),
				'value' => $this->options->get('codes', []),
				'multiple' => true,
				'options' => $codes,
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-tag';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		$items = [];

		// iterate all the reservation codes
		foreach ($this->options->get('codes', []) as $codeId)
		{
			// fetch code details
			$code = \JHtml::fetch('vikrestaurants.rescode', $codeId);

			if (!$code)
			{
				// code no longer available
				continue;
			}

			// register name only
			$items[] = $code->code;
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

		// make sure the reservation code is supported
		return in_array($order->rescode, $this->options->get('codes', []));
	}
}
