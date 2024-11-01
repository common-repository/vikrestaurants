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
 * Wraps the rendered setting within a translatable box.
 * 
 * @since 1.9
 */
class ConfigTranslatableFieldRenderer extends FormFieldRendererLayout
{
	/** @var string */
	protected $layoutId = 'form.wrapper.configuration.translatable';

	/**
	 * A list containing all the available languages (tags).
	 * 
	 * @var string[]
	 */
	protected $languages;

	/**
	 * Whether the multi-lingual has been enabled.
	 * 
	 * @var bool
	 */
	protected $enabled;

	/**
	 * Where the translation management button should be displayed.
	 * At the moment it can be placed only on the right, on the left or bottom.
	 * 
	 * @var string
	 */
	protected $position;

	/**
	 * Class constructor.
	 */
	public function __construct(array $languages, bool $enabled, string $position = 'right')
	{
		$this->languages = $languages;
		$this->enabled   = $enabled;
		$this->position  = $position;
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData($data, $input)
	{
		return [
			'setting'   => $data->get('name'),
			'id'        => $data->get('id'),
			'input'     => $input,
			'languages' => $this->languages,
			'enabled'   => $this->enabled,
			'position'  => $this->position,
			'return'    => \JFactory::getApplication()->input->get('view'),
			'blank'     => false,
		];
	}
}
