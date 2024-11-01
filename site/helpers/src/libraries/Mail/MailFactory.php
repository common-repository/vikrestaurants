<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Factory class used to instantiate the right notification provider.
 *
 * @since 1.9
 */
abstract class MailFactory
{
	/**
	 * Returns an instance of the notification provider.
	 *
	 * @param 	string 	$group 	  The group in which search for the class.
	 * @param 	string 	$id 	  The id/alias of the provider to load.
	 * @param 	mixed 	$args...  Some additional arguments to use when
	 * 							  instantiating the provider.
	 *
	 * @return 	MailTemplate
	 */
	public static function getTemplate(string $group, string $id)
	{
		// adjust the group to be compliant within a namespace:
		// foo.bar.baz becomes Foo\\Bar\\Baz
		$group = implode('\\', array_map('ucfirst', preg_split("/\./", $group)));

		// fetch class name
		$classname = 'E4J\\VikRestaurants\\Mail\\Templates\\' . $group . ($group ? '\\' : '') . ucfirst($id) . 'MailTemplate';

		// make sure the class exists
		if (!class_exists($classname))
		{
			// class not found
			throw new \Exception(sprintf('Mail template [%s] class not found', $classname), 404);
		}

		// fetch arguments to pass to the constructor by excluding the first 2 arguments
		$args = func_get_args();
		$args = array_splice($args, 2);

		// create reflection of the provider
		$class = new \ReflectionClass($classname);
		// instantiate provider class
		$template = $class->newInstanceArgs($args);

		// make sure the class is a valid instance
		if (!$template instanceof MailTemplate)
		{
			// not a valid instance
			throw new \Exception(sprintf('Mail template [%s] class is not a valid instance', $classname), 406);
		}

		return $template;
	}
}
