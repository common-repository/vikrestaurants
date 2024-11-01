<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\Joomla\Form;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Form\FormField as AbstractFormField;

/**
 * Joomla form field wrapper.
 * 
 * @since 1.9
 */
class FormField extends AbstractFormField
{
	/**
	 * @inheritDoc
	 */
	protected function getInput()
	{
		// prepare display data
		$data = $this->options->getProperties();

		// try to use an apposite layout for this platform
		$input = \JLayoutHelper::render('form.fields.joomla.' . $data['type'], $data);

		if (!$input)
		{
			// layout not found, fallback to the default one
			$input = parent::getInput();
		}

		return $input;
	}

	/**
	 * @inheritDoc
	 */
	protected function renderControl(string $input)
	{
		// prepare display data
		$data = $this->options->getProperties();

		// inject input HTML within the control display data
		$data['input'] = $input;

		// try to use an apposite layout for this platform
		$control = \JLayoutHelper::render('form.control.joomla', $data);

		if (!$control)
		{
			// layout not found, fallback to the default one
			$control = \JLayoutHelper::render('form.control.open', $data)
				. $input
				. \JLayoutHelper::render('form.control.close', $data);
		}

		return $control;
	}
}
