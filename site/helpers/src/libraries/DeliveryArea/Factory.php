<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants delivery area factory.
 *
 * @since 1.9
 */
final class Factory
{
	/**
	 * Returns a list of supported delivery area types.
	 *
	 * @return  array
	 * 
	 * @throws  \Exception
	 */
	public static function getSupportedTypes()
	{
		$types = [];

		// load all files inside types folder
		$files = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR . '*.php');

		foreach ($files as $file)
		{
			// extract type from file name
			$type = strtolower(preg_replace("/Area\.php$/i", '', basename($file)));

			try
			{
				// try to instantiate the type
				$area = Area::getInstance($type);

				// attach type to list
				$types[$type] = $area->getType();
			}
			catch (\Exception $e)
			{
				// catch error and go ahead
			}
		}

		/**
		 * Trigger hook to allow external plugins to support custom types.
		 * New types have to be appended to the given associative array.
		 * The key of the array is the unique ID of the type, the value is
		 * a readable name of the type.
		 *
		 * @param   array  &$types  An array of types.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadDeliveryAreaTypes', [&$types]);

		// sort types by ascending name and preserve keys
		asort($types);

		return $types;
	}
}
