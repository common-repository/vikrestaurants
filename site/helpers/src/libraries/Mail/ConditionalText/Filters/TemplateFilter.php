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
 * Applies the conditional text only to the selected e-mail templates.
 *
 * @since 1.9
 */
class TemplateFilter extends ConditionalTextFilterAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * A list of allowed e-mail templates.
			 * 
			 * @var string[]
			 */
			'templates' => [
				'type'  => 'groupedlist',
				'label' => \JText::translate('VRE_TEMPLATES_FIELDSET'),
				'value' => $this->options->get('templates', []),
				'multiple' => true,
				'options' => [
					\JText::translate('VRMENUTITLEHEADER1') => [
						'restaurant.customer'     => \JText::translate('VRCONFIGSMSAPITO0'),
						'restaurant.admin'        => \JText::translate('VRCONFIGSMSAPITO1'),
						'restaurant.cancellation' => \JText::translate('VRE_CANCELLATION_FIELDSET'),
					],
					\JText::translate('VRMENUTITLEHEADER5') => [
						'takeaway.customer'     => \JText::translate('VRCONFIGSMSAPITO0'),
						'takeaway.admin'        => \JText::translate('VRCONFIGSMSAPITO1'),
						'takeaway.cancellation' => \JText::translate('VRE_CANCELLATION_FIELDSET'),
						'takeaway.review'       => \JText::translate('VRMANAGECONFIG67'),
						'takeaway.stock'        => \JText::translate('VRMANAGECONFIGTK17'),
					],
				],
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-layer-group';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		$form = $this->getForm();

		// extract templates from filter form
		$templatesLookup = [];

		// flatten grouped list
		foreach ($form['templates']['options'] as $group => $groupTemplates)
		{
			$templatesLookup = array_merge($templatesLookup, $groupTemplates);
		}

		$items = [];

		// iterate the selected templates
		foreach ($this->options->get('templates', []) as $template)
		{
			if (!isset($templatesLookup[$template]))
			{
				// template no longer available
				continue;
			}

			// register name only
			$items[] = $templatesLookup[$template];
		}

		/** @see CountableItemsSummaryTrait */
		return $this->createSummary($items);
	}

	/**
	 * @inheritDoc
	 */
	public function isEligible(string $templateId, array $data)
	{
		// fetch the selected templates
		$templates = $this->options->get('templates', []);

		// check whether all the templates are allowed or whether the current
		// template is included within the list of the supported ones
		return !$templates || in_array($templateId, $templates);
	}
}
