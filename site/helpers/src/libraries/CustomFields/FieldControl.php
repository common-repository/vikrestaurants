<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants custom field control interface.
 *
 * @since 1.9
 */
interface FieldControl
{
	/**
	 * Returns the HTML of the field.
	 *
	 * @param   array   $data   An array of display data.
	 * @param   string  $input  The HTML of the input to wrap.
	 *
	 * @return  string  The HTML of the input.
	 */
	public function render(array $data, string $input = '');
}
