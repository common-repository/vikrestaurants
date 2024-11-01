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
use E4J\VikRestaurants\Helpers\DateHelper;

/**
 * Applies the conditional text only to those restaurant reservations and/or
 * take-away orders with a checkin/creation between the specified dates.
 *
 * @since 1.9
 */
class DateFilter extends ConditionalTextFilterAware
{
	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * Whether the restriction should be applied to the creation date, to the check-in date
			 * or to the current date.
			 * 
			 * @var string
			 */
			'mode' => [
				'type' => 'select',
				'label' => \JText::translate('VRRESERVATIONDATEFILTER'),
				'value' => $this->options->get('mode', 'checkin'),
				'options' => [
					'checkin' => \JText::translate('VRINVOICEDATEOPT3'),
					'booking' => \JText::translate('VRINVOICEDATEOPT2'),
					'today'   => \JText::translate('VRTODAY'),
				],
			],

			/**
			 * The start publishing date and time.
			 * 
			 * @var string
			 */
			'start' => [
				'type' => 'date',
				'label' => \JText::translate('VRMANAGETKMENU23'),
				'value' => DateHelper::sql2date($this->options->get('start', '')),
				'attributes' => [
					'showTime' => true,
				]
			],

			/**
			 * The end publishing date and time.
			 * 
			 * @var string
			 */
			'end' => [
				'type' => 'date',
				'label' => \JText::translate('VRMANAGETKMENU24'),
				'value' => DateHelper::sql2date($this->options->get('end', '')),
				'attributes' => [
					'showTime' => true,
				]
			],

			/**
			 * Whether the restriction should apply to the day of the week too.
			 * 
			 * @var string[]
			 */
			'weekdays' => [
				'type' => 'select',
				'label' => \JText::translate('VRSTATISTICSTH2'),
				'value' => $this->options->get('weekdays', []),
				'multiple' => true,
				'options' => \JHtml::fetch('vikrestaurants.days'),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-calendar-alt';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		$dates = [];

		// get start publishing date time
		$startPublishing = $this->options->get('start');

		if (!DateHelper::isNull($startPublishing))
		{
			// register start publishing date
			$dates['start'] = \JHtml::fetch('date', DateHelper::date2sql($startPublishing), \VREFactory::getConfig()->get('dateformat'));
		}

		// get end publishing date time
		$endPublishing = $this->options->get('end');

		if (!DateHelper::isNull($endPublishing))
		{
			// register end publishing date
			$dates['end'] = \JHtml::fetch('date', DateHelper::date2sql($endPublishing), \VREFactory::getConfig()->get('dateformat'));
		}

		$days = [];

		foreach ($this->options->get('weekdays', []) as $d)
		{
			$days[] = \JFactory::getDate()->dayToString($d, $abbr = true);
		}

		$text = '';

		if (!empty($dates['start']) && !empty($dates['end']))
		{
			if ($dates['start'] != $dates['end'])
			{
				// display both publishing delimiters
				$text = $dates['start'] . ' - ' . $dates['end'];
			}
			else
			{
				// same publishing date
				$text = $dates['start'];
			}
		}
		else if (!empty($dates['start']) && empty($dates['end']))
		{
			// display only the start publishing
			$text = \JText::sprintf('VRE_PUBL_START_ON', $dates['start']);
		}
		else if (empty($dates['start']) && !empty($dates['end']))
		{
			// display only the end publishing
			$text = \JText::sprintf('VRE_PUBL_END_ON', $dates['end']);
		}

		if ($days)
		{
			// display days on a new line, only if we have at least a publishing date
			$text .= ($text ? '<br />' : '') . implode(', ', $days);
		}

		return $text;
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

		switch ($this->options->get('mode', 'checkin'))
		{
			case 'checkin':
				$date = $order->checkin_ts;
				break;

			case 'booking':
				$date = $order->created_on;
				break;

			default:
				$date = \VikRestaurants::now();
		}

		// get start publishing date time
		$startPublishing = $this->options->get('start');

		if (!DateHelper::isNull($startPublishing) && DateHelper::getTimestamp($startPublishing) > $date)
		{
			// date before the start publishing
			return false;
		}

		// get end publishing date time
		$endPublishing = $this->options->get('end');

		if (!DateHelper::isNull($endPublishing) && DateHelper::getTimestamp($endPublishing) < $date)
		{
			// date after the end publishing
			return false;
		}

		// get week days
		$weekdays = $this->options->get('weekdays', []);

		if ($weekdays && !in_array((int) date('w', $date), $weekdays))
		{
			// week day not supported
			return false;
		}

		return true;
	}
}
