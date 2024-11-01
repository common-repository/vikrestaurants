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

use E4J\VikRestaurants\Form\FormFieldRendererLayout;

/**
 * Wraps the rendered field within an input group with buttons.
 * 
 * @since 1.9
 */
class InputGroupFieldRenderer extends FormFieldRendererLayout
{
	/** @var string */
	protected $layoutId = 'form.wrapper.inputgroup';

	/**
	 * The buttons to be displayed before the input.
	 * 
	 * @var string[]
	 */
	protected $before = [];

	/**
	 * The buttons to be displayed after the input.
	 * 
	 * @var string[]
	 */
	protected $after = [];

	/**
	 * Class constructor.
	 */
	public function __construct($options)
	{
		if (is_string($options))
		{
			// text provided, display after the input by default
			$options = ['after' => $options];
		}

		if (isset($options['before']))
		{
			$this->before = (array) $options['before'];
		}

		if (isset($options['after']))
		{
			$this->after = (array) $options['after'];
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData($data, $input)
	{
		return [
			'input'      => $input,
			'textBefore' => $this->before,
			'textAfter'  => $this->after,
		];
	}
}
