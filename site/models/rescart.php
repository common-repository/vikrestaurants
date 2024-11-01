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
 * VikRestaurants restaurant reservation cart model.
 *
 * @since 1.9
 */
class VikRestaurantsModelRescart extends JModelVRE
{
	/**
	 * Validates the search arguments.
	 * 
	 * @param   array  $data  The search data.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function checkIntegrity(array $data = [])
	{
		// validate request
		$code = VikRestaurants::isRequestReservationValid($data);

		if ($code !== 0)
		{
			// fetch error message from code
			$this->setError(JText::translate(VikRestaurants::getResponseFromReservationRequest($code)));
			return false;
		}

		// extract hour and minutes
		list($data['hour'], $data['min']) = explode(':', $data['hourmin']);
		
		// get checkin timestamp
		$ts = VikRestaurants::createTimestamp($data['date'], $data['hour'], $data['min']);
		
		// make sure the reservations are allowed for the selected date time
		if (!VikRestaurants::isReservationsAllowedOn($ts))
		{
			// reservation blocked for today
			$this->setError(JText::translate('VRNOMORERESTODAY'));
			return false;
		}

		// init special days manager
		$sdManager = new VRESpecialDaysManager('restaurant');
		// set checkin date
		$sdManager->setStartDate($data['date']);
		// set checkin time
		$sdManager->setCheckinTime($data['hourmin']);

		// get first available special day
		$specialDay = $sdManager->getFirst();

		if ($specialDay)
		{
			// make sure we haven't reached the threshold of allowed people
			if ($specialDay->canHostPeople($data) == false)
			{
				// unable to host the requested party size
				$this->setError(JText::translate('VRNOMORERESTODAY'));
				return false;
			}

			// check if we should ignore closing days
			$ignore_cd = $specialDay->ignoreClosingDays;
		}
		else
		{
			// never ignore closing days
			$ignore_cd = false;
		}
		
		// check if we have a closing day for the selected checkin date
		if (!$ignore_cd && VikRestaurants::isClosingDay($data))
		{
			$this->setError(JText::translate('VRSEARCHDAYCLOSED'));
			return false;
		}

		// always revalidate the coupon code
		$this->revalidateCoupon($data);

		return true;
	}

	/**
	 * Returns the details of the booked menus, if any.
	 * 
	 * @return  object[]
	 */
	public function getMenus()
	{
		// fetch booked menus from user session
		$sessionMenus = JFactory::getSession()->get('reservation.menus', null, 'vikrestaurants');

		if (!$sessionMenus)
		{
			return [];
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->select((count($sessionMenus) > 1 ? '1' : '0') . ' AS ' . $db->qn('freechoose'))
			->from($db->qn('#__vikrestaurants_menus'))
			->where($db->qn('id') . ' IN (' . implode(',', array_map('intval', array_keys($sessionMenus))) . ')');

		$db->setQuery($query);
		$menus = $db->loadObjectList();

		foreach ($menus as $menu)
		{
			// register number of booked units
			$menu->units = $sessionMenus[$menu->id];
		}

		return $menus;
	}

	/**
	 * Validates the menus selected by the customer, if any.
	 * 
	 * @param   array  $data  The search data.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function validateMenus(array $data = [])
	{
		$session = JFactory::getSession();

		// validate selected menus
		$sessionMenus = $session->get('reservation.menus', null, 'vikrestaurants');

		if (!$data['menus'])
		{
			// use the menus saved in the user session
			$data['menus'] = $sessionMenus ?: [];
		}
		
		// make sure the menus haven't been yet validated
		if (VikRestaurants::isMenusChoosable($data))
		{
			// always validate the selected menus
			if (!VikRestaurants::validateSelectedMenus($data))
			{
				$this->setError(JText::translate('VRSEARCHMENUSNOTVALID'));
				return false;
			}

			// valid menus, register in session
			$session->set('reservation.menus', $data['menus'], 'vikrestaurants');
		}
		else
		{
			// menus selection not needed
			$session->set('reservation.menus', null, 'vikrestaurants');
		}

		return true;
	}

	/**
	 * Calculates the total amount to leave when trying
	 * book a table for the specified check-in.
	 *
	 * @param   array  $data  An associative array containing the check-in details.
	 *
	 * @return  float  The total amount to leave.
	 */
	public static function getTotalDeposit(array $data)
	{
		$config = VREFactory::getConfig();

		// instantiate special days manager
		$sdManager = new VRESpecialDaysManager('restaurant');

		// set date filter
		$sdManager->setStartDate($data['date']);
		// set time filter
		$sdManager->setCheckinTime($data['hourmin']);

		// get first special day available
		$sd = $sdManager->getFirst();

		if ($sd)
		{
			// calculate total deposit
			$total = $sd->getTotalDeposit($data['people']);
		}
		else
		{
			// fallback to global configuration

			// get default cost to leave
			$total = $config->getFloat('resdeposit');
			
			if ($config->getBool('costperperson'))
			{
				// multiply deposit per the number of guests
				$total *= $data['people'];
			}

			// get minimum number of people for deposit
			$ask = $config->getUint('askdeposit');

			if ($ask == 0 || $data['people'] < $ask)
			{
				// never apply deposit in case it is disabled or in
				// case the number of people is lower than the specified amount
				$total = 0;
			}
		}

		/**
		 * This event can be used to alter the total deposit at runtime.
		 * In example, it is possible to change the amount according
		 * to the selected table/room.
		 *
		 * Since multiple plugins might be attached to this event, the system
		 * will take the highest returned value.
		 *
		 * @param 	float  $total  The current total.
		 * @param 	array  $args   The searched arguments.
		 *
		 * @return 	float  The deposit that should be used.
		 *
		 * @since 	1.8
		 */
		$result = VREFactory::getPlatform()->getDispatcher()->filter('onCalculateTotalDeposit', [$total, $data]);

		/** @var E4J\VikRestaurants\Event\EventResponse */

		// check if at least a plugin returned something
		if ($result->has())
		{
			// overwrite the total deposit with the highest fetched value
			$total = max($result->numbers());
		}

		// make sure the total is not lower than 0
		return max(0, $total);
	}

	/**
	 * Returns the details of the redeemed coupon code.
	 * 
	 * @return  object|null
	 */
	public function getCoupon()
	{
		// clears the coupon from the session after retrieving it
		return JFactory::getSession()->set('reservation.coupon', null, 'vikrestaurants');
	}

	/**
	 * Helper method used to redeem the specified coupon code.
	 *
	 * @param   mixed  $coupon  Either the coupon details or its code.
	 * @param   array  $data    The search data.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function redeemCoupon($coupon, array $data = [])
	{
		if (empty($coupon))
		{
			// coupon code not specified
			$this->setError(JText::translate('VRCOUPONNOTVALID'));
			return false;
		}

		// inject "restaurant" group within search arguments
		$data['group'] = 'restaurant';

		// inject total deposit
		$data['total'] = $this->getTotalDeposit($data);

		try
		{
			// validate coupon code compliance
			$coupon = (new E4J\VikRestaurants\Coupon\CouponValidator($data))->validate($coupon);
		}
		catch (Exception $e)
		{
			// cannot apply the coupon code
			$this->setError(JText::translate('VRCOUPONNOTVALID'));
			return false;
		}

		// coupon valid, register it within the user session
		JFactory::getSession()->set('reservation.coupon', $coupon, 'vikrestaurants');

		return true;
	}

	/**
	 * Revalidates the internal coupon code, since the cart might be no more
	 * compliant with the coupon restrictions after some changes.
	 * 
	 * @param   array  $data  The search data.
	 *
	 * @return  bool   True in case of valid coupon, false otherwise.
	 */
	public function revalidateCoupon(array $data = [])
	{
		$session = JFactory::getSession();

		// fetch coupon from the user session
		$coupon = $session->get('reservation.coupon', null, 'vikrestaurants');

		if (!$coupon)
		{
			// coupon discount not set
			return false;
		}

		// try to redeem the coupon code one more time
		if ($this->redeemCoupon($coupon, $data) === false)
		{
			// coupon no more valid, unset it
			$session->set('reservation.coupon', null, 'vikrestaurants');
			return false;
		}

		// coupon still valid
		return true;
	}
}
