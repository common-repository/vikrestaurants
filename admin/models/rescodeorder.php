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
 * VikRestaurants order-reservation code relational model.
 *
 * @since 1.9
 */
class VikRestaurantsModelRescodeorder extends JModelVRE
{
	/**
	 * Basic save implementation.
	 *
	 * @param 	mixed  $data  Either an array or an object of data to save.
	 *
	 * @return 	mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		if (empty($data['id']))
		{
			$db = JFactory::getDbo();

			// check if we already have an order status with the specified code
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn('#__vikrestaurants_order_status'))
				->where($db->qn('id_order') . ' = ' . (int) ($data['id_order'] ?? 0))
				->where($db->qn('id_rescode') . ' = ' . (int) ($data['id_rescode'] ?? 0))
				->where($db->qn('group') . ' = ' . (int) ($data['group']) ?? 0);

			$db->setQuery($query, 0, 1);
			$pk = $db->loadResult();

			if ($pk)
			{
				// the reservation code already exists, update it
				// in order to avoid duplicate records
				$data['id'] = (int) $pk;
			}
		}

		// attempt to save the relation
		$id = parent::save($data);

		if (!$id)
		{
			// an error occurred, do not go ahead
			return false;
		}

		switch ($data['group'] ?? null)
		{
			case 1:
				$group = 'restaurant';
				break;

			case 2:
				$group = 'takeaway';
				break;

			case 3:
				$group = 'food';
				break;

			case 4:
				$group = 'tkfood';
				break;

			default:
				$group = null;
		}

		try
		{
			// trigger code change to dispatch the rule action, if any
			E4J\VikRestaurants\ReservationCodes\CodesHandler::trigger(
				(int)    ($data['id_rescode'] ?? 0),
				(int)    ($data['id_order'] ?? 0),
				(string) $group
			);
		}
		catch (Exception $e)
		{
			// suppress error silently...
		}
		
		return $id;
	}
}
