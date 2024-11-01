<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextFilterAware;

/**
 * Uses the conditional texts only if we are in test mode.
 *
 * @since 1.9
 */
class DebugFilter extends ConditionalTextFilterAware
{
	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-flask';
	}

	/**
	 * @inheritDoc
	 */
	public function isEligible(string $templateId, array $data)
	{
		// the last element of the $data array should always be a configuration array
		$options = end($data);

		// make sure we have a valid configuration and the test mode is active
		return is_array($options) && !empty($options['test']);
	}
}
