<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The import/export drivers can inherit this interface in case the process
 * should require custom arguments.
 * 
 * @since 1.9
 */
interface ConfigurableDriver
{
	/**
	 * Returns a readable name for the driver.
	 * 
	 * @return  string
	 */
	public function getName();

	/**
	 * Returns an optional description for the selected driver.
	 * 
	 * @return  string
	 */
	public function getDescription();

	/**
	 * Returns an associative array containing the configuration form of the driver.
	 * 
	 * @return  array
	 */
	public function getForm();
}
