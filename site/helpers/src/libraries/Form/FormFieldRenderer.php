<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Form;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Form field renderer interface, which can be passed to the render method
 * of a FormField instance.
 * 
 * @since 1.9
 */
interface FormFieldRenderer
{
	/**
	 * Renders the form field.
	 * 
	 * The output can be either returned or echoed.
	 * 
	 * @param   \JObject  $data   The field display data registry.
	 * @param   string    $input  The default rendered input, if any.
	 * 
	 * @return  string|void
	 */
	public function render($data, $input);
}
