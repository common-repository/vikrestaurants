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
use E4J\VikRestaurants\Collection\Filters\Helpers\NumberComparator;

/**
 * Applies the conditional text according to the number of confirmed reservations/orders.
 *
 * @since 1.9
 */
class OrdersCountFilter extends ConditionalTextFilterAware
{
	use NumberComparator;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * Whether the number of reservations/orders should be lower, equals or
			 * higher then the specified threshold.
			 * 
			 * @var string
			 */
			'comparator' => [
				'type' => 'select',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR'),
				'value' => $this->options->get('comparator', '='),
				'options' => [
					'='  => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_ET'),
					'!'  => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_DT'),
					'<'  => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_LT'),
					'<=' => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_LTE'),
					'>'  => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_GT'),
					'>=' => \JText::translate('VRE_CONDITIONAL_TEXT_FILTER_ORDERSCOUNT_COMPARATOR_OPT_GTE'),
				],
			],

			/**
			 * The reservations/orders threshold.
			 * 
			 * @var int
			 */
			'threshold' => [
				'type' => 'number',
				'value' => $this->options->get('threshold', 0),
				'hiddenLabel' => true,
				'step' => 1,
				'min' => 0,
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-shopping-bag';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		// obtain configuration form
		$form = $this->getForm();

		// get configuration values
		$comparator = $this->options->get('comparator', '=');
		$threshold  = $this->options->get('threshold', 0);

		// fetch comparator label from configuration array
		$label = $form['comparator']['options'][$comparator] ?? '';

		// append threshold to comparator label
		return trim($label . ' ' . $threshold);
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

		if ($order->id_user <= 0)
		{
			// only logged-in users are eligible
			return false;
		}

		$db = \JFactory::getDbo();

		// count the number of reservations/orders made by this user
		$query = $db->getQuery(true)
			->select('COUNT(1)')
			->where($db->qn('id_user') . ' = ' . (int) $order->id_user);

		if ($order instanceof \VREOrderRestaurant)
		{
			// count restaurant reservations
			$query->from($db->qn('#__vikrestaurants_reservation'));

			// exclude cluster children
			$query->where($db->qn('id_parent') . ' = 0');

			// get any approved codes
			$approved = \JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]);
		}
		else
		{
			// count takeaway orders
			$query->from($db->qn('#__vikrestaurants_takeaway_reservation'));

			// get any approved codes
			$approved = \JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]);
		}

		if ($approved)
		{
			// filter by approved status
			$query->where($db->qn('status') . ' IN (' . implode(',', array_map(array($db, 'q'), $approved)) . ')');
		}

		$db->setQuery($query);
		$ordersCount = (int) $db->loadResult();

		// fetch comparator operator
		$comparator = $this->options->get('comparator', '=');

		// fetch threshold
		$threshold = (int) $this->options->get('threshold', 0);

		// compare the reservations/orders count against the specified threshold
		return $this->compare($ordersCount, $threshold, $comparator);
	}
}
