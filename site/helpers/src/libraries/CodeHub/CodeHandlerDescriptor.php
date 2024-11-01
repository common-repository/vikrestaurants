<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CodeHub;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The purpose of this interface is to describe code handlers in an optional way.
 * 
 * @since 1.9
 */
interface CodeHandlerDescriptor
{
	/**
	 * Returns an FontAwesome icon to identify this code handler.
	 * 
	 * @return  string
	 */
	public function getIcon();

	/**
	 * Returns a human-readable name for this code handler.
	 * 
	 * @return  string
	 */
	public function getName();

	/**
	 * Returns some information about the usage of this handler.
	 * 
	 * @return  string
	 */
	public function getHelp();
}
