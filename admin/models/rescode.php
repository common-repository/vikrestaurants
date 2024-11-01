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
 * VikRestaurants reservation code model.
 *
 * @since 1.9
 */
class VikRestaurantsModelRescode extends JModelVRE
{
	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function delete($ids)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		// invoke parent first
		if (!parent::delete($ids))
		{
			// nothing to delete
			return false;
		}

		$db = JFactory::getDbo();

		// load any related order status
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_order_status'))
			->where($db->qn('id_rescode') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($statuses = $db->loadColumn())
		{
			// delete all the order statuses that belong to the removed codes
			JModelVRE::getInstance('rescodeorder')->delete($statuses);
		}

		// unset deleted codes from restaurant reservations
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('rescode') . ' = 0')
			->where($db->qn('rescode') . ' IN (' . implode(',', $ids) . ')');

		$db->setQuery($q);
		$db->execute();

		// do the same for take-away orders
		$q->clear('update')->update($db->qn('#__vikrestaurants_takeaway_reservation'));

		$db->setQuery($q);
		$db->execute();

		return true;
	}
}
