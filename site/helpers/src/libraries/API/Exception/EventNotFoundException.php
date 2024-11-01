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
 * Event not found exception.
 * 
 * @since 1.9
 */
class EventNotFoundException extends \Exception
{
	/**
	 * @inheritDoc
	 * 
	 * @param  string  $event  The event ID.
	 */
	public function __construct(string $event, $message = '', $code = 0, \Throwable $previous = null)
	{
		if ($event)
		{
			// event provided (404 not found)
			$message = $message ?: sprintf('Plugin [%s] Not Found! The requested event does not exist.', $event);
			$code    = $code    ?: 202;
		}
		else
		{
			// event missing (400 bad request)
			$message = $message ?: 'Missing event.';
			$code    = $code    ?: 200;
		}

		// make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
