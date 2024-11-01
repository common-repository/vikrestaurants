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
 * User authentication exception.
 * 
 * @since 1.9
 */
class UserAuthenticationException extends \Exception
{
	/**
	 * @inheritDoc
	 * 
	 * @param  User  $user  The user login data.
	 */
	public function __construct(User $user = null, $message = '', $code = 0, \Throwable $previous = null)
	{
		// make sure everything is assigned properly
		parent::__construct(
			$message ?: 'Authentication Error!',
			$code ?: 100,
			$previous
		);
	}
}
