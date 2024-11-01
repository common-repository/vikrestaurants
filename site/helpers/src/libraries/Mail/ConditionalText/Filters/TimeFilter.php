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

/**
 * Applies the conditional text only to those restaurant reservations and/or
 * take-away orders with a checkin between the specified times.
 *
 * @since 1.9
 */
class TimeFilter extends ConditionalTextFilterAware
{
	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		$times = [];

		// add option to ignore the from time
		$times[] = \JHtml::fetch('select.option', '*', \JText::translate('VRANY'));

		// create hour:min dropdown
		for ($hm = 0; $hm <= 1440; $hm += 15)
		{
			$times[] = \JHtml::fetch('select.option', $hm, \JHtml::fetch('vikrestaurants.min2time', $hm));
		}

		return [
			/**
			 * The from time.
			 * 
			 * @var int|string
			 */
			'from' => [
				'type'  => 'select',
				'label' => \JText::translate('VRMANAGEROOM7'),
				'value' => $this->options->get('from', '*'),
				'options' => $times,
			],

			/**
			 * The end time.
			 * 
			 * @var int|string
			 */
			'end' => [
				'type'  => 'select',
				'label' => \JText::translate('VRMANAGEROOM8'),
				'value' => $this->options->get('end', '*'),
				'options' => $times,
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-clock';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		// get from time
		$from = $this->options->get('from', 0);

		if ($from === '*')
		{
			// any from hour, reset to 00:00
			$from = 0;
		}

		// get end time
		$end = $this->options->get('end', 1440);

		if ($end === '*')
		{
			// any end hour, reset to 24:00
			$end = 1440;
		}

		// format times (ignore end if equals to from)
		return \JHtml::fetch('vikrestaurants.min2time', $from) . ($from != $end ? ' - ' . \JHtml::fetch('vikrestaurants.min2time', $end) : '');
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

		// convert check-in timestamp into a time
		$checkin = \JHtml::fetch('vikrestaurants.time2min', date('H:i', $order->checkin_ts));

		// get from time
		$from = $this->options->get('from', '*');

		if ($from !== '*' && (int) $from > $checkin)
		{
			// checkin before the from time
			return false;
		}

		// get end time
		$end = $this->options->get('end', '*');

		if ($end !== '*' && (int) $end < $checkin)
		{
			// checkin after the end time
			return false;
		}

		return true;
	}
}
