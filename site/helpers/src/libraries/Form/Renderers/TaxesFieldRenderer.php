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
use E4J\VikRestaurants\Platform\Form\FormFactoryInterface;

/**
 * Implements a button to manage the taxes without leaving the page.
 * 
 * @since 1.9
 */
class TaxesFieldRenderer extends FormFieldRendererLayout
{
	/** @var string */
	protected $layoutId = 'form.wrapper.taxes';

	/** @var FormFactoryInterface */
	protected $formFactory;

	/**
	 * Class constructor.
	 */
	public function __construct(FormFactoryInterface $formFactory = null)
	{
		if ($this->formFactory)
		{
			$this->formFactory = $formFactory;
		}
		else
		{
			// use default form factory
			$this->formFactory = \VREFactory::getPlatform()->getFormFactory();
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData($data, $input)
	{
		$options = $data->get('options');

		if (!$data->get('options'))
		{
			// load supported taxes
			$options = \JHtml::fetch('vrehtml.admin.taxes');
		}

		if ($data->get('placeholder') && (!$options || $options[0]->value))
		{
			// add missing "placeholder" option
			array_unshift($options, \JHtml::fetch('select.option', '', ''));
		}

		$data->set('options', $options);

		return [
			'data'        => $data,
			'formFactory' => $this->formFactory,
		];
	}
}
