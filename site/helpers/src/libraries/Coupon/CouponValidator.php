<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Coupon;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Helpers\DateHelper;

/**
 * Helper class used to validate the coupon codes.
 * 
 * @since 1.9
 */
class CouponValidator
{
	/** @var array */
	protected $options;

	/** @var JDatabaseDriver */
	protected $db;

	/**
	 * Class constructor.
	 * 
	 * @param  array  $options
	 * @param  mixed  $db
	 */
	public function __construct(array $options = [], $db = null)
	{
		$this->options = $options;

		if (isset($options['group']))
		{
			if (is_string($options['group']))
			{
				$this->options['group'] = $options['group'] === 'restaurant' ? 0 : 1;
			}
			else
			{
				$this->options['group'] = (int) $options['group'];
			}
		}
		else
		{
			$this->options['group'] = null;
		}

		if (isset($options['date']))
		{
			if (!is_numeric($options['date']))
			{
				// convert date to UNIX timestamp
				if (isset($options['hourmin']))
				{
					// fetch hours and minutes
					list($options['hour'], $options['min']) = explode(':', $options['hourmin']);
				}

				// create check-in date and time
				$options['date'] = DateHelper::getTimestamp($options['date'], $options['hour'] ?? 0, $options['min'] ?? 0);
			}

			// UNIX timestamp provided
			$this->options['checkin'] = \JFactory::getDate(date('Y-m-d H:i:s', $options['date']), \JFactory::getApplication()->get('offset', 'UTC'));
		}
		else
		{
			$this->options['checkin'] = null;
		}

		if (isset($options['total']))
		{
			$this->options['total'] = abs((float) $options['total']);
		}
		else
		{
			$this->options['total'] = null;
		}

		if (isset($options['people']) && $this->options['group'] === 0)
		{
			$this->options['people'] = abs((int) $options['people']);
		}
		else
		{
			$this->options['people'] = null;
		}

		if ($db)
		{
			$this->db = $db;
		}
		else
		{
			$this->db = \JFactory::getDbo();
		}
	}

	/**
	 * Checks whether the provided coupon code is compliant with the
	 * query arguments filled during the class construction.
	 * 
	 * @param   mixed   $coupon  Either a coupon code string, an object or an array.
	 * 
	 * @return  object  The details of the coupon code.
	 * 
	 * @throws  \Exception  In case the coupon is not compliant.
	 */
	public function validate($coupon)
	{
		if (is_string($coupon))
		{
			// fetch coupon by code
			$coupon = \JModelVRE::getInstance('coupon')->getItem(['code' => $coupon]);
		}
		else
		{
			$coupon = (object) $coupon;
		}

		if (!$coupon || empty($coupon->id))
		{
			// the provided coupon does not exist
			throw new \Exception('The specified coupon does not exist', 404);
		}

		if ($this->options['group'] !== null && $coupon->group != $this->options['group'])
		{
			// coupon valid, but for a different group
			throw new \Exception('The specified coupon is not applicable to the specified group', 405);
		}

		if (!DateHelper::isNull($this->options['checkin']))
		{
			// make sure the check-in is higher than the coupon start publishing
			if (!DateHelper::isNull($coupon->start_publishing) && $coupon->start_publishing > $this->options['checkin']->format('Y-m-d H:i:s'))
			{
				// check-in before the start publishing
				throw new \Exception('The coupon is not yet active.', 425);
			}

			// make sure the check-in is lower than the coupon end publishing
			if (!DateHelper::isNull($coupon->end_publishing) && $coupon->end_publishing < $this->options['checkin']->format('Y-m-d H:i:s'))
			{
				// check-in after the end publishing
				throw new \Exception('The coupon is expired.', 410);
			}
		}

		if ($this->options['total'] !== null && $this->options['total'] < $coupon->mincost)
		{
			// the total to pay is lower then the minimum threshold
			throw new \Exception('Not enough money in cart', 405);
		}

		if ($this->options['people'] !== null && $this->options['people'] < $coupon->minpeople)
		{
			// the number of participants is lower then the minimum threshold
			throw new \Exception('Not enough people', 405);
		}

		/**
		 * Make sure the number of usages didn't exceed the maximum threshold.
		 *
		 * @since 1.8
		 */
		if ($coupon->maxusages > 0 && $coupon->usages >= $coupon->maxusages)
		{
			// cannot redeem the coupon anymore
			throw new \Exception('Exceeded the maximum number of usages', 429);
		}

		/**
		 * Check if we should check whether the current user
		 * should be able to redeem the coupon one more time.
		 *
		 * @since 1.8
		 */
		if ($coupon->maxperuser > 0)
		{
			// get current user
			$user = \JFactory::getUser();

			if ($user->guest)
			{
				// the coupon can be redeemed only by logged-in users
				throw new \Exception('Only registered users can redeem this coupon', 401);
			}

			// fetch reservations table
			$table = $coupon->group == 0 ? '#__vikrestaurants_reservation' : '#__vikrestaurants_takeaway_reservation';

			$query = $this->db->getQuery(true)
				->select('COUNT(1)')
				->from($this->db->qn($table, 'r'))
				->leftjoin($this->db->qn('#__vikrestaurants_users', 'u') . ' ON ' . $this->db->qn('r.id_user') . ' = ' . $this->db->qn('u.id'))
				->where($this->db->qn('r.coupon_str') . ' LIKE ' . $this->db->q($coupon->code . ';;%'));

			if ($coupon->group == 0)
			{
				// exclude children reservations and closures
				$query->where($this->db->qn('r.closure') . ' = 0');
				$query->where($this->db->qn('r.id_parent') . ' <= 0');
			}

			$query->andWhere([
				$this->db->qn('r.created_by') . ' = ' . (int) $user->id,
				$this->db->qn('u.jid') . ' = ' . (int) $user->id,
			], 'OR');

			$this->db->setQuery($query);
			
			// compare the number of usages against the maximum limit
			if ((int) $this->db->loadResult() >= $coupon->maxperuser)
			{
				// the user already redeemed the coupon all the possible times
				throw new \Exception('Exceeded the maximum number of usages per person', 429);
			}
		}

		/**
		 * This event can be used to apply additional conditions to the 
		 * coupon validation. When this event is triggered, the
		 * system already validated the standard conditions and the
		 * coupon has been approved for the usage.
		 * 
		 * NOTE: it is possible to throw an exception to prevent the
		 * activation with a specific error message.
		 *
		 * @param   object 	$coupon  The coupon code to check.
		 * @param   array   $args    A configuration array.
		 *
		 * @return  bool    Return false to deny the coupon activation.
		 *
		 * @since   1.8
		 */
		$result = \VREFactory::getPlatform()->getDispatcher()->filter('onBeforeActivateCoupon', [$coupon, $this->options]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		if ($result->isFalse())
		{
			throw new \Exception('Aborted by a plugin', 409);
		}

		// return coupon object
		return $coupon;
	}
}
