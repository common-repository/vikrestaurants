<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Form\Renderers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Form\FormFieldRenderer;

/**
 * Generates a dropdown field to pick a check-in time.
 * 
 * @since 1.9
 */
class CheckinTimeFieldRenderer implements FormFieldRenderer
{
	/**
	 * @inheritDoc
	 */
	public function render($data, $input)
	{
		// prepare attributes
		$attributes = array_merge(
			$data->get('attributes', []),
			[
				'id'    => $data->get('id'),
				'class' => trim($data->get('class') . ' ' . ($data->get('required') ? 'required' : '')),
			]
		);

		// obtain select options
		$times = $data->get('options', []);

		if (!$times)
		{
			$group = $data->get('group');

			if (!is_numeric($group))
			{
				// convert from string to int
				$group = $group === 'takeaway' ? 2 : 1;
			}

			// calculate available times
			$times = \JHtml::fetch('vikrestaurants.times', $group, $data->get('day'));
		}

		// render dropdown
		return \JHtml::fetch(
			'vrehtml.site.timeselect',
			$data->get('name'),
			$data->get('value'),
			$times,
			$attributes
		);
	}
}
