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
 * VikRestaurants custom field service interface.
 *
 * @since 1.9
 */
interface FieldService
{
	/**
	 * Returns a readable name of the service.
	 * 
	 * @return  string
	 */
	public function getName();

	/**
	 * Performs some tasks before extracting and validating the typed
	 * value for the given field.
	 * 
	 * @param   Field  $field  The field to observe.
	 * 
	 * @return  void
	 * 
	 * @throws  Exception  Can throw an exception to abort.
	 */
	public function preflight(Field $field);

	/**
	 * Performs some tasks once the system already retrived the typed
	 * value for the given field.
	 * 
	 * @param   Field  $field   The field to observe.
	 * @param   mixed  &$value  The retrieved value.
	 * 
	 * @return  void
	 * 
	 * @throws  Exception  Can throw an exception to abort.
	 */
	public function postflight(Field $field, &$value);
}
