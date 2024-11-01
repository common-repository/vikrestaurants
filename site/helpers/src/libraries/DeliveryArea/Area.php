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

use E4J\VikRestaurants\Collection\Item;

/**
 * VikRestaurants delivery area holder.
 *
 * @since 1.9
 */
abstract class Area extends Item
{
	/**
	 * Creates a new instance for the specified delivery area.
	 *
	 * @param   mixed  $area  Either an array or an object holding the details
	 *                        of the delivery area.
	 *
	 * @return  self   A new delivery area instance.
	 * 
	 * @throws  \Exception
	 */
	final public static function getInstance($area)
	{
		if (is_string($area))
		{
			$area = ['type' => $area];
		}
		else
		{
			$area = (array) $area;
		}

		if (empty($area['type']))
		{
			// the type is mandatory in order to fetch the correct instance
			throw new \Exception('Missing delivery area type', 400);
		}

		/**
		 * Trigger hook to allow external plugins to include new types of delivery
		 * areas that might have been implemented out of this project. Plugins must
		 * include here the file holding the class of the area type.
		 *
		 * @param   string  $type  The requested delivery area type.
		 *
		 * @return  string  The classname of the object.
		 *
		 * @since   1.9
		 */
		$result = \VREFactory::getPlatform()->getDispatcher()->filter('onLoadDeliveryArea', [$area['type']]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		// take the first available one		
		$classname = $result->first();

		if (!$classname)
		{
			// set up class name starting from rule identifier
			$classname = 'E4J\\VikRestaurants\\DeliveryArea\\Types\\' . ucfirst($area['type']) . 'Area';
		}

		if (!class_exists($classname))
		{
			// unable to find a class for the specified type
			throw new \Exception(sprintf('Delivery area [%s] class not found', $classname), 404);
		}

		// create instance
		$handler = new $classname($area);

		if (!$handler instanceof Area)
		{
			// the class handler must inherit this class
			throw new \UnexpectedValueException(sprintf('Delivery area [%s] is not a valid instance', $classname), 404);
		}

		return $handler;
	}

	/**
	 * Returns the name of the area type.
	 *
	 * @return  string
	 */
	abstract public function getType();

	/**
	 * Checks whether the provided delivery query can be covered by this area.
	 * 
	 * @param   DeliveryQuery  $query
	 * 
	 * @return  bool  True if supported, false otherwise.
	 */
	abstract public function canAccept(DeliveryQuery $query);

	/**
	 * Fires when the model is going to save the delivery area.
	 * Children classes can overwrite this method to manipulate the
	 * contents and the attributes.
	 * 
	 * @param   array  &$src   The data to bind.
	 * @param   mixed  $model  The form model.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function onSave(array &$src, $model)
	{
		// data to bind is ok by default
		return true;
	}
}
