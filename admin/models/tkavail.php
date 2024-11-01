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
 * VikRestaurants take-away availability override model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkavail extends JModelVRE
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
		// always free some space with old records
		$this->flush('-14 days');

		// attempt to save the record
		return parent::save($data);
	}

	/**
	 * Deletes all the overrides registered for the dates lower than the specified threshold.
	 *
	 * @param   string|int  $threshold
	 *
	 * @return  bool
	 */
	public function flush($threshold)
	{
		if (!is_numeric($threshold))
		{
			// convert threshold into a timestamp
			$threshold = strtotime($threshold, VikRestaurants::now());
		}

		$db = JFactory::getDbo();

		// delete availability overrides older than 2 weeks
		$query = $db->getQuery(true)
			->delete($db->qn($this->getTable()->getTableName()))
			->where($db->qn('ts') . ' < ' . (int) $threshold);

		$db->setQuery($query);
		$db->execute();

		return (bool) $db->getAffectedRows();
	}
}
