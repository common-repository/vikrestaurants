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

use E4J\VikRestaurants\DeliveryArea\Area as DeliveryArea;

/**
 * VikRestaurants take-away delivery area model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkarea extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return  mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		$area = parent::getItem($pk, $new);

		if (!$area)
		{
			return null;
		}

		// decode stringified columns
		$area->content    = $area->content    ? json_decode($area->content)    : new stdClass;
		$area->attributes = $area->attributes ? json_decode($area->attributes) : new stdClass;

		return $area;
	}

	/**
	 * Basic save implementation.
	 *
	 * @param   mixed  $data  Either an array or an object of data to save.
	 *
	 * @return  mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		if (isset($data['type']))
		{
			try
			{
				// let the delivery area handler completes the binding
				$result = DeliveryArea::getInstance($data['type'])->onSave($data, $this);

				if ($result === false)
				{
					// the handler aborted the saving process
					return false;
				}
			}
			catch (Exception $e)
			{
				// an error has occurred
				$this->setError($e);
				return false;
			}
		}

		// attempt to save the record
		return parent::save($data);
	}
}
