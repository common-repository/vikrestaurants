<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API\Exception;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\User;

/**
 * Invalid event exception.
 * 
 * @since 1.9
 */
class InvalidEventException extends \Exception
{
	/**
	 * @inheritDoc
	 * 
	 * @param  string  $event  The event ID.
	 */
	public function __construct(string $event, $message = '', $code = 0, \Throwable $previous = null)
	{
		// make sure everything is assigned properly
		parent::__construct(
			$message ?: sprintf('Invalid [%s] plugin! The requested event cannot be executed.', $event),
			$code ?: 203,
			$previous
		);
	}
}
