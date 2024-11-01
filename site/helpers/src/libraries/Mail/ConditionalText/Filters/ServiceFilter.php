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
 * Applies the conditional text only to those take-away orders that have
 * been assigned to the selected service.
 *
 * @since 1.9
 */
class ServiceFilter extends ConditionalTextFilterAware
{
	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * The services list.
			 * 
			 * @var string
			 */
			'service' => [
				'type'  => 'select',
				'label' => \JText::translate('VRMANAGETKRES13'),
				'value' => $this->options->get('service'),
				'options' => \E4J\VikRestaurants\CustomFields\Factory::getSupportedServices(),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-shipping-fast';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		// convert the selected service into a human-readable label
		return \JHtml::fetch('vikrestaurants.tkservice', $this->options->get('service', ''));
	}

	/**
	 * @inheritDoc
	 */
	public function isEligible(string $templateId, array $data)
	{
		$order = $data[0] ?? null;

		if (!$order instanceof \VREOrderTakeaway)
		{
			// the provided e-mail template is not observable
			return false;
		}

		// fetch selected service
		$service = $this->options->get('service');

		// allow in case the service of the order is supported
		return $service === $order->service;
	}
}
