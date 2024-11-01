<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The APIs error representation.
 *
 * @since  1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\Error instead.
 */
class ErrorAPIs extends E4J\VikRestaurants\API\Error
{
	/**
	 * Raise the specified error and stop the flow if needed.
	 *
	 * @param 	integer 	$errcode 	The code identifier.
	 * @param 	string 		$error 		The text description.
	 * @param 	boolean 	$exit 		True to stop the execution, otherwise false.
	 *
	 * @return 	mixed 		The error raised when exit is not needed, otherwise the error will be echoed in JSON.
	 * 
	 * @deprecated 1.11  Without replacement.
	 */
	public static function raise($errcode, $error, $exit = true)
	{
		$err = new static($errcode, $error);

		if ($exit)
		{
			echo $err->toJSON();
			exit;
		}

		return $err;
	}
}
