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
 * VikRestaurants take-away topping separator model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTktopseparator extends JModelVRE
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

		$toppingModel = JModelVRE::getInstance('tktopping');

		$db = JFactory::getDbo();

		// load any assigned topping
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_topping'))
			->where($db->qn('id_separator') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		foreach ($db->loadAssocList() as $topping)
		{
			// unset relation
			$topping['id_separator'] = 0;
			$toppingModel->save($topping);
		}

		return true;
	}
}
