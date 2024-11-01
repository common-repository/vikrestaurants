<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Export;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Backup export type interface.
 * 
 * @since 1.9
 */
interface Type
{
	/**
	 * Returns a readable name of the export type.
	 * 
	 * @return  string
	 */
	public function getName();

	/**
	 * Returns a readable description of the export type.
	 * 
	 * @return  string
	 */
	public function getDescription();

	/**
	 * Configures the backup director.
	 * 
	 * @param   Director  $director
	 * 
	 * @return  void
	 */
	public function build(Director $director);
}
