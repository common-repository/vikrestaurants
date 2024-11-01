<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Interface used to for manageable entities of the conditional texts.
 *
 * @since 1.9
 */
interface ConditionalTextManageable
{
	/**
	 * Returns the identifier of the conditional text action/filter.
	 * 
	 * @return  string
	 */
	public function getID();

	/**
	 * Returns a readable name for the conditional text action/filter.
	 * 
	 * @return  string
	 */
	public function getName();

	/**
	 * Returns an extended description for the conditional text action/filter.
	 * 
	 * @return  string
	 */
	public function getDescription();

	/**
	 * Returns an icon (FontAwesome) for the conditional text action/filter.
	 * 
	 * @return  string
	 */
	public function getIcon();

	/**
	 * Renders a summary text according to the parameters of the conditional text action/filter.
	 * 
	 * @return  string
	 */
	public function getSummary();

	/**
	 * Returns an associative array containing the configuration data of the conditional 
	 * text action/filter.
	 * 
	 * @return  array
	 */
	public function getData();

	/**
	 * Returns an associative array containing the configuration form of the conditional 
	 * text action/filter.
	 * 
	 * @return  string
	 */
	public function getForm();
}
