<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Payment\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;

/**
 * Filters the payment gateways to obtain only the ones available for trusted customers.
 * 
 * @since 1.9
 */
class TrustedCustomerFilter implements CollectionFilter
{
	/** @var int */
	protected $ordersCount = 0;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $group  The current group ("restaurant" or "takeaway").
	 * @param  mixed   $user   The user.
	 * @param  mixed   $db     The database driver.
	 */
	public function __construct(string $group, $user = null, $db = null)
	{
		if (is_null($user))
		{
			// get current user
			$userId = \JFactory::getUser()->id;
		}
		else if (is_object($user))
		{
			// extract ID from object
			$userId = $user->id;
		}
		else
		{
			$userId = (int) $user;
		}

		/**
		 * The payment can be available only for trusted customer.
		 * In this case, we have to count the total number of orders
		 * made by the specified user, which must be equals or greater
		 * than the "trust" factor of the payment.
		 *
		 * @since 1.8
		 */
		if ($userId > 0)
		{
			if (!$db)
			{
				$db = \JFactory::getDbo();
			}

			$query = $db->getQuery(true);
			$query->select('COUNT(1)');
			$query->where(1);

			if ($group == 'restaurant')
			{
				$query->from($db->qn('#__vikrestaurants_reservation', 'r'));

				// exclude closures and clusters
				$query->where($db->qn('r.closure') . ' = 0');
				$query->where($db->qn('r.id_parent') . ' <= 0');
				
				// get any approved codes
				$approved = \JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]);
			}
			else
			{
				$query->from($db->qn('#__vikrestaurants_takeaway_reservation', 'r'));

				// get any approved codes
				$approved = \JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]);
			}

			// join with the customers to access the CMS user ID
			$query->leftjoin($db->qn('#__vikrestaurants_users', 'c') . ' ON ' . $db->qn('c.id') . ' = ' . $db->qn('r.id_user'));

			if ($approved)
			{
				// filter by approved status
				$query->where($db->qn('r.status') . ' IN (' . implode(',', array_map(array($db, 'q'), $approved)) . ')');
			}

			$query->andWhere([
				// must be the author of the reservation
				$db->qn('r.created_by') . ' = ' . $userId,
				// or the assigned user
				$db->qn('c.jid') . ' = ' . $userId,
			], 'OR');

			$db->setQuery($query);
			$this->ordersCount = (int) $db->loadResult();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function match(Item $item)
	{
		// get the provided threshold
		return (int) $item->get('trust', 0) <= $this->ordersCount;
	}
}
