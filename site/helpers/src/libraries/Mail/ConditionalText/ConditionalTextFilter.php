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
 * Interface used to implement a filter for the eligibility 
 * of a conditional text according to the provided template ID
 * and related data.
 *
 * @since 1.9
 */
interface ConditionalTextFilter
{
	/**
	 * Checks whether the current filter allows the usage of the 
	 * conditional text to which it belongs.
	 * 
	 * @param   string  $templateId  The template used for the mail.
	 * @param   array   $data        The data wrapped by the mail template.
	 * 
	 * @return  bool    True if eligible, false otherwise.
	 */
	public function isEligible(string $templateId, array $data);
}
