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
 * The rendered field can be locked/unlocked.
 * 
 * @since 1.9
 */
class LockableFieldRenderer extends FormFieldRendererLayout
{
	/** @var string */
	protected $layoutId = 'form.wrapper.lockable';

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData($data, $input)
	{
		return [
			'input' => $input,
			'id'    => $data->get('id'),
			'value' => $data->get('value'),
		];
	}
}
