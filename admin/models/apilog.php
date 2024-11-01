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
 * VikRestaurants API log model.
 *
 * @since 1.9
 */
class VikRestaurantsModelApilog extends JModelVRE
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

		if (!$ids)
		{
			// nothing to delete
			return false;
		}

		$db = JFactory::getDbo();

		// find number of existing logs
		$q = $db->getQuery(true)
			->select('COUNT(1)')
			->from($db->qn('#__vikrestaurants_api_login_logs'));

		$db->setQuery($q, 0, 1);

		if ((int) $db->loadResult() == count($ids))
		{
			// truncate all in case the user selected all the existing logs
			return $this->truncate();
		}

		// otherwise invoke parent to delete logs
		return parent::delete($ids);
	}

	/**
	 * Method to truncate the table.
	 *
	 * @param   integer  $id_login  An optional ID to delete all the logs assigned to the
	 *                              specified account.
	 *
	 * @return  boolean  True on success.
	 */
	public function truncate($id_login = null)
	{
		$db = JFactory::getDbo();

		if ($id_login)
		{
			// delete only the logs assigned to the specified account
			$q = $db->getQuery(true)
				->delete($db->qn('#__vikrestaurants_api_login_logs'))
				->where($db->qn('id_login') . ' = ' . (int) $id_login);
		}
		else
		{
			// truncate API logs
			$q = "TRUNCATE TABLE " . $db->qn('#__vikrestaurants_api_login_logs');
		}

		$db->setQuery($q);
		return $db->execute();
	}

	/**
	 * Flushes older API logs.
	 *
	 * @return  void
	 */
	public function flush()
	{
		$factor = VREFactory::getConfig()->getUint('apilogflush');

		if ($factor > 0)
		{
			$db = JFactory::getDbo();

			// get current date time minus the specified factor
			$now = strtotime('-' . $factor . ' days', VikRestaurants::now());

			$q = $db->getQuery(true)
				->delete($db->qn('#__vikrestaurants_api_login_logs'))
				->where($db->qn('createdon') . ' < ' . $now);
			
			$db->setQuery($q);
			$db->execute();
		}
	}
}
