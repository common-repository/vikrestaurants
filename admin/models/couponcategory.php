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
 * VikRestaurants coupon category model.
 *
 * @since 1.9
 */
class VikRestaurantsModelCouponcategory extends JModelVRE
{
	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return 	boolean  True on success, false otherwise.
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

		// load any assigned coupon
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_coupons'))
			->where($db->qn('id_category') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($query);
		
		foreach ($db->loadColumn() as $couponId)
		{
			// detach category coupon
			JModelVRE::getInstance('coupon')->save([
				'id'          => (int) $couponId,
				'id_category' => 0,
			]);
		}

		return true;
	}
}
