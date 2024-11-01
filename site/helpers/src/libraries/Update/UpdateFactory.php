<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Factory used to handle the update adapters.
 *
 * ------------------------------------------------------------------------------------
 *
 * Update adapters CLASS name must have the following structure:
 * 
 * "E4J\VikRestaurants\Update\Adapters\UpdateAdapter" + VERSION (replace dots with underscores)
 * eg. E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_2_5 (com_vikrestaurants 1.2.5)
 *
 * ------------------------------------------------------------------------------------
 *
 * Update adapters FILE name must have the following structure:
 * 
 * UpdateAdapter + VERSION (replace dots with underscores) + ".php"
 * eg. UpdateAdapter1_2_5.php (com_vikrestaurants 1.2.5)
 *
 * @since 1.9
 */
class UpdateFactory
{
	/**
	 * Executes the requested method.
	 *
	 * @param   string  $method   The method name to launch.
	 * @param   string  $version  The version to consider.
	 * @param   mixed   $caller   The object that invoked this method.
	 * 
	 * @return  bool    True on success, false otherwise.
	 */
	public static function run(string $method, string $version, $caller = null)
	{
		// get all adapters
		$adapters = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Adapters' . DIRECTORY_SEPARATOR . '*.php');

		// iterate each supported version
		foreach ($adapters as $file)
		{
			// get filename
			$filename = preg_replace("/\.php$/i", '', basename($file));

			// get class name of update adapter for current loop version
			$classname = 'E4J\\VikRestaurants\\Update\\Adapters\\' . $filename;

			// get version from filename
			if (preg_match("/UpdateAdapter([0-9_]+)$/", $filename, $match))
			{
				$v = preg_replace("/_+/", '.', $match[1]);
			}
			else
			{
				// cannot detect version
				$v = '0';
			}

			// in case the software version is lower than loop version, launch the rule
			if (version_compare($version, $v, '<') && class_exists($classname))
			{
				try
				{
					// check whether the requested class exists
					$reflection = new \ReflectionClass($classname);

					// Get method details.
					// In case the method doesn't exist, an exception will be thrown.
					$methodData = $reflection->getMethod($method);

					if ($methodData->isStatic())
					{
						// use static class
						$object = $classname;
					}
					else
					{
						// instantiate object
						$object = new $classname;
					}

					// then run update callback function
					$success = call_user_func(array($object, $method), $caller);

					if ($success === false)
					{
						// stop adapters in case something gone wrong
						return false;
					}
				}
				catch (\Exception $e)
				{
					if (!$e instanceof \ReflectionException)
					{
						// prompt error message
						\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
					}

					// One of the following errors occurred:
					// - the class does not exist;
					// - the method does not exist;
					// - the launched method thrown an exception.
					$success = false;
				}
			}
		}

		// no error found
		return true;
	}
}
