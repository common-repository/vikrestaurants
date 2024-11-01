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
 * Invalid user credentials exception.
 * 
 * @since 1.9
 */
class InvalidUserCredentialsException extends \Exception
{
	/**
	 * @inheritDoc
	 * 
	 * @param  User  $user  The user login data.
	 */
	public function __construct(User $user, $message = '', $code = 0, \Throwable $previous = null)
	{
		if (strlen((string) $user->getUsername()) === 0)
		{
			// username empty
			$message = $message ?: 'Authentication Error! The username is empty or invalid.';
			$code    = $code    ?: 101;
		}
		else if (strlen((string) $user->getPassword()) === 0)
		{
			// password empty
			$message = $message ?: 'Authentication Error! The password is empty or invalid.';
			$code    = $code    ?: 102;
		}

		// make sure everything is assigned properly
		parent::__construct($message, $code, $previous);
	}
}
