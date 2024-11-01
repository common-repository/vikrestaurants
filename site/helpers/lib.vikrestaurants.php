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
 * VikRestaurants component helper.
 *
 * @since 1.0
 */
abstract class VikRestaurants
{	
	/**
	 * Returns a list of admin e-mails.
	 *
	 * @return 	array
	 */
	public static function getAdminMailList()
	{
		// get all e-mails
		$admin_mail_list = VREFactory::getConfig()->getString('adminemail');

		if (!strlen($admin_mail_list))
		{
			return array();
		}

		return array_map('trim', explode(',', $admin_mail_list));
	}
	
	/**
	 * Returns the admin e-mail.
	 * If not specified, the one set in the global
	 * configuration of the CMS will be used
	 *
	 * @return 	string
	 */
	public static function getAdminMail()
	{
		// get all e-mails
		$mails = self::getAdminMailList();

		if ($mails)
		{
			// returns first e-mail available
			return $mails[0];
		}

		// use owner e-mail
		return JFactory::getApplication()->get('mailfrom');
	}
	
	/**
	 * Returns the sender mail.
	 *
	 * @return 	string
	 */
	public static function getSenderMail()
	{
		// get sender from config
		$sender = VREFactory::getConfig()->getString('senderemail');

		if (empty($sender))
		{
			// missing sender, use the default one
			$sender = self::getAdminMail();
		}

		return $sender;
	}

	/**
	 * Checks whether the restaurant section is enabled.
	 *
	 * @return 	boolean
	 */
	public static function isRestaurantEnabled()
	{
		return VREFactory::getConfig()->getBool('enablerestaurant', false);
	}

	/**
	 * Checks whether the take-away section is enabled.
	 *
	 * @return 	boolean
	 */
	public static function isTakeAwayEnabled()
	{
		return VREFactory::getConfig()->getBool('enabletakeaway', false);
	}
	
	/**
	 * Checks whether the component should support multi-lingual contents.
	 *
	 * @return 	boolean
	 */
	public static function isMultilanguage()
	{
		return VREFactory::getConfig()->getBool('multilanguage', false);
	}
	
	/**
	 * Checks if the restaurant performs a continuous opening time
	 * or whether it works with shifts.
	 *
	 * @return 	boolean
	 */
	public static function isContinuosOpeningTime()
	{
		return VREFactory::getConfig()->getUint('opentimemode') == 0;
	}

	/**
	 * Checks whether the specified API library is enabled.
	 * The configuration currently supports the following APIs:
	 * 
	 * - Places API       places
	 * - Directions API   directions
	 * - Maps Static API  staticmap
	 *
	 * @param 	mixed 	 $api  The API library to check. If not specified,
	 * 						   the default API Key should be checked.
	 *
	 * @return 	boolean  True if enabled, false otherwise.
	 *
	 * @since 	1.8
	 */
	public static function isGoogleMapsApiEnabled($api = null)
	{
		$config = VREFactory::getConfig();

		// return FALSE in case the API Key is missing
		if (!$config->get('googleapikey'))
		{
			return false;
		}

		if (!$api)
		{
			// nothing else to check, return TRUE
			return true;
		}

		// check if the specified API library is enabled
		return $config->getBool('googleapi' . strtolower($api));
	}

	/**
	 * Checks whether the review system is enabled.
	 *
	 * @return 	boolean
	 */
	public static function isReviewsEnabled()
	{
		return VREFactory::getConfig()->getBool('enablereviews');
	}
	
	/**
	 * Checks whether the review system (for take-away) is enabled.
	 *
	 * @return 	boolean
	 */
	public static function isTakeAwayReviewsEnabled()
	{
		return self::isReviewsEnabled() && VREFactory::getConfig()->getBool('revtakeaway');
	}

	/**
	 * Checks whether dashboard should display a restaurant overview.
	 *
	 * @return 	boolean
	 *
	 * @since 	1.8.3
	 */
	public static function isRestaurantOnDashboard()
	{
		return self::isRestaurantEnabled()
			&& VREFactory::getConfig()->getBool('ondashboard')
			&& JFactory::getUser()->authorise('core.access.reservations', 'com_vikrestaurants');
	}

	/**
	 * Checks whether dashboard should display a take-away overview.
	 *
	 * @return 	boolean
	 *
	 * @since 	1.8.3
	 */
	public static function isTakeAwayOnDashboard()
	{
		return self::isTakeAwayEnabled()
			&& JFactory::getUser()->authorise('core.access.tkorders', 'com_vikrestaurants');
	}
	
	/**
	 * Checks whether the reservations are currently allowed.
	 *
	 * @return 	boolean
	 */
	public static function isReservationsAllowed()
	{
		return self::isReservationsAllowedOn(static::now());
	}
	
	/**
	 * Checks whether the reservations are allowed on the specified date.
	 *
	 * @param 	integer  $timestamp  The timestamp of the date to check.
	 *
	 * @return 	boolean
	 */
	public static function isReservationsAllowedOn($timestamp)
	{
		return VREFactory::getConfig()->getInt('stopuntil') <= $timestamp;
	}

	/**
	 * Checks whether the take-away orders are currently allowed.
	 *
	 * @return 	boolean
	 */
	public static function isTakeAwayReservationsAllowed()
	{
		return self::isTakeAwayReservationsAllowedOn(static::now());
	}
	
	/**
	 * Checks whether the take-away orders are allowed on the specified date.
	 *
	 * @param 	integer  $timestamp  The timestamp of the date to check.
	 *
	 * @return 	boolean
	 */
	public static function isTakeAwayReservationsAllowedOn($timestamp)
	{
		/**
		 * Convert date string to UNIX timestamp.
		 *
		 * @since 1.8.2
		 */
		if (!preg_match("/^\d+$/", $timestamp))
		{
			$timestamp = static::createTimestamp($timestamp);
		}

		return VREFactory::getConfig()->getInt('tkstopuntil') <= $timestamp;
	}
	
	/**
	 * Returns the minimum cost needed to accept a take-away order.
	 *
	 * @param 	float  $areaCost  An optional delivery area cost.
	 * @param 	array  $args      An associative array with the searched query.
	 *
	 * @return 	float  The minimum area cost that will be used.
	 */
	public static function getTakeAwayMinimumCostPerOrder($areaCost = 0, array $args = null)
	{
		$config = VREFactory::getConfig();

		// get highest minimum required cost between config and delivery area
		$mincost = max(array($config->getFloat('mincostperorder'), (float) $areaCost));

		// search if we have a valid array
		if (!$args)
		{
			// create search array according to the details held by the cart instance
			$args = static::getCartSearchArray();
		}

		// init special days manager
		$sdManager = new VRESpecialDaysManager('takeaway');
		// set checkin date
		$sdManager->setStartDate($args['date']);
		// set checkin time
		$sdManager->setCheckinTime($args['hourmin']);
		// get first available special day
		$sd = $sdManager->getFirst();

		/**
		 * When the special day applies a minimum cost per order higher
		 * than 0, overwrite the default cost with the new one.
		 *
		 * @since 1.8.3
		 */
		if ($sd && $sd->minCostOrder)
		{
			$mincost = $sd->minCostOrder;
		}

		$dispatcher = VREFactory::getEventDispatcher();

		/**
		 * Plugins can use this hook to override the minimum cost 
		 * at runtime. The highest returned amount will be always used.
		 *
		 * In case the plugins returned something, the global cost will
		 * be always ignored, even if higher.
		 *
		 * @param 	float  $mincost  The minimum cost based on configuration and delivery area.
		 * @param 	array  $args     An associative array containing the order query.
		 *
		 * @return 	float  The minimum cost to use.
		 *
		 * @since 	1.8.3
		 */
		$return = $dispatcher->trigger('onCalculateOrderMinCost', array($mincost, $args));

		// filter the array in order to exclude all empty values
		$return = array_values(array_filter($return));
		
		// check whether the plugins returned something
		if ($return)
		{
			// override cost with the highest returned value
			$mincost = max($return);
		}

		return $mincost;
	}

	/**
	 * Returns the delivery service charge to be used.
	 *
	 * @param 	mixed 	$total  The current total net. If not specified, only
	 * 							the configuration delivery amount will be returned.
	 * @param 	mixed 	$area   Either an array or an object containing the delivery
	 * 							area details. If specified, the area charge will be
	 * 							added to the delivery charge.
	 *
	 * @return 	float 	The delivery service charge.
	 *
	 * @since 	1.2
	 */
	public static function getTakeAwayDeliveryServiceAddPrice($total = null, $area = null)
	{
		$config = VREFactory::getConfig();

		// get delivery charge
		$charge = $config->getFloat('dsprice');

		if (is_null($total))
		{
			// backward compatibility, just return the default value
			return $charge;
		}

		// get percentage or total
		$percentot = $config->getUint('dspercentot');

		if ($percentot == 1)
		{
			// percentage amount, calculate charge on total net
			$charge = $total * $charge / 100.0;
		}

		if (!is_null($area))
		{
			$area = (object) $area;

			// sum area charge to delivery charge
			$charge += $area->charge;
		}

		/**
		 * Always round the calculated amount to 2 decimals, in order
		 * to avoid roundings when saving the amount in the database.
		 *
		 * @since 1.8
		 */
		return round($charge, 2);
	}

	/**
	 * Returns the pickup service charge to be used.
	 *
	 * @param 	mixed 	$total  The current total net. If not specified, only
	 * 							the configuration pickup amount will be returned.
	 *
	 * @return 	float 	The pickup service charge.
	 *
	 * @since 	1.2
	 */
	public static function getTakeAwayPickupAddPrice($total = null)
	{
		$config = VREFactory::getConfig();

		// get pickup charge
		$charge = $config->getFloat('pickupprice');

		if (is_null($total))
		{
			// backward compatibility, just return the default value
			return $charge;
		}

		// get percentage or total
		$percentot = $config->getUint('pickuppercentot');

		if ($percentot == 1)
		{
			// percentage amount, calculate charge on total net
			$charge = $total * $charge / 100.0;
		}

		/**
		 * Always round the calculated amount to 2 decimals, in order
		 * to avoid roundings when saving the amount in the database.
		 *
		 * @since 1.8
		 */
		return round($charge, 2);
	}

	/**
	 * Checks whether the delivery charge should be applied or not.
	 *
	 * @param 	mixed 	 $cart  The cart instance.
	 *
	 * @return 	boolean  True in case of free delivery, false otherwise.
	 *
	 * @since 	1.8.3
	 */
	public static function isTakeAwayFreeDeliveryService($cart = null)
	{
		if (!$cart)
		{
			// recover cart instance if not specified
			$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();
		}

		$config = VREFactory::getConfig();

		// fetch threshold from configuration
		$threshold = $config->getFloat('freedelivery');
		
		/**
		 * Consider the total cost of the prices before the taxes calculation.
		 *
		 * @since 1.8.3
		 * 
		 * Subtract the total discount in order to avoid offering the delivery
		 * for extremely discounted orders.
		 * 
		 * @since 1.9
		 */
		$total = max(0, $cart->getTotalCost() - $cart->getTotalDiscount());

		// prepare search arguments
		$args = static::getCartSearchArray($cart);

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = VREFactory::getPlatform()->getDispatcher();

		/**
		 * Plugins can use this hook to override the threshold used
		 * to offer free deliveries at runtime.
		 * The highest returned amount will be always used.
		 *
		 * In case the plugins returned something, the global threshold will
		 * be always ignored, even if higher.
		 *
		 * @param   float  $threshold  The free delivery threshold.
		 * @param   Cart   $cart       The cart instance.
		 * @param   array  $args       An associative array containing the order query.
		 *
		 * @return  float  The threshold to use.
		 *
		 * @since   1.8.3
		 */

		$result = $dispatcher->filter('onCalculateFreeDeliveryThreshold', [$threshold, $cart, $args]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		// check whether the plugins returned something
		if ($result->has())
		{
			// override threshold with the highest returned value
			$threshold = max($result->numbers());
		}

		// check whether the specified threshold is higher
		// then the cart total cost
		return $threshold <= $total;
	}

	/**
	 * Returns a list of restaurant origin addresses.
	 * 
	 * @param 	boolean  $full  True to return the whole record of the locations, false
	 *                          to obtain an array of addresses (added @since 1.8.5).
	 *
	 * @return 	array
	 */
	public static function getTakeAwayOriginAddresses($full = false)
	{
		$dbo = JFactory::getDbo();

		$list = [];

		/**
		 * Load origins from the apposite database table.
		 * 
		 * @since 1.8.5
		 */
		$q = $dbo->getQuery(true);

		if ($full)
		{
			$q->select('*');
		}
		else
		{
			$q->select($dbo->qn('address'));
		}

		$q->from($dbo->qn('#__vikrestaurants_origin'));
		$q->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		
		if (!$full)
		{
			return $dbo->loadColumn();
		}

		return $dbo->loadObjectList();
	}

	/**
	 * Returns the a list of closing days.
	 *
	 * @return 	array
	 */
	public static function getClosingDays()
	{
		$config = VREFactory::getConfig();

		$_str = $config->get('closingdays', '');
		
		if (!strlen($_str))
		{
			return array();
		}

		$list = explode(';;', $_str);

		foreach ($list as &$cd)
		{
			$_app = explode(':', $cd);

			$cd = array(
				'ts'   => $_app[0],
				'date' => date($config->get('dateformat'), $_app[0]),
				'freq' => $_app[1],
			);
		}

		return $list;
	}
	
	/**
	 * Returns the list of columns to display in the reservations list.
	 *
	 * @return 	array
	 */
	public static function getListableFields()
	{
		$str = VREFactory::getConfig()->get('listablecols');
		
		if (empty($str))
		{
			return array();
		}
		
		return explode(',', $str);
	}

	/**
	 * Returns the list of custom fields to display in the reservations list.
	 *
	 * @return 	array
	 */
	public static function getListableCustomFields()
	{
		$str = VREFactory::getConfig()->get('listablecf');
		
		if (empty($str))
		{
			return array();
		}
		
		return explode(',', $str);
	}
	
	/**
	 * Returns the list of columns to display in the take-away orders list.
	 *
	 * @return 	array
	 */
	public static function getTakeAwayListableFields()
	{
		$str = VREFactory::getConfig()->get('tklistablecols');
		
		if (empty($str))
		{
			return array();
		}
		
		return explode(',', $str);
	}

	/**
	 * Returns the list of custom fields to display in the take-away orders list.
	 *
	 * @return 	array
	 */
	public static function getTakeAwayListableCustomFields()
	{
		$str = VREFactory::getConfig()->get('tklistablecf');
		
		if (empty($str))
		{
			return array();
		}
		
		return explode(',', $str);
	}

	/**
	 * Returns the texts to display in the print order view.
	 *
	 * @return 	array
	 */
	public static function getPrintOrdersText()
	{
		$text = VREFactory::getConfig()->getArray('printorderstext', array());

		// merge saved text with default array
		return array_merge(
			array(
				'header' => '',
				'footer' => '',
			),
			$text
		);
	}

	/**
	 * Returns the confirmation message that will be asked while deleting an item.
	 * In case the confirmation message is disabled, an empty string will be returned.
	 *
	 * @return 	string
	 *
	 * @since 	1.8
	 */
	public static function getConfirmSystemMessage()
	{
		if (VREFactory::getConfig()->getBool('askconfirm', true))
		{
			return JText::translate('VRSYSTEMCONFIRMATIONMSG');
		}

		return '';
	}

	/**
	 * Returns the audio file that will be used to play a
	 * notification sound every time a new reservation/order
	 * comes in.
	 *
	 * It is possible to use a different audio simply by uploading
	 * that file within the admin/assets/audio/ folder. The most
	 * recent file will be always used.
	 *
	 * @return 	string 	The file URI.
	 *
	 * @since 	1.8
	 */
	public static function getNotificationSound()
	{
		// get all files placed within audio folder
		$files = glob(VREADMIN . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR . '*');

		// take only audio files (exclude default one too)
		$files = array_values(array_filter($files, function($f)
		{
			if (preg_match("/[\/\\\\]notification\.mp3$/i", $f))
			{
				// ignore default file
				return false;
			}

			// keep only the most common audio files
			return preg_match("/\.(mp3|mp4|wav|ogg|aac|flac)$/i", $f);
		}));

		if (!$files)
		{
			// no additional audio files, use the default one
			return VREASSETS_ADMIN_URI . 'audio/notification.mp3';
		}

		// sort files from the most recent to the oldest
		usort($files, function($a, $b)
		{
			// sort by descending creation date
			return filemtime($b) - filemtime($a);
		});

		// return most recent file
		return VREASSETS_ADMIN_URI . 'audio/' . basename($files[0]);
	}
	
	/**
	 * Loads the cart dependencies.
	 *
	 * @return 	void
	 * @deprecated 1.10  Without replacement.
	 */
	public static function loadCartLibrary()
	{
		VRELoader::import('library.cart.cart');
		
		VRELoader::import('library.cart.item');
		VRELoader::import('library.cart.itemgroup');
		VRELoader::import('library.cart.topping');
		
		VRELoader::import('library.cart.deals');
		VRELoader::import('library.cart.discount');
	}

	/**
	 * Loads the deals dependencies.
	 *
	 * @return 	void
	 * 
	 * @deprecated 1.10  Without replacement.
	 */
	public static function loadDealsLibrary()
	{
		VRELoader::import('library.deals.handler');
	}

	/**
	 * Loads the banking dependencies.
	 *
	 * @param 	array 	$files  A list of dependencies to include.
	 * 							Loads all if empty.
	 *
	 * @return 	void
	 * 
	 * @deprecated 1.10  Directly use VRELoader::import('library.banking.creditcard') instead.
	 */
	public static function loadBankingLibrary($files = array())
	{
		if (!count($files) || in_array('creditcard', $files))
		{
			VRELoader::import('library.banking.creditcard');
		}
	}

	/**
	 * Loads the crypto dependencies.
	 *
	 * @return 	void
	 * 
	 * @deprecated 1.10  Directly use VRELoader::import('library.crypt.cipher') instead.
	 */
	public static function loadCryptLibrary()
	{
		VRELoader::import('library.crypt.cipher');
	}

	/**
	 * Loads the APIs Framework dependencies.
	 *
	 * @return 	void
	 */
	public static function loadFrameworkApis()
	{
		VRELoader::import('library.apislib.autoload');
		VRELoader::import('library.apislib.framework');
		VRELoader::import('library.apislib.login');
	}

	/**
	 * Flushes older API logs.
	 *
	 * @return 	void
	 * 
	 * @since 1.6
	 * @deprecated 1.10  Use VikRestaurantsModelApilog::flush() instead.
	 */
	public static function flushApiLogs()
	{
		// flush stored API logs
		JModelVRE::getInstance('apilog')->flush();
	}

	/**
	 * In case the group owns people that don't belong to the same
	 * family, the system will multiply the number of people by the
	 * configuration factor, so that the required distances can be
	 * maintained between the people.
	 *
	 * @param 	mixed 	 $people  The number of selected people. If not specified, this
	 * 							  value will be recovered from the request as 'people'.
	 * @param 	mixed 	 $family  True whether the customer agreed that all the members
	 * 							  belong to the same family. If not specified, this value
	 * 							  will be recovered from the request as 'family'.
	 *
	 * @return 	integer  The resulting number of people to apply to
	 * 					 the availability search.
	 *
	 * @since 	1.8
	 */
	public static function getPeopleSafeDistance($people = null, $family = null)
	{
		$input = JFactory::getApplication()->input;

		if (is_null($people))
		{
			// recover people from request
			$people = $input->get('people', 1, 'uint');
		}

		$config = VREFactory::getConfig();

		// make sure safe distance is supported
		if (!$config->getBool('safedistance'))
		{
			// do not alter number of people
			return $people;
		}

		if (is_null($family))
		{
			// recover family from request
			$family = $input->get('family', false, 'bool');
		}

		if ($family)
		{
			// all family members, use the specified number of people
			return $people;
		}

		// multiply the number of people by the specified factor
		return ceil($people * max(array(1, $config->getFloat('safefactor'))));
	}
	
	/**
	 * Checks whether the user can cancel an order.
	 *
	 * @param 	object   $order  The order instance.
	 * @param 	integer  $type   The order type (0 for restaurant, 1 for take-away).
	 *
	 * @return  boolean  True if possible, false otherwise.
	 */
	public static function canUserCancelOrder($order, $type = null)
	{
		if (is_null($type))
		{
			// fetch type from order class
			$type = preg_match("/Restaurant$/i", get_class($order)) ? 0 : 1;
		}

		// make sure the order is confirmed
		if ($order->statusRole != 'APPROVED')
		{
			// order not confirmed
			return false;
		}

		$config = VREFactory::getConfig();

		if ($type == 0)
		{
			// check restaurant cancellation
			$enabled = $config->getBool('enablecanc');
			$mintime = $config->getUint('canctime');
			$minunit = $config->getString('cancunit', 'days');
			$maxmins = $config->getUint('cancmins');
		}
		else
		{
			// check take-away cancellation
			$enabled = $config->getBool('tkenablecanc');
			$mintime = $config->getUint('tkcanctime');
			$minunit = $config->getString('tkcancunit', 'days');
			$maxmins = $config->getUint('tkcancmins');
		}

		if (!$enabled)
		{
			// do not go ahead in case the cancellation is disabled
			return false;
		}

		// get current time
		$now = static::now();

		/**
		 * Check whether it is still possible to cancel the reservation
		 * by comparing the creation date time with the maximum number of minutes.
		 * In example, it could be possible to cancel a reservation only within
		 * 5 minutes since the purchase date time.
		 *
		 * @since 1.8
		 */
		if ($maxmins > 0)
		{
			// sum maximum number of minutes to creation date time
			$creation = strtotime('+' . $maxmins . ' minutes', $order->created_on);

			if ($now > $creation)
			{
				// the current time exceeded the specified limit
				return false;
			}
		}

		// sum minimum required days/hours to the current date and time
		$checkin = strtotime('+' . $mintime . ' ' . $minunit, $now);

		if ($checkin >= $order->checkin_ts)
		{
			// not enough time to complete the cancellation, the check-in
			// is too close to the current date and time
			return false;
		}

		// build plugin event
		$event = 'onCheck' . ($type == 0 ? 'Reservation' : 'Order') . 'Cancellation';

		/**
		 * This event can be used to apply additional conditions to the 
		 * cancellation restrictions. When this event is triggered, the
		 * system already validated the standard conditions and the
		 * cancellation has been approved for the usage.
		 *
		 * The method might be built as:
		 * - onCheckReservationCancellation  for restaurant reservations;
		 * - onCheckOrderCancellation 		 for take-away orders.
		 *
		 * @param 	mixed 	 $order  The order/reservation to check.
		 *
		 * @return 	boolean  Return false to deny the cancellation.
		 *
		 * @since 	1.8
		 */
		$res = VREFactory::getEventDispatcher()->trigger($event, [$order]);

		// check if at least a plugin returned FALSE to prevent the cancellation
		return !in_array(false, $res, true);
	}

	/**
	 * Checks whether the user can approve its own order.
	 *
	 * @param 	object   $order  The order instance.
	 * @param 	integer  $type   The order type (0 for restaurant, 1 for take-away).
	 *
	 * @return  boolean  True if possible, false otherwise.
	 *
	 * @since 	1.8
	 */
	public static function canUserApproveOrder($order, $type = null)
	{
		if (is_null($type))
		{
			// fetch type from order class
			$type = preg_match("/Restaurant$/i", get_class($order)) ? 0 : 1;
		}

		// make sure the order is pending
		if ($order->statusRole != 'PENDING')
		{
			// order not pending
			return false;
		}

		$config = VREFactory::getConfig();

		// check if the order has been assigned to a payment
		if ($order->id_payment > 0)
		{
			// get payment details
			$payment = VikRestaurants::hasPayment(null, $order->id_payment);

			/**
			 * Check if the payment allows the self-confirmation.
			 *
			 * @since 1.8.1
			 */
			$enabled = $payment && $payment->selfconfirm;
		}
		// otherwise check parameter globally
		else
		{
			if ($type == 0)
			{
				// check restaurant self-confirmation
				$enabled = $config->getBool('selfconfirm');
			}
			else
			{
				// check take-away self-confirmation
				$enabled = $config->getBool('tkselfconfirm');
			}
		}

		if (!$enabled)
		{
			// do not go ahead in case the self-confirmation is disabled
			return false;
		}

		// build plugin event
		$event = 'onCheck' . ($type == 0 ? 'Reservation' : 'Order') . 'SelfConfirmation';

		/**
		 * This event can be used to apply additional conditions to the 
		 * self-confirmation restrictions. When this event is triggered, the
		 * system already validated the standard conditions and the
		 * confirmation has been approved for the usage.
		 *
		 * The method might be built as:
		 * - onCheckReservationSelfConfirmation  for restaurant reservations;
		 * - onCheckOrderSelfConfirmation        for take-away orders.
		 *
		 * @param 	mixed 	 $order  The order/reservation to check.
		 *
		 * @return 	boolean  Return false to deny the confirmation.
		 *
		 * @since 	1.8
		 */
		$res = VREFactory::getEventDispatcher()->trigger($event, array($order));

		// check if at least a plugin returned FALSE to prevent the confirmation
		return !in_array(false, $res, true);
	}

	/**
	 * Checks whether the user is allowed to order food.
	 *
	 * @param 	object   $order   The order instance.
	 * @param 	mixed 	 &$error  This parameter can be used to retrieve
	 * 							  the reason of the failure.
	 *
	 * @return  boolean  True if possible, false otherwise.
	 *
	 * @since 	1.8
	 */
	public static function canUserOrderFood($order, &$error = null)
	{
		$config = VREFactory::getConfig();

		// get ordering flag
		// 0: never
		// 1: at the restaurant
		// 2: always
		$flag = $config->getUint('orderfood');

		if ($flag == 0)
		{
			// food ordering not allowed
			return false;
		}

		// make sure the order is confirmed
		if ($order->statusRole != 'APPROVED')
		{
			// order not confirmed
			$error = JText::translate('VREORDERFOOD_DISABLED_STATUS');
			
			return false;
		}

		$now = static::now();

		// make sure the group arrived at the restaurant
		if ($flag == 1 && !$order->arrived && $order->checkin_ts > $now)
		{
			// not yet arrived
			$error = JText::translate('VREORDERFOOD_DISABLED_ARRIVED');

			return false;
		}

		// calculate time threshold
		$checkout = strtotime('+3 hours', $order->checkout);

		// allow ordering as long as the bill is open and
		// didn't pass more than 3 hours since the check-out
		if ($order->bill_closed == 1 || $checkout < $now)
		{
			// ordering no more allowed
			$error = JText::translate('VREORDERFOOD_DISABLED_BILLCLOSED');

			return false;
		}

		/**
		 * This event can be used to apply additional conditions to the 
		 * default restrictions. When this event is triggered, the
		 * system already validated the standard conditions and the
		 * food ordering has been approved for the usage.
		 *
		 * @param 	mixed 	 $order   The restaurant reservation to check.
		 * @param 	mixed 	 &$error  It is possible to include here the reason
		 * 							  of the failure.
		 *
		 * @return 	boolean  Return false to deny the food ordering.
		 *
		 * @since 	1.8
		 */
		$res = VREFactory::getEventDispatcher()->trigger('onCheckRestaurantFoodOrdering', array($order, &$error));

		// check if at least a plugin returned FALSE to prevent the food ordering
		return !in_array(false, $res, true);
	}

	/**
	 * Prepares the document related to the specified view.
	 * Used also to implement OPEN GRAPH protocol and to include
	 * global meta data.
	 *
	 * @param 	mixed 	$page 	The view object.
	 *
	 * @return 	void
	 */
	public static function prepareContent($page)
	{
		VRELoader::import('library.view.contents');

		$handler = VREViewContents::getInstance($page);

		/**
		 * Set the browser page title.
		 *
		 * @since 1.8
		 */
		$handler->setPageTitle();

		// show the page heading (if not provided, an empty string will be returned)
		$handler->getPageHeading(true);

		// set the META description of the page
		$handler->setMetaDescription();

		// set the META keywords of the page
		$handler->setMetaKeywords();

		// set the META robots of the page
		$handler->setMetaRobots();

		// create OPEN GRAPH protocol
		$handler->buildOpenGraph();

		// create MICRODATA
		$handler->buildMicrodata();
	}
	
	/**
	 * Loads global CSS and JS resources.
	 *
	 * @return 	void
	 */
	public static function load_css_js()
	{
		$vik = VREApplication::getInstance();

		// since jQuery is a required dependency, the framework should be 
		// invoked even if jQuery is disabled
		$vik->loadFramework('jquery.framework');
		
		$vik->addScript(VREASSETS_URI . 'js/jquery-ui.min.js');
		$vik->addScript(VREASSETS_URI . 'js/vikrestaurants.js');

		/**
		 * Load the CSS file containing the environment variables.
		 * 
		 * @since 1.9
		 */
		JHtml::fetch('vrehtml.assets.environment');

		$vik->addStyleSheet(VREASSETS_URI . 'css/jquery-ui.min.css');
		$vik->addStyleSheet(VREASSETS_URI . 'css/vikrestaurants.css');
		$vik->addStyleSheet(VREASSETS_URI . 'css/input-select.css');
		$vik->addStyleSheet(VREASSETS_URI . 'css/vikrestaurants-mobile.css');

		/**
		 * Loads the custom CSS file.
		 * 
		 * @since 1.9  Moved in a specified helper function.
		 */
		JHtml::fetch('vrehtml.assets.customcss');

		/**
		 * Loads utils.
		 *
		 * @since 1.8
		 */
		JHtml::fetch('vrehtml.assets.utils');

		/**
		 * Always instantiate the currency object.
		 *
		 * @since 1.7.4
		 */
		JHtml::fetch('vrehtml.assets.currency');

		/**
		 * Auto set CSRF token to ajaxSetup so all jQuery ajax call will contain CSRF token.
		 *
		 * @since 1.9
		 */
		JHtml::fetch('vrehtml.sitescripts.ajaxcsrf');

		/**
		 * Load the customizer tools.
		 * 
		 * @since 1.9
		 */
		if (JFactory::getApplication()->getUserStateFromRequest('vre.customizer.script', 'vikrestaurants_customizer', false, 'bool'))
		{
			$vik->addScript(VREASSETS_URI . 'js/customizer.js');
			$vik->addStyleSheet(VREASSETS_URI . 'css/customizer.css');
		}
	}

	/**
	 * Returns the current login URL.
	 *
	 * @param 	string 	 $url
	 * @param 	boolean  $xhtml
	 *
	 * @return 	string
	 */
	public static function getLoginReturnURL($url = '', $xhtml = false)
	{
		if (empty($url))
		{
			// get current URL
			return JUri::getInstance()->toString();
		}
		
		// route specified URL
		return VREApplication::getInstance()->routeForExternalUse($url, $xhtml);
	}
	
	/**
	 * Checks whether the requested arguments are valid to
	 * register a table booking.
	 *
	 * @param 	array 	 $args  An associative array containing the checkin
	 * 						    date, time and people.
	 *
	 * @return 	integer  The error code, otherwise 0 on success.
	 */
	public static function isRequestReservationValid($args)
	{
		$config = VREFactory::getConfig();

		if (empty($args['date']))
		{
			// missing date
			return 1;
		}
		
		if (empty($args['hourmin']))
		{
			// missing time
			return 2;
		}
		else
		{
			$tmp = explode(':', $args['hourmin']);

			if (count($tmp) != 2)
			{
				// invalid time string (HH:mm)
				return 2;
			}
			
			$args['hour'] = intval($tmp[0]);
			$args['min']  = intval($tmp[1]);

			/**
			 * Do not check anymore whether the specified minutes
			 * are a valid interval. This because the same check
			 * is already performed by isHourBetweenShifts() method,
			 * which makes sure that the selected time is an existing
			 * time slot.
			 *
			 * @since 1.8
			 */
			
			if (!self::isHourBetweenShifts($args, 1))
			{
				// the selected time is not part of a shift
				return 3;
			}
		}
		
		if (empty($args['people']) || $args['people'] < $config->getUint('minimumpeople') || $args['people'] > $config->getUint('maximumpeople'))
		{
			// the selected number of people is not allowed
			return 4;
		}
		
		// check date

		/**
		 * Workaround used to adjust the current time to the specific
		 * timezone for those websites that are not able to change the
		 * server configuration.
		 *
		 * @since 1.7.4
		 */
		$now = static::now();
		
		$_date = self::createTimestamp($args['date'], $args['hour'], $args['min']);
		
		if ($now > $_date)
		{
			// the selected check-in is in the past
			return 5;
		}

		// get first available checkin
		$next = strtotime('+' . $config->getUint('bookrestr') . ' minutes', $now);

		/**
		 * Get minimum date available.
		 *
		 * @since 1.8
		 */
		$minDate = $config->getUint('mindate');

		if ($minDate)
		{
			// increase current date by the specified number of days
			$tmp = strtotime('+' . $minDate . ' days 00:00:00', $now);
			// take highest timestamp between min date and asap
			$next = max(array($next, $tmp));
		}
		
		if ($next > $_date)
		{
			// the check-in time is not in the past but it is
			// before the first allowed time
			return 6;
		}

		/**
		 * Get maximum date available.
		 *
		 * @since 1.8
		 */
		$maxDate = $config->getUint('maxDate');

		if ($maxDate)
		{
			// increase current date by the specified number of days
			$maxDate = strtotime('+' . $maxDate . ' days 23:59:59', $now);
			
			if ($_date > $maxDate)
			{
				// the check-in time exceeds the maximum limit
				return 7;
			}
		}
		
		// valid request
		return 0;
	}
	
	/**
	 * Returns the error message related to the specified code.
	 *
	 * @param 	integer  $code  The error code.
	 *
	 * @return 	string 	 The error message.
	 *
	 * @see 	isRequestReservationValid()
	 */
	public static function getResponseFromReservationRequest($code)
	{
		$config = VREFactory::getConfig();

		/**
		 * Take maximum number of minutes between booking
		 * minutes restrictions and minimum check-in date.
		 *
		 * @since 1.8
		 */
		$asap = $config->getUint('bookrestr');
		$min  = $config->getUint('mindate') * 1440;

		$asap = max(array($asap, $min));

		$lookup = array( 
			'', 
			'VRRESERVATIONREQUESTMSG1', 
			'VRRESERVATIONREQUESTMSG2',
			'VRRESERVATIONREQUESTMSG3',
			'VRRESERVATIONREQUESTMSG4',
			'VRRESERVATIONREQUESTMSG5',
			/**
			 * Format Booking Minutes Restriction to the closest units.
			 *
			 * @since 1.7.4
			 */
			JText::sprintf(
				'VRRESERVATIONREQUESTMSG6',
				self::minutesToStr($asap)
			),
			/**
			 * Format Maximum Date restriction.
			 *
			 * @since 1.8
			 */
			JText::sprintf(
				'VRRESERVATIONREQUESTMSG7',
				self::minutesToStr($config->get('maxdate') * 1440)
			),
		);
		
		return $lookup[$code];
	}

	/**
	 * Returns the current time adjusted to the global timezone.
	 * Proxy for timestamp() method without passing any arguments.
	 *
	 * @return 	integer  The current time.
	 *
	 * @since 	1.7.4
	 */
	public static function now()
	{
		return self::timestamp();
	}

	/**
	 * Adjusts the given timestamp to the global timezone.
	 *
	 * @param 	integer  $ts  The timestamp to adjust.
	 *
	 * @return 	integer  The current time.
	 *
	 * @since 	1.7.4
	 */
	public static function timestamp($ts = null)
	{
		// create timezone instance
		$timezone = new DateTimeZone(JFactory::getConfig()->get('offset', 'UTC'));

		if (is_null($ts))
		{
			// get current time based on server configuration
			$date = new JDate();
		}
		else
		{
			// instantiate date object using the given timestamp
			$date = new JDate($ts);
		}

		// adjust to global timezone
		$date->setTimezone($timezone);

		// convert adjusted datetime to timestamp (based on server timezone)
		return strtotime($date->format('Y-m-d H:i:s', true));
	}

	/**
	 * Checks if the given time is in the past.
	 *
	 * @param 	mixed 	 $date 	A timestamp, a date string or an array of arguments.
	 *
	 * @return 	boolean  True if in the past, otherwise false.
	 *
	 * @since 	1.7.4
	 */
	public static function isTimePast($date)
	{
		if (is_integer($date))
		{
			// always convert timestamp to a supported date format
			$date = date('Y-m-d H:i', $date);
			// instantiate date object based on local timezone (this will adjust the date to UTC)
			$date = JDate::getInstance($date, JFactory::getConfig()->get('offset', 'UTC'));
		}
		else if (is_array($date))
		{
			// create timestamp from filters
			$ts = self::createTimestamp(@$date['date'], @$date['hour'], @$date['min']);

			// always convert timestamp to a supported date format
			$date = date('Y-m-d H:i', $ts);

			// instantiate date object based on local timezone (this will adjust the date to UTC)
			$date = JDate::getInstance($date, JFactory::getConfig()->get('offset', 'UTC'));
		}
		else
		{
			// instantiate date object based on UTC timezone, as it is supposed
			// to receive dates that were stored within the database
			$date = JDate::getInstance($date);
		}

		// check if the given date is in the past
		return $date->getTimestamp() <= JDate::getInstance()->getTimestamp();
	}
	
	/**
	 * Returns a human-readable string to check how time passed
	 * (or needs to pass) since the specified timestamp.
	 * The function supports conversion in minutes, hours, days and weeks.
	 *
	 * For example: 2 min. ago (past version) or in 2 min. (future version).
	 *
	 * In case the difference between the timestamp and the current time is
	 * longer than 2 weeks, it will be displayed the formatted date as fallback.
	 * 
	 * @param 	string 	 $dt_f 	 The date format as fallback.
	 * @param 	integer  $ts 	 The UNIX timestamp to check.
	 * @param 	boolean  $local  True to convert the provided timestamp
	 * 							 to the local timezone.
	 *
	 * @return 	string 	 The formatted string.
	 */
	public static function formatTimestamp($dt_f, $ts, $local = true)
	{
		/**
		 * Added $local parameter to adjust the specified timezone
		 * to the local offset if needed.
		 *
		 * @since 1.7.4
		 */
		if ($local)
		{
			// use current local time
			$now = self::now();
		}
		else
		{
			// use current server time
			$now = time();
		}

		if (abs($now - $ts) < 60)
		{
			return JText::translate('VRDFNOW');
		}
		
		$diff = ($now - $ts);
		
		$minutes = abs($diff) / 60;
		
		if ($minutes < 60)
		{
			$minutes = floor($minutes);

			return JText::sprintf('VRDFMINS' . ($diff > 0 ? 'AGO' : 'AFT'), $minutes);
		}
		
		$hours = $minutes / 60;

		if ($hours < 24)
		{
			$hours = floor($hours);
			
			if ($hours == 1)
			{
				return JText::translate('VRDFHOUR' . ($diff > 0 ? 'AGO' : 'AFT'));
			}

			return JText::sprintf('VRDFHOURS' . ($diff > 0 ? 'AGO' : 'AFT'), $hours);
		}
		
		$days = $hours / 24;

		if ($days < 7)
		{
			$days = floor($days);

			if ($days == 1)
			{
				return JText::translate('VRDFDAY' . ($diff > 0 ? 'AGO' : 'AFT'));
			}

			return JText::sprintf('VRDFDAYS' . ($diff > 0 ? 'AGO' : 'AFT'), $days);
		}
		
		$weeks = $days / 7;

		if ($weeks < 3)
		{
			$weeks = floor($weeks);

			if ($weeks == 1)
			{
				return JText::translate('VRDFWEEK' . ($diff > 0 ? 'AGO' : 'AFT'));
			}

			return JText::sprintf('VRDFWEEKS' . ($diff > 0 ? 'AGO' : 'AFT'), $weeks);
		}

		if (!$dt_f)
		{
			// do not return anything in case of missing format
			return '';
		}
		
		/**
		 * Adjust date time to local timezone.
		 *
		 * @since 1.7.4
		 */
		return JHtml::fetch('date', $ts, $dt_f, date_default_timezone_get());
	}

	/**
	 * Helper method to format the specified minutes to the closest unit.
	 * For example, 150 minutes will be formatted as "1 hour & 30 min.".
	 *
	 * @param 	string 	 $minutes 	The minutes amount.
	 *
	 * @return 	string 	 The formatted string.
	 */
	public static function minutesToStr($minutes)
	{
		$min_str   = array( JText::translate('VRSHORTCUTMINUTE') );

		/**
		 * Try using the front-end language key in case
		 * the VRSHORTCUTMINUTE text is not translated.
		 *
		 * @since 1.8
		 */
		if ($min_str[0] == 'VRSHORTCUTMINUTE')
		{
			$min_str[0] = JText::translate('VRMINSHORT');
		}
		
		$hours_str = array( JText::translate('VRFORMATHOUR') , JText::translate('VRFORMATHOURS') );
		$days_str  = array( JText::translate('VRFORMATDAY')  , JText::translate('VRFORMATDAYS')  );
		$weeks_str = array( JText::translate('VRFORMATWEEK') , JText::translate('VRFORMATWEEKS') );
		
		$comma_char = JText::translate('VRFORMATCOMMASEP');
		$and_char 	= JText::translate('VRFORMATANDSEP');
		
		$is_negative = $minutes < 0 ? 1 : 0;
		$minutes 	 = abs($minutes);
		
		$format = "";

		while ($minutes >= 60)
		{
			$app_str = "";

			if ($minutes >= 10080)
			{
				// weeks
				$val = intval($minutes / 10080);

				// if greater than 1 -> multiple, otherwise single
				$app_str = $val . ' ' . $weeks_str[(int) ($val > 1)];
				$minutes = $minutes % 10080;
			} 
			else if ($minutes >= 1440)
			{
				// days
				$val = intval($minutes / 1440);

				// if greater than 1 -> multiple, otherwise single
				$app_str = $val . ' ' . $days_str[(int) ($val > 1)];
				$minutes = $minutes % 1440;
			}
			else
			{
				// hours
				$val = intval($minutes / 60);

				// if greater than 1 -> multiple, otherwise single
				$app_str = $val . ' ' . $hours_str[(int) ($val > 1)];
				$minutes = $minutes % 60;
			}
			
			$sep = '';
			
			if ($minutes > 0)
			{
				$sep = $comma_char;
			}
			else if ($minutes == 0)
			{
				$sep = " $and_char";
			}
			
			$format .= (!empty($format) ? $sep . ' ' : '') . $app_str;
		}
		
		if ($minutes > 0)
		{
			$format .= (!empty($format) ? " $and_char " : '') . $minutes . ' ' . $min_str[0];
		}
		
		if ($is_negative)
		{
			$format = '-' . $format;
		}
			
		return $format;
	}

	/**
	 * Fetches the CSS gradient to use in proportion with the specified
	 * reservations statuses (CONFIRMED or PENDING).
	 *
	 * @param 	array 	$list       A map of status codes.
	 * @param 	string 	$direction  The gradient direction.
	 *
	 * @return 	string 	the CSS rules.
	 *
	 * @since 	1.7
	 */
	public static function getCssGradientFromStatuses($list = [], $direction = 'right')
	{
		// make sure we have more than one status
		if (count(array_keys($list)) <= 1)
		{
			return false;
		}

		arsort($list);

		$total_count = 0;

		foreach ($list as $status => $count)
		{
			$total_count += $count;
		}

		$rgb_css = array();

		foreach ($list as $status => $count)
		{
			 $rgba = array();

			 if (JHtml::fetch('vrehtml.status.isapproved', 'restaurant', $status))
			 {
			 	$rgba = array(56, 200, 112, 1);
			 }
			 else
			 {
			 	$rgba = array(233, 184, 44, 1);
			 }

			 $perc = $count * 100 / $total_count;

			 $rgb_css[] = 'rgba(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . $rgba[3] . ') ' . $perc . '%';
		}

		$rgb_css = implode(',', $rgb_css);

		return "background: -webkit-linear-gradient($direction,$rgb_css);"
			 . "background: -o-linear-gradient($direction,$rgb_css);"
			 . "background: -moz-linear-gradient($direction,$rgb_css);"
			 . "background: linear-gradient(to $direction,$rgb_css);";
	}
	
	/**
	 * Checks whether the menu selected during the 
	 * booking process are valid.
	 *
	 * @param 	array 	 $args 	An associative array with the search arguments.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @since 	1.5
	 */
	public static function validateSelectedMenus($args)
	{
		// Get menus available for the selected date and time.
		// Obtain only the menus that can effectively be chosen.
		$menus = self::getAllAvailableMenusOn($args, $choosable = true);
		
		if (count($menus) == 0)
		{
			// no menus selection, request valid
			return true;
		}
		
		$total = 0;

		// iterate selected menus
		foreach ($args['menus'] as $id => $quantity)
		{
			$ok = false;
			
			// find whether the menu is available
			for ($i = 0; $i < count($menus) && !$ok; $i++)
			{
				if ($id == $menus[$i]->id)
				{
					// menu found, increase total quantity
					$total += $quantity;

					$ok = true;
				}
			}
		}
		
		// make sure the selected total is OK
		return $total == $args['people'];
	}
	
	/**
	 * Creates the UNIX timestamp related to the specified date, hour and minutes.
	 *
	 * @param 	string 	 $date  The date in the configuration format.
	 * @param 	integer  $hour  The time hours.
	 * @param 	integer  $min   The time minutes.
	 *
	 * @return 	integer  The UNIX timestamp, otherwise -1 on failure.
	 * 
	 * @deprecated 1.10  Use E4J\VikRestaurants\Helpers\DateHelper::getTimestamp() instead.
	 */
	public static function createTimestamp($date, $hour = 0, $min = 0)
	{
		return E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($date, $hour, $min);
	}
	
	/**
	 * Checks whether the specified checkin arguments are
	 * part of an existing working shift.
	 *
	 * @param 	array 	 $args    An associative array of checkin arguments.
	 * @param 	integer  $group   The group to check (1: restaurant, 2: takeaway).
	 * @param 	boolean  $strict  True to make sure that the selected time is a valid
	 * 							  time slot for bookings. Otherwise the method will check
	 * 							  whether the specified time stays between a shift.
	 * 
	 * @return 	boolean  True if supported, false otherwise.
	 */
	public static function isHourBetweenShifts($args, $group = 1, $strict = true)
	{
		/**
		 * Obtain list of available times and make
		 * sure the selected time is there.
		 *
		 * @since 1.8
		 */
		$times = JHtml::fetch('vikrestaurants.times', $group, $args['date']);

		$tmp = explode(':', $args['hourmin']);

		// calculate time in minutes
		$hm = (int) $tmp[0] * 60 + (int) $tmp[1];

		// iterate all working shifts
		foreach ($times as $shift)
		{
			// reset previous slot
			$prev = null;

			// iterate all times in shift
			foreach ($shift as $time)
			{
				$tmp = explode(':', $time->value);

				// calculate shift time in minutes
				$hm2 = (int) $tmp[0] * 60 + (int) $tmp[1];

				// strict mode on?
				if ($strict)
				{
					// make sure the time is exactly the same
					if ($hm == $hm2)
					{
						// the time is supported
						return true;
					}
				}
				else
				{
					// check if the specified time stays between this slot
					// and the previous one
					if ($prev !== null && $prev <= $hm && $hm <= $hm2)
					{
						// the time is supported
						return true;
					}
					
					// update previous slot
					$prev = $hm2;
				}
			}
		}
		
		// time not supported
		return false;
	}
	
	/**
	 * Finds the first available hour.
	 *
	 * @return 	string
	 */
	public static function getFirstAvailableHour()
	{
		if (self::isContinuosOpeningTime())
		{
			return VREFactory::getConfig()->getUint('hourfrom') . ':0';
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('MIN(' .  $dbo->qn('from') . ') AS ' . $dbo->qn('from'))
			->from($dbo->qn('#__vikrestaurants_shifts'));

		$dbo->setQuery($q, 0, 1);
		$from = (int) $dbo->loadResult();

		$h = floor($from / 60);
		$m = $from % 60;

		return $h . ':' . $m;
	}

	/**
	 * Returns the closest available time for the given day.
	 *
	 * Considering the shifts below:
	 * 12:00 - 14:00
	 * 18:00 - 23:00
	 *
	 * In case the current time is 13:35, the closest available time
	 * would be 13:xx where xx is the minute rounded to the previous shift.
	 * For example, in case of 30 minutes of interval, the time would be 13:30.
	 *
	 * In case the current time is 16:45, the closest available time
	 * would be 18:00, the first time available in the future.
	 *
	 * @param 	mixed 	 &$day 	  The timestamp to use. Null to use the current day.
	 * 							  In case the timestamp is specified, it must be adjusted
	 * 							  to the local timezone.
	 * @param 	boolean  $next 	  True whether the closest time should always be in the future.
	 * @param 	integer  $client  The section to check (1: restaurant, 2: takeaway).
	 *
	 * @return 	mixed 	 The hour and minutes string in case of success, using the format h:m.
	 * 					 False in case of failure.
	 *
	 * @since 	1.7.4
	 */
	public static function getClosestTime(&$day = null, $next = false, $client = 1)
	{
		$shifts = array();

		$config = VREFactory::getConfig();

		if (is_null($day))
		{
			// get current local time
			$day = self::now();

			if ($next && JFactory::getApplication()->isClient('site'))
			{
				if ($client == 1)
				{
					// In case of site client, we need to increase the current time
					// by the specified "Booking Minutes Restriction" setting.
					$day = strtotime('+' . $config->getUint('bookrestr') . ' minutes', $day);
				}

				/**
				 * Get minimum date available.
				 *
				 * @since 1.8
				 */
				$minDate = $config->getUint($client == 1 ? 'mindate' : 'tkmindate');

				if ($minDate)
				{
					// increase current date by the specified number of days
					$tmp = strtotime('+' . $minDate . ' days 00:00:00', self::now());
					// take highest timestamp between min date and asap
					$day = max(array($day, $tmp));
				}
			}
		}
		else if (is_string($day))
		{
			// get current time
			$now = self::now();

			/**
			 * Check whether the specified day is equals to the current one,
			 * in order to use the proper time in the future. 
			 *
			 * @since 1.8.3
			 */
			if (date('Y-m-d', $now) == $day)
			{
				// find current hour and minutes
				list($hour, $min) = explode(':', date('H:i', self::now()));
			}
			else
			{
				// then use midnight for a different date
				$hour = $min = 0;
			}

			// create timestamp for given string by using current hour and minutes
			$day = VikRestaurants::createTimestamp($day, $hour, $min);
		}

		// use filters to consider current local time
		$filters = array(
			'date' => date($config->get('dateformat'), $day),
			'hour' => date('H', $day),
			'min'  => date('i', $day),
		);

		if ($client == 1)
		{
			$min_int = $config->getUint('minuteintervals');
		}
		else
		{
			$min_int = $config->getUint('tkminint');
		}

		// Always round to the previous interval.
		// e.g. 11:29 -> 29 % 30 = 29 - 29 = 11:00
		// e.g. 12:45 -> 45 % 30 = 45 - 15 = 12:30
		// e.g. 13:57 -> 57 % 15 = 57 - 8  = 13:45
		$filters['min'] -= $filters['min'] % $min_int;

		if ($next === true)
		{
			// increase the minutes by the interval amount in order
			// to retrieve always the closest future time
			$filters['min'] += $min_int;

			// increase the hours by the number of exceeding minutes
			$filters['hour'] += floor($filters['min'] / 60);

			// always round the minutes
			$filters['min'] = $filters['min'] % 60;
		}

		// get the working shifts available for the specified day
		$shifts = JHtml::fetch('vikrestaurants.times', $client, $day);

		$tmp = $filters['hour'] * 60 + $filters['min'];

		// iterate all the working shifts
		foreach ($shifts as $shift)
		{
			/**
			 * Search for the closest time slot because
			 * the fetched time might not exist, as the
			 * minute intervals could not correspond to
			 * the shift intervals.
			 *
			 * @since 1.8
			 */
			foreach ($shift as $timeSlot)
			{
				// convert time to minutes
				$hm = JHtml::fetch('vikrestaurants.time2min', $timeSlot);

				if ($hm >= $tmp)
				{
					return $timeSlot->value;
				}
			}
		}

		// impossible to evaluate the closest time, return false
		return false;
	}

	/**
	 * Returns the closest available time for the given day (take-away section only).
	 *
	 * Considering the shifts below:
	 * 12:00 - 14:00
	 * 18:00 - 23:00
	 *
	 * In case the current time is 13:35, the closest available time
	 * would be 13:xx where xx is the minute rounded to the previous shift.
	 * For example, in case of 30 minutes of interval, the time would be 13:30.
	 *
	 * In case the current time is 16:45, the closest available time
	 * would be 18:00, the first time available in the future.
	 *
	 * @param 	mixed 	 &$day 	The timestamp to use. Null to use the current day.
	 * 							In case the timestamp is specified, it must be adjusted
	 * 							to the local timezone.
	 * @param 	boolean  $next 	True whether the closest time should always be in the future.
	 *
	 * @return 	mixed 	 The hour and minutes string in case of success, using the format h:m.
	 * 					 False in case of failure.
	 *
	 * @since 	1.8
	 */
	public static function getClosestTimeTakeAway(&$day = null, $next = false)
	{
		// get closest time for take-away section
		return self::getClosestTime($day, $next, 2);
	}

	/**
	 * Validates the selected time against the available ones.
	 * In case of invalid time, the first one will be used.
	 *
	 * @param 	string 	 &$time   The select time string.
	 * @param 	mixed 	 $shifts  The list of available times. In case a string was
	 * 							  passed, it will be used as date to retrieve all the
	 * 							  available time slots.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 *
	 * @since 	1.8
	 */
	public static function validateTakeAwayTime(&$time, $shifts)
	{
		if (is_string($shifts))
		{
			// obtain all the available times for pick-up and delivery
			$shifts = JHtml::fetch('vikrestaurants.takeawaytimes', $shifts);
		}

		if ($time)
		{
			// convert time to minutes
			$hm = JHtml::fetch('vikrestaurants.time2min', $time);
		}
		else
		{
			$hm = 0;
		}

		foreach ($shifts as $shift)
		{
			foreach ($shift as $slot)
			{
				// convert time to minutes
				$hm2 = JHtml::fetch('vikrestaurants.time2min', $slot);

				if (empty($slot->disable) && $hm == $hm2)
				{
					// time is valid
					return true;
				}
			}
		}

		if ($shifts)
		{
			// use first time available in case there is no selected time
			$sh = reset($shifts);
			$time = $sh[0]->value;
		}

		return false;
	}
	
	/**
	 * Checks whether the requested arguments are valid to
	 * register a take-away order.
	 *
	 * @param 	array 	 $args  An associative array containing the checkin
	 * 						    date, time and delivery.
	 *
	 * @return 	integer  The error code, otherwise 0 on success.
	 *
	 * @since 	1.2
	 */
	public static function isRequestTakeAwayOrderValid($args)
	{
		if (empty($args['date']))
		{
			// missing date
			return 1;
		}
		
		if (empty($args['hourmin']))
		{
			// missing time
			return 2;
		}
		else
		{
			$tmp = explode(':', $args['hourmin']);

			if (count($tmp) != 2)
			{
				// invalid time string (HH:mm)
				return 2;
			}
			
			$args['hour'] = intval($tmp[0]);
			$args['min']  = intval($tmp[1]);

			/**
			 * Do not check anymore whether the specified minutes
			 * are a valid interval. This because the same check
			 * is already performed by isHourBetweenShifts() method,
			 * which makes sure that the selected time is an existing
			 * time slot.
			 *
			 * @since 1.8
			 */
			
			if (!self::isHourBetweenShifts($args, 2))
			{
				// the selected time is not part of a shift
				return 3;
			}
		}

		// init special days manager
		$sdManager = new VRESpecialDaysManager('takeaway');
		// set checkin date
		$sdManager->setStartDate($args['date']);
		// set checkin time
		$sdManager->setCheckinTime($args['hourmin']);
		// get first available special day
		$sd = $sdManager->getFirst();

		if ($sd)
		{
			// set up delivery/pickup service configuration
			$delivery = $sd->delivery;
			$pickup   = $sd->pickup;
		}
		else
		{
			// get delivery service flag from configuration
			$service = VREFactory::getConfig()->getUint('deliveryservice');
			
			// set up delivery/pickup service configuration
			$delivery = $service == 1 || $service == 2;
			$pickup   = $service == 0 || $service == 2;
		}

		/**
		 * Make sure the selected service is supported.
		 *
		 * @since 1.8
		 */
		if ($args['delivery'])
		{
			// delivery service selected
			if (!$delivery)
			{
				// delivery service is not supported
				return 4;
			}
		}
		else
		{
			// pickup service selected
			if (!$pickup)
			{
				// pickup service is not supported
				return 4;
			}
		}
		
		return 0;
	}
	
	/**
	 * Returns the error message related to the specified code.
	 *
	 * @param 	integer  $code  The error code.
	 *
	 * @return 	string 	 The error message.
	 *
	 * @since 	1.2
	 *
	 * @see 	isRequestTakeAwayOrderValid()
	 */
	public static function getResponseFromTakeAwayOrderRequest($code)
	{
		$lookup = array( 
			'', 
			'VRTKORDERREQUESTMSG1', 
			'VRTKORDERREQUESTMSG2',
			'VRTKORDERREQUESTMSG3',
			'VRTKSERVICENOTALLOWEDERR',
		 );
		
		return $lookup[$code];
	}

	/**
	 * Returns a search array based on the query held
	 * by the cart instance. The returned array will 
	 * contain the selected check-in date and time as
	 * well as the type of service.
	 *
	 * @param 	mixed  $cart  The cart instance.
	 *
	 * @return 	array
	 *
	 * @since 	1.8.3
	 */
	public static function getCartSearchArray($cart = null)
	{
		if (!$cart)
		{
			// get cart instance
			$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();
		}

		$config = VREFactory::getConfig();

		// create return array
		$args = array();
		$args['date']     = date($config->get('dateformat'), $cart->getCheckinTimestamp());
		$args['hourmin']  = $cart->getCheckinTime();
		$args['delivery'] = $cart->getService() == 1;

		if (!preg_match("/:/", $args['hourmin']))
		{
			// use a valid time for BC
			$args['hourmin'] = '0:0';
		}

		// extract hours and minutes
		list($args['hour'], $args['min']) = explode(':', $args['hourmin']);

		return $args;
	}
	
	/**
	 * Validates the user arguments before registering an account.
	 *
	 * @param 	array 	 &$args   The user arguments.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 */
	public static function checkUserArguments(&$args, $ignore = false)
	{
		if (empty($args['firstname']) && empty($args['lastname']))
		{
			// at least one of these 2 arguments cannot be empty
			return false;
		}

		/**
		 * In case the username is not provided, take the specified
		 * e-mail address. In this way, developers can override the 
		 * layout of the login to get rid of the "username" field.
		 *
		 * @since 1.8
		 */
		if (empty($args['username']))
		{
			$args['username'] = $args['email'];
		}

		if (empty($args['password']))
		{
			// password cannot be empty
			return false;
		}

		/**
		 * Validate password only if confirmation is provided.
		 *
		 * @since 1.8
		 */
		if (isset($args['confpassword']) && strcmp($args['password'], $args['confpassword']))
		{
			// password do not match
			return false;
		}

		if (empty($args['email']) || !self::validateUserEmail($args['email']))
		{
			// e-mail is empty or invalid
			return false;
		}

		/**
		 * Compare the e-mail with the confirmation only if provided.
		 *
		 * @since 1.8
		 */
		if (isset($args['confemail']) && strcasecmp($args['email'], $args['confemail']))
		{
			// e-mail do not match
			return false;
		}

		// valid arguments
		return true;
	}
	
	/**
	 * Validates an e-mail address.
	 *
	 * @param 	string 	 $email  The e-mail address to validate.
	 *
	 * @return 	boolean  True if valid, false otherwise.
	 */
	public static function validateUserEmail($email = '')
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");

		if (is_bool($atIndex) && !$atIndex)
		{
			return false;
		}
		
		$domain 	= substr($email, $atIndex +1);
		$local  	= substr($email, 0, $atIndex);
		$localLen 	= strlen($local);
		$domainLen 	= strlen($domain);

		if ($localLen < 1 || $localLen > 64)
		{
			// local part length exceeded or too short
			return false;
		}

		if ($domainLen < 1 || $domainLen > 255)
		{
			// domain part length exceeded or too short
			return false;
		}
			
		if ($local[0] == '.' || $local[$localLen -1] == '.')
		{
			// local part starts or ends with '.'
			return false;
		}
				
		if (preg_match('/\\.\\./', $local))
		{
			// local part has two consecutive dots
			return false;
		}
					
		if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		{
			// character not valid in domain part
			return false;
		}
		
		if (preg_match('/\\.\\./', $domain))
		{
			// domain part has two consecutive dots
			return false;
		} 

		if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local)))
		{
			// character not valid in local part unless local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local)))
			{
				return false;
			}
		}

		if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A"))
		{
			// domain not found in DNS
			return false;
		}
		
		return true;
	}

	/**
	 * Registers a new Joomla User with the details specified in the given $args associative array.
	 *
	 * @param   array  $args  The user details.
	 *
	 * @return  mixed  The user ID on success, false on failure,
	 *                 the string status during the activation.
	 *
	 * @since   1.0
	 * @since   1.9  Alias for deprecated createNewJoomlaUser() method.
	 */
	public static function createNewUserAccount(array $args)
	{
		$app = JFactory::getApplication();

		// load com_users site language
		JFactory::getLanguage()->load('com_users', JPATH_SITE, JFactory::getLanguage()->getTag(), true);

		// save registration data within the user state, so that in case of
		// errors we can recover the entered details to auto-fill the form
		$app->setUserState('vre.cms.user.register', $args);

		if (VersionListener::isJoomla())
		{
			/**
			 * Autoload the form fields of com_users to avoid fatal errors, since Joomla 3.9.27
			 * seems to autoload the model forms/fields according to the current component.
			 *
			 * @since 1.8.5
			 */
			JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');

			/**
			 * Joomla 4.0 moved the XML forms into a different folder.
			 *
			 * @since 1.8.5
			 */
			JForm::addFormPath(JPATH_SITE . '/components/com_users/forms');
		}

		// load UsersModelRegistration
		JModelLegacy::addIncludePath(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_users' . DIRECTORY_SEPARATOR . 'models');
		$model = JModelLegacy::getInstance('registration', 'UsersModel');

		// adapt data for model
		$args['name'] 		= trim($args['firstname'] . ' ' . $args['lastname']);
		$args['email1'] 	= $args['email'];
		$args['password1'] 	= $args['password'];
		$args['block'] 		= 0;

		/**
		 * Attempt to hijack the Privacy Policy plugin to auto-flag
		 * the privacy consent of the newly registered user.
		 *
		 * @since 1.9
		 */

		// get current request arguments
		$option = $app->input->get('option');
		$task   = $app->input->get->get('task');
		$form   = $app->input->post->get('jform', []);

		if (VREFactory::getConfig()->getBool('gdpr'))
		{
			// force privacy consent in case GDPR setting was enabled
			$form['privacyconsent'] = ['privacy' => 1];
		}

		// hijack the Privacy Policy plugin condition
		$app->input->set('option', 'com_users');
		$app->input->get->set('task', 'registration.register');
		$app->input->post->set('jform', $form);

		/**
		 * It is now possible to validate the password against the com_users configuration.
		 * Compatible only with J4.x
		 * 
		 * @since 1.9
		 */
		if (VersionListener::isJoomla4x())
		{
			// obtain com_users registration form
			$form = $model->getForm();

			if ($form)
			{
				try
				{
					// validate the password against the com_users configuration
					$validate = $form->getField('password1')->validate($args['password']);

					if ($validate instanceof Exception)
					{
						$app->enqueueMessage($validate->getMessage(), 'error');
						return false;
					}
				}
				catch (Throwable $t)
				{
					// ignore in case of fatal errors
				}
			}
		}

		// register user
		$return = $model->register($args);

		// restore previous request arguments
		$app->input->set('option', $option);
		$app->input->get->set('task', $task);

		if ($return === false)
		{
			// impossible to save the user
			$app->enqueueMessage($model->getError(), 'error');
		}
		else if ($return === 'adminactivate')
		{
			// user saved: admin activation required
			$app->enqueueMessage(JText::translate('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
		}
		else if ($return === 'useractivate')
		{
			// user saved: self activation required
			$app->enqueueMessage(JText::translate('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
		}
		else
		{
			// user saved: can login immediately
			$app->enqueueMessage(JText::translate('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
		}

		if ($return !== false)
		{
			// unset user registration data on success
			$app->setUserState('vre.cms.user.register', null);
		}

		return $return;
	}

	/**
	 * Registers a new Joomla User with the details
	 * specified in the given $args associative array.
	 *
	 * @param 	array 	 $args 	The user details.
	 *
	 * @return 	mixed 	 The user ID on success, false on failure,
	 * 					 the string status during the activation.
	 * 
	 * @deprecated 1.10  Use VikRestaurants::createNewUserAccount() instead.
	 */
	public static function createNewJoomlaUser($args)
	{
		return static::createNewUserAccount($args);
	}
	
	/**
	 * Returns the details of the operator assigned
	 * to the current logged-in user.
	 *
	 * @return 	mixed  The operator details on success, false otherwise.
	 *
	 * @since 	1.4
	 */
	public static function getOperator()
	{
		VRELoader::import('library.operator.user');
		
		try
		{
			// get operator instance
			$operator = VREOperatorUser::getInstance();
		}
		catch (Exception $e)
		{
			// user is not logged-in or the account
			// is not assigned to any operator
			return false;
		}

		/**
		 * Returns a VREOperatorUser instance.
		 *
		 * @since 1.8
		 */
		return $operator;
	}

	/**
	 * Removes the credit card details assigned to reservations
	 * with a check-in a day in the past.
	 *
	 * @return 	void
	 *
	 * @since 	1.7
	 */
	public static function removeExpiredCreditCards()
	{
		$session = JFactory::getSession();

		$now = VikRestaurants::now();

		// if the session token does not exist, get a time in the past
		$check = intval($session->get('cc-flush-check', $now - 3600, 'vr'));

		if ($check < $now)
		{
			$dbo = JFactory::getDbo();

			/**
			 * @todo hold credit card details for a week rather than for a single day
			 */	

			// update restaurant reservations
			$q = $dbo->getQuery(true)
				->update($dbo->qn('#__vikrestaurants_reservation'))
				->set($dbo->qn('cc_details') . ' = ' . $dbo->q(''))
				->where($dbo->qn('checkin_ts') . ' + 86400 < ' . $now);

			$dbo->setQuery($q);
			$dbo->execute();

			// update take-away orders
			$q->clear('update')->update($dbo->qn('#__vikrestaurants_takeaway_reservation'));

			$dbo->setQuery($q);
			$dbo->execute();

			// check only every 15 minutes
			$session->set('cc-flush-check', $now + 15 * 60, 'vr');
		}
	}

	/**
	 * Validates the specified coupon against the
	 * specified arguments.
	 *
	 * @param 	mixed 	$coupon  Either the coupon code or the object itself.
	 * @param 	array 	$args    An array of validation arguments.
	 *
	 * @return 	mixed 	The coupon object in case of success, null otherwise.
	 *
	 * @since 1.8
	 * @deprecated 1.10  Use E4J\VikRestaurants\Coupon\CouponValidator instead.
	 */
	public static function getValidCoupon($coupon, $args)
	{
		try
		{
			return (new E4J\VikRestaurants\Coupon\CouponValidator($args))->validate($coupon);
		}
		catch (Exception $e)
		{
			return null;
		}
	}
	
	/**
	 * Checks whether the specified coupon can be used for
	 * a restaurant reservation and for the specified 
	 * number of participants.
	 *
	 * @param 	mixed 	 $coupon  Either the coupon code or the object itself.
	 * @param 	integer  $args    The number of selected people.
	 *
	 * @return 	mixed 	 The coupon object in case of success, null otherwise.
	 * 
	 * @deprecated 1.10  Use E4J\VikRestaurants\Coupon\CouponValidator instead.
	 */
	public static function validateCoupon($coupon, $people)
	{
		// validate coupon code
		return self::getValidCoupon($coupon, [
			// for restaurant only
			'group' => 0,
			// make sure the number of guests is allowed
			'people' => $people,
			// use the current date and time
			'date' => static::now(),
		]);
	}
	
	/**
	 * Checks whether the specified coupon can be used for
	 * a take-away order and for the specified cart details.
	 *
	 * @param 	mixed 	$coupon  Either the coupon code or the object itself.
	 * @param 	object  $cart    The cart instance.
	 *
	 * @return 	mixed 	The coupon object in case of success, null otherwise.
	 *
	 * @since 1.2
	 * @deprecated 1.10  Use E4J\VikRestaurants\Coupon\CouponValidator instead.
	 */
	public static function validateTakeawayCoupon($coupon, $cart)
	{
		// validate coupon code
		return self::getValidCoupon($coupon, [
			// for take-away only
			'group' => 1,
			// make sure the number of guests is allowed
			'total' => $cart->getTotalCost(),
			// use the selected check-in date and time
			'date' => $cart->getCheckinTimestamp(),
		]);
	}
	
	/**
	 * Helper method used to generate a serial code.
	 * In a remote case, this method may generate 2 identical codes.
	 * The probability to have 2 identical strings is:
	 * 1 / count($map)^$len
	 *
	 * @param 	integer  $length  The length of the serial code.
	 * @param 	string 	 $scope   The purpose of the serial code.
	 * @param 	array 	 $map 	  A map containing all the allowed tokens.
	 *
	 * @return 	string 	 The resulting serial code.
	 */
	public static function generateSerialCode($length = 12, $scope = null, $map = null)
	{
		$code = '';

		/**
		 * This event can be used to change the way the system generates
		 * a serial code. It is possible to edit the code or simply to
		 * alter the map of allowed tokens. In case the serial code
		 * didn't reach the specified length, the remaining characters
		 * will be generated according to the default algorithm.
		 *
		 * @param 	string 	 	 $code    The serial code.
		 * @param 	array|null 	 &$map    A map of allowed tokens.
		 * @param 	integer  	 $length  The length of the serial code.
		 * @param 	string|null  $scope   The purpose of the code.
		 *
		 * @return 	void
		 *
		 * @since 	1.8
		 */
		VREFactory::getEventDispatcher()->trigger('onGenerateSerialCode', array(&$code, &$map, $length, $scope));

		if (!is_scalar($code))
		{
			// reset code in case of invalid string
			$code = '';
		}

		// check if we already have a complete serial code
		if (strlen($code) >= $length)
		{
			// just return the specified number of characters
			return substr($code, 0, $length);
		}

		if (!$map)
		{
			// use default tokens if not specified/modified
			$map = array(
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'0123456789'
			);
		}
		else
		{
			// always treat as array
			$map = (array) $map;
		}
		
		// iterate until the specified length is reached
		for ($i = strlen($code); $i < $length; $i++)
		{
			// toss tokens block
			$_row = rand(0, count($map) - 1);
			// toss block character
			$_col = rand(0, strlen($map[$_row]) - 1);

			// append character to serial code
			$code .= (string) $map[$_row][$_col];
		}

		return $code;
	}

	/**
	 * Calculates the total amount to leave when trying
	 * book a table for the specified check-in.
	 *
	 * @param 	array 	$args 	An associative array containing the check-in details.
	 *
	 * @return 	float 	The total amount to leave.
	 *
	 * @since 1.8
	 * @deprecated 1.10  Use VikRestaurantsModelRescart::getTotalDeposit() instead.
	 */
	public static function getTotalDeposit($args)
	{
		return JModelVRE::getInstance('rescart')->getTotalDeposit($args);
	}

	/**
	 * Returns a list of available payments.
	 *
	 * @param 	integer  $group  The group to check (1: restaurant, 2: takeaway).
	 * @param 	mixed    $user   The user that requested the payment. If not specified,
	 * 							 the current user will be taken.
	 * @param 	mixed 	 $total  The total cost of the order. If not specified, it will
	 * 							 retrieved from the take-away cart.
	 *
	 * @return 	array 	 A list of payments.
	 *
	 * @since 	1.8
	 * @deprecated 1.10  Use PaymentsCollection::getInstance() instead.
	 */
	public static function getAvailablePayments($group, $user = null, $total = null)
	{
		$group = $group == 1 ? 'restaurant' : 'takeaway';

		/** @var E4J\VikRestaurants\Collection\Item[] */
		$payments = E4J\VikRestaurants\Payment\PaymentsCollection::getInstance()
			->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter);

		if ($group === 'restaurant')
		{
			$payments = $payments->filter(new E4J\VikRestaurants\Payment\Filters\RestaurantGroupFilter);
		}
		else
		{
			$payments = $payments->filter(new E4J\VikRestaurants\Payment\Filters\TakeAwayGroupFilter)
				->filter(new E4J\VikRestaurants\Payment\Filters\TotalCostFilter($total));
		}

		$payments = $payments->filter(new E4J\VikRestaurants\Payment\Filters\TrustedCustomerFilter($group, $user))
			->filter(new E4J\VikRestaurants\Payment\Filters\PluginAvailabilityFilter($group));

		return $payments;
	}

	/**
	 * Checks whether there is at least a published
	 * payment gateway for the specified section.
	 *
	 * @param 	integer  $group   The group to check (1: restaurant, 2: takeaway).
	 * @param 	integer  $id      An optional ID to obtain the specified payment.
	 * @param 	boolean  $strict  False to include also unpublished payments.
	 *
	 * @return 	mixed    In case we are searching by ID, the payment details will be
	 * 					 returned (false if not exists). Otherwise, true/false depending
	 * 					 on the number of published payments.
	 *
	 * @since 	1.8
	 * @deprecated  1.10  Use PaymentsCollection::getInstance() instead.
	 */
	public static function hasPayment($group = null, $id = null, $strict = true)
	{
		/** @var E4J\VikRestaurants\Collection\Item[] */
		$payments = E4J\VikRestaurants\Payment\PaymentsCollection::getInstance();

		if ($group === 'restaurant' || $group === 1)
		{
			$payments = $payments->filter(new E4J\VikRestaurants\Payment\Filters\RestaurantGroupFilter);
		}
		else
		{
			$payments = $payments->filter(new E4J\VikRestaurants\Payment\Filters\TakeAwayGroupFilter);
		}

		if ($strict)
		{
			$payments = $payments->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter);
		}

		if ($id)
		{
			$payments = $payments->filter(new E4J\VikRestaurants\Collection\Filters\NumberFilter('id', $id, '='));
		}

		if (!count($payments))
		{
			// no available payments
			return false;
		}

		return $id ? $payments->getIterator()[0] : true;
	}
	
	/**
	 * Returns a list of menus available for the restaurant.
	 *
	 * @param 	array 	 $args       The searched arguments. In case of missing
	 * 								 date, all the menus will be retrieved.
	 * @param 	boolean  $choosable  Whether to include only the menus that can be selected.
	 *
	 * @return 	array 	 A list of choosable menus.
	 *
	 * @since 	1.5
	 */
	public static function getAllAvailableMenusOn(array $args = array(), $choosable = false)
	{
		// Check if we have a closure. In case the date was
		// not passed, the system will ignore the closure.
		$closed = self::isClosingDayIgnoringDate($args);

		$ids = array();

		$sdList = null;

		// flag used to check whether all the customers of
		// the group are allowed to choose different menus
		$freedom = true;

		/**
		 * Do not use special days if the date was not specified.
		 * This allows us to retrieve all the menus.
		 *
		 * @since 1.8
		 */
		if (!empty($args['date']))
		{
			// instantiate special days manager
			$sdManager = new VRESpecialDaysManager('restaurant');

			// set date filter
			$sdManager->setStartDate($args['date']);

			if (!empty($args['hourmin']))
			{
				// set time filter
				$sdManager->setCheckinTime($args['hourmin']);
			}

			// get list of available special days
			$sdList = $sdManager->getList();
			
			// make sure any special days exist
			if ($sdList)
			{
				$overwrite_closure = false;

				foreach ($sdList as $sd)
				{
					// in case of closure, make sure the special day can overwrite it
					if (!$closed || $sd->ignoreClosingDays)
					{
						if ($sd->ignoreClosingDays)
						{
							// special day can overwrite closure
							$overwrite_closure = true;
						}

						// if we need to get only the choosable menus,
						// make sure the special day allows their selection
						if (!$choosable || $sd->chooseMenu)
						{
							// get available menus
							$ids = array_merge($ids, $sd->menus);

							// all the special days found must allow the freedom of choice
							$freedom = $freedom && $sd->choiceFreedom;
						}
					}
				}

				if (!$ids)
				{
					// no selected menus
					return [];
				}

				if ($overwrite_closure)
				{
					// overwrite closure
					$closed = false;
				}

				// avoid duplicates
				$ids = array_unique($ids);
			}
		}

		if ($closed)
		{
			// restaurant closed, return empty list
			return [];
		}
		
		$dbo = JFactory::getDbo();

		$menus = [];

		// recover menus
		$q = $dbo->getQuery(true)
			->select('*')
			->select((int) $freedom . ' AS ' . $dbo->qn('freechoose'))
			->from($dbo->qn('#__vikrestaurants_menus'))
			->where($dbo->qn('published') . ' = 1')
			->order($dbo->qn('ordering') . ' ASC');

		if ($ids)
		{
			// take only the menus fetched by the special day
			$q->where($dbo->qn('id') . ' IN (' . implode(',', $ids) . ')');
		}

		if ($choosable)
		{
			// take only the menus that can be chosen
			$q->where($dbo->qn('choosable') . ' = 1');
		}
		
		$dbo->setQuery($q);
		$menus = $dbo->loadObjectList();

		if (!$menus)
		{
			// no menus found
			return [];
		}

		if ($sdList || empty($args['date']))
		{
			// directly return menus in case of special day
			// or in case we should not filter them
			return $menus;
		}

		$list = array();

		if (!empty($args['hourmin']))
		{
			// extract hours and minutes
			list($args['hour'], $args['min']) = explode(':', $args['hourmin']);
		}
		else
		{
			// use midnight in case the time was not specified
			$args['hour'] = $args['min'] = 0;
		}

		// calculate checkin week day
		$weekday = date('w', VikRestaurants::createTimestamp($args['date'], $args['hour'], $args['min']));
		// calculate time in minutes
		$time = (int) $args['hour'] * 60 + (int) $args['min'];

		// validate each menu against the selected date and time
		foreach ($menus as $m)
		{
			// make sure the menu is not published for special days only
			$ok = !$m->special_day;

			if (strlen((string) $m->days_filter))
			{
				// split days
				$days = preg_split("/,\s*/", $m->days_filter);
	
				// make sure the checkin day is supported
				$ok = $ok && in_array($weekday, $days);
			}

			if (!empty($args['hourmin']) && !empty($m->working_shifts))
			{
				// split shifts
				$shifts = preg_split("/,\s*/", $m->working_shifts);
	
				$has = false;

				// iterate shifts
				for ($i = 0; $i < count($shifts) && $has == false; $i++)
				{
					// from shift ID to time
					$sh = JHtml::fetch('vikrestaurants.timeofshift', (int) $shifts[$i]);

					if ($sh && $sh->from <= $time && $time <= $sh->to)
					{
						$has = true;
					}
				}

				$ok = $ok && $has;
			}

			if ($ok)
			{
				// menu is ok, copy it
				$list[] = $m;
			}
		}

		return $list;
	}

	/**
	 * Checks whether the customers can choose the menus for the party
	 * in the specified date and time.
	 * This method ignores the closing days.
	 *
	 * @param 	array 	 $args  The searched arguments.
	 *
	 * @return 	boolean  True if choosable, false otherwise.
	 *
	 * @since 	1.5
	 */
	public static function isMenusChoosable($args)
	{
		// instantiate special days manager
		$sdManager = new VRESpecialDaysManager('restaurant');

		// set date filter
		$sdManager->setStartDate($args['date']);
		// set time filter
		$sdManager->setCheckinTime($args['hourmin']);

		// get list of available special days
		$sdList = $sdManager->getList();
		
		if ($sdList)
		{
			foreach ($sdList as $sd)
			{
				// checks whether it is possible to choose menus
				// with the configuration of the special day found
				if ($sd->chooseMenu)
				{
					return true;
				}
			}

			// none of the available special days allows
			// the menus selection
			return false;
		}

		// fallback to global configuration
		return VREFactory::getConfig()->getBool('choosemenu');
	}
	
	/**
	 * Returns a list of take-away menus available for
	 * the specified date and time.
	 * In case the returned list is empty, no menus are 
	 * available for the given check-in.
	 *
	 * @param 	array 	$args  An associative array containing the date and time.
	 *
	 * @return 	array   A list of available menus.
	 *
	 * @since 	1.6
	 */
	public static function getAllTakeawayMenusOn($args)
	{
		/**
		 * Manipulate $args in order to use the closest time
		 * in case we passed an invalid time.
		 *
		 * @since 1.7.5
		 */
		if (empty($args['hourmin']))
		{
			/**
			 * Attempt to extract the time from the date string.
			 * 
			 * @since 1.8.6
			 */
			if (preg_match("/\s+[0-9]{1,2}:[0-9]{1,2}/", $args['date']))
			{
				// extract time from date string
				list($args['date'], $args['hourmin']) = explode(' ', $args['date']);
			}
			else
			{
				// always get a time in the future
				$args['hourmin'] = self::getClosestTimeTakeAway($args['date'], $next = true);
			}

			if (!$args['hourmin'])
			{
				// unable to find a valid time for the given date
				return [];
			}
		}

		// Check if we have a closure. In case the date was
		// not passed, the system will ignore the closure.
		$closed = self::isClosingDayIgnoringDate($args);

		$ids = [];

		$sdList = null;

		// instantiate special days manager
		$sdManager = new VRESpecialDaysManager('takeaway');

		// set date filter
		$sdManager->setStartDate($args['date']);

		// set time filter
		$sdManager->setCheckinTime($args['hourmin']);

		// get list of available special days
		$sdList = $sdManager->getList();
		
		// make sure any special days exist
		if ($sdList)
		{
			$overwrite_closure = false;

			foreach ($sdList as $sd)
			{
				// in case of closure, make sure the special day can overwrite it
				if (!$closed || $sd->ignoreClosingDays)
				{
					if ($sd->ignoreClosingDays)
					{
						// special day can overwrite closure
						$overwrite_closure = true;
					}

					// get available menus
					$ids = array_merge($ids, $sd->menus);
				}
			}

			if (!$ids)
			{
				// no selected menus
				return [];
			}

			if ($overwrite_closure)
			{
				// overwrite closure
				$closed = false;
			}

			// avoid duplicates
			$ids = array_unique($ids);
		}

		if ($closed)
		{
			// restaurant closed, return empty list
			return [];
		}

		if (!$ids)
		{
			$dbo = JFactory::getDbo();

			// get all published menus
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id'))
				->from($dbo->qn('#__vikrestaurants_takeaway_menus'))
				->where($dbo->qn('published') . ' = 1');

			list($h, $m) = explode(':', $args['hourmin']);

			if (is_int($args['date']))
			{
				// set hour and minutes to received timestamp
				$checkin = strtotime($h . ":" . $m, $args['date']);
			}
			else
			{
				// calculate check-in date time
				$checkin = VikRestaurants::createTimestamp($args['date'], $h, $m);
			}

			/**
			 * Take all the menus with a valid/empty start publishing.
			 *
			 * @since 1.8.3
			 */
			$q->andWhere([
				$dbo->qn('start_publishing') . ' = -1',
				$dbo->qn('start_publishing') . ' IS NULL',
				$dbo->qn('start_publishing') . ' <= ' . $checkin, 
			], 'OR');

			/**
			 * Take all the menus with a valid/empty finish publishing.
			 *
			 * @since 1.8.3
			 */
			$q->andWhere([
				$dbo->qn('end_publishing') . ' = -1',
				$dbo->qn('end_publishing') . ' IS NULL',
				$dbo->qn('end_publishing') . ' >= ' . $checkin, 
			], 'OR');

			$dbo->setQuery($q);
			$ids = $dbo->loadColumn();
		}

		return $ids;
	}

	/**
	 * Checks whether the menus in the list are available
	 * for the purchase.
	 *
	 * @param 	array 	 &$menus 	 The menus list.
	 * @param 	integer  $checkin    The check-in timestamp.
	 * @param 	mixed 	 $available  A list of available menus.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 */
	public static function fetchMenusStatus(&$menus, $checkin = null, $available = null)
	{
		$config = VREFactory::getConfig();

		if (is_null($checkin))
		{
			// recover cart instance
			$cart = E4J\VikRestaurants\TakeAway\Cart::getInstance();

			// use check-in stored in cart
			$checkin = $cart->getCheckinTimestamp();
		}

		/**
		 * Convert UNIX timestamp to date string, in order
		 * to exclude the time.
		 *
		 * @since 1.8.2
		 */
		if (preg_match("/^\d+$/", $checkin))
		{
			$checkin = date($config->get('dateformat'), $checkin);
		}

		if (is_null($available))
		{
			// get all take-away menus available for the specified date
			$available = self::getAllTakeawayMenusOn(array('date' => $checkin));
		}

		// check whether the date selection is allowed
		$is_date_allowed = $config->getBool('tkallowdate');
		// in case the date selection is disabled, check whether pre-orders are enabled
		$is_live_orders = $is_date_allowed ? false : $config->getBool('tkwhenopen');
		// in case the pre-orders are disabled, check whether the restaurant is currently open
		$is_currently_avail = !$is_live_orders ? true : self::isTakeAwayCurrentlyAvailable();

		// check whether take-away orders are currently allowed
		$orders_allowed = self::isTakeAwayReservationsAllowedOn($checkin);

		if (!is_array($menus))
		{
			// always use an array
			$menus = array($menus);
			// remember that the argument was NOT an array
			$was_array = false;
		}
		else
		{
			// remember that the argument was already an array
			$was_array = true;
		}

		foreach ($menus as &$menu)
		{
			// check whether the menu products are available for purchase
			$menu->isActive = $orders_allowed && $is_currently_avail && in_array($menu->id, $available);

			if ($menu->isActive == false)
			{
				// menu not active, fetch reason
				if (!$orders_allowed)
				{
					// orders have been stopped for the current day (from dashboard)
					$menu->availError = JText::translate('VRTKMENUNOTAVAILABLE3');
				}
				else if ($is_currently_avail)
				{
					// since the restaurant is open, the menu is not available
					// for the selected check-in date
					$menu->availError = JText::translate('VRTKMENUNOTAVAILABLE'); 
				}
				else
				{
					// restaurant is currently closed
					$menu->availError = JText::translate('VRTKMENUNOTAVAILABLE2'); 
				}
			}
		}

		if (!$was_array)
		{
			// revert to original value
			$menus = array_shift($menus);
		}
	}

	/**
	 * Checks whether there is a closing day for the given information.
	 * In case the array contains the date and it is equals to "-1", 
	 * the day will never be considered as closed.
	 *
	 * @param 	array 	 $args  The date information array.
	 *
	 * @return 	boolean  True if closing day, false otherwise.
	 *
	 * @see 	isClosingDay()
	 */
	public static function isClosingDayIgnoringDate(array $args)
	{
		if (empty($args['date']) || $args['date'] == -1)
		{
			return false;
		}
		
		return self::isClosingDay($args);
	}
	
	/**
	 * Checks whether there is a closing day for the given information.
	 *
	 * @param 	mixed   $args  Either an array containing the date information
	 * 						   or a UNIX timestamp. If not specified, the current
	 * 						   date and time will be used. In case of array, it is
	 * 						   possible to use the following attributes:
	 * 						   - date  a system-formatted date (mandatory);
	 *						   - hour  an hour in 24h format (optional);
	 * 						   - min   a minute (optional).
	 *
	 * @return 	boolan  True if closing day, false otherwise.
	 */
	public static function isClosingDay($args = array())
	{
		// get closing days
		$cd = self::getClosingDays();

		if (isset($args['date']))
		{
			if (!empty($args['hourmin']))
			{
				// extract hour and min
				list($hour, $min) = explode(':', $args['hourmin']);
			}
			else
			{
				// look for hour and min
				$hour = isset($args['hour']) ? $args['hour'] : 0;
				$min  = isset($args['min'])  ? $args['min']  : 0;
			}

			if ($hour == -1)
			{
				$hour = 0;
			}
			
			if (is_numeric($args['date']))
			{
				/**
				 * Set time to given timestamp.
				 *
				 * @since 1.8
				 */
				$ts = strtotime($hour . ':' . $min, $args['date']);
			}
			else
			{
				// create timestamp
				$ts = self::createTimestamp($args['date'], $hour, $min);
			}
		}
		else if (is_numeric($args))
		{
			/**
			 * Use the given timestamp.
			 *
			 * @since 1.8
			 */
			$ts = (int) $args;
		}
		else if (is_string($args))
		{
			/**
			 * Create timestamp from given date string.
			 *
			 * @since 1.8
			 */
			$ts = VikRestaurants::createTimestamp($args, 0, 0);
		}
		else
		{
			/**
			 * Use current date and time if not specified.
			 *
			 * @since 1.8
			 */
			$ts = self::now();
		}

		// get date information
		$date = getdate($ts);
		
		// iterate closing days
		foreach ($cd as $v)
		{
			// get closing date information
			$app = getdate($v['ts']);
			
			if ($v['freq'] == 0)
			{
				// no recurrence, make sure the day is exactly the same
				if ($date['year'] == $app['year'] && $date['mon'] == $app['mon'] && $date['mday'] == $app['mday'])
				{
					return true;
				}
			}
			else if ($v['freq'] == 1)
			{
				// weekly recurrence, make sure the day of the week is the same
				if ($date['wday'] == $app['wday'])
				{
					return true;
				}
			}
			else if ($v['freq'] == 2)
			{
				// monthly recurrence, make sure the day of the month is the same
				if ($date['mday'] == $app['mday'])
				{
					return true;
				}
			}
			else if ($v['freq'] == 3)
			{
				// yearly recurrence, make sure the day and the month are the same
				if ($date['mday'] == $app['mday'] && $date['mon'] == $app['mon'])
				{
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Checks whether it is possible to purchase products
	 * at the current date and time. This method should
	 * be used only in case the "Live Orders" setting
	 * is turned on.
	 *
	 * @return 	boolean
	 *
	 * @since 	1.7
	 */
	public static function isTakeAwayCurrentlyAvailable()
	{
		/**
		 * Consider current real time.
		 *
		 * @since 1.7.4
		 */	
		$date = getdate(self::now());

		$args = array(
			'date'    => $date[0],
			'hourmin' => (int) $date['hours'] . ':' . (int) $date['minutes'],
		);

		// Make sure the current date and time is included
		// within a valid working shift. Use non-strict method
		// in order to make sure the time is between the shift
		// opening-closing delimiters.
		return static::isHourBetweenShifts($args, 2, $strict = false);
	}

	/**
	 * Checks whether the specified product owns at least
	 * a topping group.
	 *
	 * @param 	integer  $id_entry   The product ID.
	 * @param 	integer  $id_option  The variation ID (optional).
	 *
	 * @return 	boolean  True if supports toppings, false otherwise.
	 *
	 * @since 	1.7
	 */
	public static function hasItemToppings($id_entry, $id_option = 0)
	{
		static $lookup = null;

		$id_entry  = (int) $id_entry;
		$id_option = (int) $id_option;

		// check if we already cached the toppings
		if ($lookup === null)
		{
			$lookup = array();
			
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_entry'))
				->select($dbo->qn('id_variation'))
				->from($dbo->qn('#__vikrestaurants_takeaway_entry_group_assoc'));

			$dbo->setQuery($q);
			foreach ($dbo->loadObjectList() as $group)
			{
				if (!isset($lookup[$group->id_entry]))
				{
					$lookup[$group->id_entry] = [];
				}

				// add variation only if not already exists
				if (!in_array($group->id_variation, $lookup[$group->id_entry]))
				{
					$lookup[$group->id_entry][] = $group->id_variation == -1 ? 0 : $group->id_variation;
				}
			}
		}

		if (!isset($lookup[$id_entry]))
		{
			// no toppings available for the given product
			return false;
		}

		// search for a topping group available for all the variations
		if (in_array(0, $lookup[$id_entry]))
		{
			return true;
		}

		// otherwise search for a group assigned only to the specified variation
		return $id_option > 0 && in_array($id_option, $lookup[$id_entry]);
	}

	/**
	 * Returns the media upload settings.
	 *
	 * @return 	array
	 */
	public static function getMediaProperties()
	{
		$prop = VREFactory::getConfig()->getArray('mediaprop', null);

		if (!$prop)
		{
			$prop = array(
				'resize' 		=> 0,
				'resize_value' 	=> 512,				
				'thumb_value' 	=> 128,
			);
		}

		return $prop;
	}

	/**
	 * Updates the media upload settings.
	 *
	 * @param  	array 	&$prop
	 *
	 * @return 	void
	 */
	public static function storeMediaProperties(&$prop)
	{
		// get default media properties
		$defaults = static::getMediaProperties();

		// inject provided properties within the existing ones
		$prop = array_merge($defaults, $prop);

		// commit changes
		$config = VREFactory::getConfig();
		$config->set('mediaprop', $prop);
		$config->set('firstmediaconfig', 0);
	}

	/**
	 * Uploads a media file.
	 *
	 * @param 	string 	 $name       The media name.
	 * @param 	mixed 	 $prop       The upload settings.
	 * @param 	boolean  $overwrite  True to overwrite the existing media.
	 *
	 * @return 	array 	 A response.
	 *
	 * @uses 	uploadFile()
	 */
	public static function uploadMedia($name, $prop = null, $overwrite = false)
	{
		$model = JModelVRE::getInstance('media');

		// upload as a normal file
		$resp = self::uploadFile($name, VREMEDIA . DIRECTORY_SEPARATOR, $model->getFileAllowedRegex('image'), $overwrite);

		// import image cropper
		VRELoader::import('library.image.resizer');

		if ($resp->status)
		{
			if ($prop === null)
			{
				// get media settings if not specified
				$prop = self::getMediaProperties();
			}
			
			if ($prop['resize'] == 1)
			{	
				// crop original image
				$crop_dest = str_replace($resp->name, '$_' . $resp->name, $resp->path);
				
				ImageResizer::proportionalImage($resp->path,  $crop_dest, $prop['resize_value'], $prop['resize_value']);
				copy($crop_dest, $resp->path);
				unlink($crop_dest);
			}

			// generate thumbnail
			$thumb_dest = VREMEDIA_SMALL . DIRECTORY_SEPARATOR . $resp->name;
			ImageResizer::proportionalImage($resp->path, $thumb_dest,  $prop['thumb_value'],  $prop['thumb_value']);
		}

		return $resp;
	}

	/**
	 * Moves the given file within the specified destination.
	 *
	 * @param 	mixed 	 $name       Either the file object or the $_FILES name in
	 *                               which the file is located.
	 * @param 	string 	 $dest       The path (including filename) in which to move the uploaded file.
	 * @param 	string 	 $filters    Either a regex or a comma-separated list of supported extensions.
	 * @param 	boolean  $overwrite  True to overwrite the file if the destination is already occupied.
	 *                               Otherwise a progressive file name will be used.
	 *
	 * @return 	object 	 An object containing the information of the uploaded file. It is possible to
	 *                   check whether the file was uploaded by looking the "status" property. In case of
	 *                   errors, the "errno" property will return an error code to understand why the error
	 *                   occurred (1: unsupported file, 2: generic upload error).
	 */
	public static function uploadFile($name, $dest, $filters = '*', $overwrite = false)
	{
		if (is_string($name))
		{
			/**
			 * Use "raw" instead of "array" to avoid filtering the files upload.
			 * 
			 * @since 1.9 
			 */
			$file = JFactory::getApplication()->input->files->get($name, null, 'raw');
		}
		else
		{
			$file = (array) $name;
		}

		/**
		 * Check whether the destination path includes the file name or
		 * just the upload directory.
		 *
		 * @since 1.9
		 */
		if (preg_match("/\.[a-zA-Z0-9]+$/", $dest) && !is_dir($dest))
		{
			// We found a path ending with a probable extension and
			// the destination path is not a directory.
			// Extract the filename from the destination path.
			$filename = basename($dest);
			// remove file name from destination
			$dest = dirname($dest);
		}
		else
		{
			// otherwise use the file name of the uploaded file
			$filename = isset($file['name']) ? $file['name'] : null;
		}

		$dest = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		
		/**
		 * Added support for status property.
		 * The [esit] property will be temporarily
		 * left for backward compatibility.
		 *
		 * @since 1.8
		 */
		$obj = new stdClass;
		$obj->status = 0;
		$obj->errno  = null;
		$obj->path   = '';
		
		if (isset($file) && strlen(trim($file['name'])) > 0)
		{
			jimport('joomla.filesystem.file');

			$filename = JFile::makeSafe(str_replace(' ', '-', $file['name']));
			$src = $file['tmp_name'];

			// use a different name if the file path is already occupied
			if (!$overwrite && file_exists($dest . $filename))
			{
				$j = 2;

				// split file name and file extension
				if (preg_match("/(.*?)(\.[a-z0-9]{2,})/i", $filename, $match))
				{
					$basename = $match[1];
					$file_ext = $match[2];
				}
				else
				{
					$basename = $filename;
					$file_ext = '';
				}

				// increase counter as long as the path is occupied
				while (file_exists($dest . $basename . '-' . $j . $file_ext))
				{
					$j++;
				}

				// construct file name
				$filename = $basename . '-' . $j . $file_ext;
			}

			// create file object
			$obj->path = $dest . $filename;
			$obj->src  = $src;
			$obj->name = $filename;

			// make sure the file is compatible
			if (self::isFileTypeCompatible($filename, $filters))
			{
				// complete file upload
				if (JFile::upload($src, $obj->path, $use_streams = false, $allow_unsafe = true))
				{
					$obj->status = 1;
				}
				else
				{
					// unable to upload the file
					$obj->errno = 2;
				}
			}
			else
			{
				// file not supported
				$obj->errno = 1;
				// include fetched MIME type
				$obj->mimeType = $file['type'];
			}
		}

		return $obj;
	}

	/**
	 * Helper method used to check whether the given file name
	 * supports one of the given filters.
	 *
	 * @param 	mixed 	 $file     Either the file name or the uploaded file.
	 * @param 	string 	 $filters  Either a regex or a comma-separated list of supported extensions.
	 *                             The regex must be inclusive of 
	 *
	 * @return 	boolean  True if supported, false otherwise.
	 */
	public static function isFileTypeCompatible($file, $filters)
	{
		// make sure the filters query is not empty
		if (strlen($filters) == 0)
		{
			// cannot assert whether the file could be accepted or not
			return false;
		}

		// check whether all the files are accepted
		if ($filters == '*')
		{
			return true;
		}

		// use the file MIME TYPE in case of array
		if (is_array($file))
		{
			$file = $file['type'];
		}

		/**
		 * Check if we are handling a regex.
		 *
		 * @since 1.8
		 */
		if (static::isRegex($filters))
		{
			return (bool) preg_match($filters, $file);
		}
		
		// fallback to comma-separated list
		$types = array_filter(preg_split("/\s*,\s*/", $filters));

		foreach ($types as $t)
		{
			// remove initial dot if specified
			$t = ltrim($t, '.');
			// escape slashes to avoid breaking the regex
			$t = preg_replace("/\//", '\/', $t);

			// check if the file ends with the given extension
			if (preg_match("/{$t}$/i", $file))
			{
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Checks whether the given string is a structured PCRE regex.
	 * It simply makes sure that the string owns valid delimiters.
	 * A delimiter can be any non-alphanumeric, non-backslash,
	 * non-whitespace character.
	 *
	 * @param 	string   $str  The string to check.
	 *
	 * @return 	boolean  True if a regex, false otherwise.
	 *
	 * @since 	1.8
	 */
	public static function isRegex($str)
	{
		// first of all make sure the first character is a supported delimiter
		if (!preg_match("/^([!#$%&'*+,.\/:;=?@^_`|~\-(\[{<\"])/", $str, $match))
		{
			// no valid delimiter
			return false;
		}

		// get delimiter
		$d = $match[1];

		// lookup used to check if we should take a different ending delimiter
		$lookup = array(
			'{' => '}',
			'[' => ']',
			'(' => ')',
			'<' => '>',
		);

		if (isset($lookup[$d]))
		{
			$d = $lookup[$d];
		}

		// make sure the regex ends with the delimiter found
		return (bool) preg_match("/\\{$d}[gimsxU]*$/", $str);
	}

	/**
	 * Sets the status to REMOVED for all the take-away orders
	 * that haven't been confirmed within the specified range of time.
	 *
	 * @param 	mixed    $id     Either an array or the ID of the orders
	 * 							 to remove. If not specified, all the expired
	 * 							 orders will be taken.
	 *
	 * @return 	void
	 *
	 * @since 1.8
	 * @deprecated 1.10  Use VikRestaurantsModelTkreservation::checkExpired() instead.
	 */
	public static function removeTakeAwayOrdersOutOfTime($id = null)
	{
		JModelVRE::getInstance('tkreservation')->checkExpired([
			'id' => $id,
		]);
	}

	/**
	 * Sets the status to REMOVED for all the restaurant reservations
	 * that haven't been confirmed within the specified range of time.
	 *
	 * @param 	mixed    $id     Either an array or the ID of the reservations
	 * 							 to remove. If not specified, all the expired
	 * 							 reservations will be taken.
	 *
	 * @return 	void
	 *
	 * @since 1.8
	 * @deprecated 1.10  Use VikRestaurantsModelReservation::checkExpired() instead.
	 */
	public static function removeRestaurantReservationsOutOfTime($id = null)
	{
		JModelVRE::getInstance('reservation')->checkExpired([
			'id' => $id,
		]);
	}

	/**
	 * Sends a SMS notification to the customer/administrator according
	 * to the SMS APIs configuration settings.
	 *
	 * A notification e-mail is sent to the administrator in case the
	 * selected gateway fails while sending a message.
	 *
	 * @param 	string   $phone  The phone number of the customer. If not specified,
	 * 							 it will be recovered from the order detais.
	 * @param 	mixed 	 $order  Either the order details object or the order ID.
	 * @param 	integer  $group  The group to which the order belongs. Specify
	 * 							 0 for restaurant, 1 for take-away.
	 *
	 * @return 	boolean  True in case of successful notification, false otherwise.
	 *
	 * @since 	1.3
	 */
	public static function sendSmsAction($phone, $order, $group = 0)
	{
		$config = VREFactory::getConfig();

		/**
		 * Make sure the SMS can be sent for this group
		 * List of accepted statuses:
		 * - 0  Restaurant only;
		 * - 1  Take-Away only;
		 * - 2  Restaurant & Take-Away;
		 * - 3  Only manual.
		 */
		if (!in_array($config->getUint('smsapiwhen'), array($group, 2)))
		{
			// do not send automated messages for this group
			return false;
		}

		try
		{
			// get current SMS instance
			$smsapi = VREApplication::getInstance()->getSmsInstance();
		}
		catch (Exception $e)
		{
			// SMS framework not supported
			return false;
		}

		// retrieve order details in case an ID was passed
		if (is_scalar($order))
		{
			if ($group == 0)
			{
				// get restaurant reservation details
				$order = VREOrderFactory::getReservation($order);
			}
			else
			{
				// get take-away order details
				$order = VREOrderFactory::getOrder($order);
			}
		}

		if (!$order)
		{
			// invalid order
			return false;
		}

		$notified = 0;
		$errors   = array();
		$records  = array();

		// Make sure the customer can receive automated messages.
		// 0 for customer, 2 for customer & admin.
		if (in_array($config->getUint('smsapito'), array(0, 2)))
		{
			// get SMS notification message
			$message = VikRestaurants::getSmsCustomerTextMessage($order, $group);

			// missing phone number, try to use the one assigned to the order
			if (!$phone)
			{
				$phone = $order->purchase_phone;
			}

			if ($phone)
			{
				$records[] = array(
					'phone' => $phone,
					'text'  => $message,
				);
			}
		}

		// Make sure the administrator can receive automated messages.
		// 1 for admin, 2 for customer & admin.
		if (in_array($config->getUint('smsapito'), array(1, 2)))
		{
			// get SMS notification message
			$message = VikRestaurants::getSmsAdminTextMessage($order, $group);

			// get admin phone number
			$phone = $config->get('smsapiadminphone');

			if ($phone)
			{
				$records[] = array(
					'phone' => $phone,
					'text'  => $message,
				);
			}
		}

		// iterate messages to send
		foreach ($records as $sms)
		{
			// send message
			$response = $smsapi->sendMessage($sms['phone'], $sms['text']);

			// validate response
			if ($smsapi->validateResponse($response))
			{
				// successful notification
				$notified++;
			}
			else
			{
				// unable to send the notification, register error message
				$errors[] = $smsapi->getLog();
			}
		}

		if ($errors)
		{
			// send a notification e-mail to the administrator in case of error(s)
			self::sendAdminMailSmsFailed($errors);
		}
		
		return (bool) $notified;
	}

	/**
	 * Returns the notification message that should be sent via
	 * SMS to the customer of the given order/reservation.
	 *
	 * @param 	mixed    $order  The order/reservation details object.
	 * @param 	integer  $group  The section to notify (0: restaurant, 1: take-away).
	 *
	 * @return 	string   The parsed template message.
	 *
	 * @since 	1.3
	 */
	public static function getSmsCustomerTextMessage($order, $group = 0)
	{
		// use order lang tag
		$tag = $order->langtag;

		if (!$tag)
		{
			// no lang tag found, use the default one of the website
			$tag = self::getDefaultLanguage();
		}

		if ($group == 0)
		{
			// load content for restaurant reservation
			$setting = 'smstmplcust';
		}
		else
		{
			// load content for take-away order
			$setting = 'smstmpltkcust';
		}

		// get template from configuration
		$tmpl = [];
		$tmpl[$setting] = VREFactory::getConfig()->get($setting);

		/**
		 * Try to translate the SMS template.
		 *
		 * @since 1.9
		 */
		VikRestaurants::translateConfig($tmpl, $tag);

		if (empty(trim((string) $tmpl[$setting])))
		{
			/**
			 * Refresh language before obtaining the default template.
			 * 
			 * @since 1.9
			 */
			self::loadLanguage($tag);

			// fallback to default template
			if ($group == 0)
			{
				// restaurant template
				$sms = JText::translate('VRSMSMESSAGECUSTOMER');
			}
			else
			{
				// take-away template
				$sms = JText::translate('VRSMSMESSAGETKCUSTOMER');
			}
		}
		else
		{
			$sms = $tmpl[$setting];
		}

		// parse SMS template
		return self::parseContentSMS($order, $group, $sms);
	}

	/**
	 * Returns the notification message that should be sent via
	 * SMS to the administrator.
	 *
	 * @param 	mixed    $order  The order/reservation details object.
	 * @param 	integer  $group  The section to notify (0: restaurant, 1: take-away).
	 *
	 * @return 	string   The parsed template message.
	 *
	 * @since 	1.3
	 */
	public static function getSmsAdminTextMessage($order, $group = 0)
	{
		if ($group == 0)
		{
			// load content for restaurant reservation
			$setting = 'smstmpladmin';
		}
		else
		{
			// load content for take-away order
			$setting = 'smstmpltkadmin';
		}

		// get SMS template
		$sms = VREFactory::getConfig()->get($setting);
		
		if (empty($sms))
		{
			// fallback to default template
			if ($group == 0)
			{
				// restaurant template
				$sms = JText::translate('VRSMSMESSAGEADMIN');
			}
			else
			{
				// take-away template
				$sms = JText::translate('VRSMSMESSAGETKADMIN');
			}
		}
		
		// parse SMS template
		return self::parseContentSMS($order, $group, $sms);
	}
	
	/**
	 * Parses the SMS template to replace any placeholder with the related value.
	 *
	 * @param 	mixed    $order   The order/reservation details object.
	 * @param 	integer  $action  The section to notify (0: restaurant, 1: take-away).
	 * @param 	string   $sms     The template to parse.
	 *
	 * @return 	string   The parsed template message.
	 *
	 * @since 	1.3
	 */
	private static function parseContentSMS($order, $action = 0, $sms = '')
	{
		$config   = VREFactory::getConfig();
		$currency = VREFactory::getCurrency();

		$checkin_date_time  = date($config->get('dateformat') . ' ' . $config->get('timeformat'), $order->checkin_ts);
		$creation_date_time = date($config->get('dateformat') . ' ' . $config->get('timeformat'), $order->created_on);

		if ($action == 0)
		{
			// restaurant
			$sms = str_replace('{total_cost}', $currency->format($order->deposit), $sms);
			$sms = str_replace('{people}'    , $order->people                    , $sms);
		}
		else
		{
			// take-away
			$sms = str_replace('{total_cost}', $currency->format($order->total_to_pay), $sms);
		}

		// commons
		$sms = str_replace('{checkin}'   , $checkin_date_time          , $sms);
		$sms = str_replace('{created_on}', $creation_date_time         , $sms);
		$sms = str_replace('{company}'   , $config->get('restname')    , $sms);
		$sms = str_replace('{customer}'  , $order->purchaser_nominative, $sms);
		
		return $sms;
	}
	
	/**
	 * Sends a notification e-mail to the administrator(s) every
	 * time an error occurs while sending a SMS.
	 *
	 * @param 	mixed 	 $text   Either an array of messages or a string.
	 *
	 * @return 	boolean  True in case the notification was sent, false otherwise.
	 *
	 * @since 	1.3
	 */
	public static function sendAdminMailSmsFailed($text)
	{
		if (is_array($text))
		{
			// join messages, separated by an empty line
			$text = implode('<br /><br />', $text);
		}

		$config = VREFactory::getConfig();

		// get administrators e-mail
		$adminmails = self::getAdminMailList();
		// get sender e-mail address
		$sendermail = self::getSenderMail();
		// get restaurant name
		$fromname = $config->getString('restname');
		
		// fetch e-mail subject
		$subject = JText::sprintf('VRSMSFAILEDSUBJECT', $fromname);

		$vik = VREApplication::getInstance();

		$sent = false;
		
		// iterate e-mails to notify
		foreach ($adminmails as $recipient)
		{
			// send the e-mail notification
			$sent = $vik->sendMail($sendermail, $fromname, $recipient, $recipient, $subject, $text) || $sent;
		}

		return $sent;
	}

	/**
	 * Sends a notification e-mail to the administrator(s) every
	 * time an error occurs while trying to validate a payment.
	 *
	 * @param   int    $id    The order number (@since 1.9).
	 * @param   mixed  $text  Either an array of messages or a string.
	 *
	 * @return  bool   True in case the notification was sent, false otherwise.
	 *
	 * @since   1.8
	 */
	public static function sendAdminMailPaymentFailed($id, $text)
	{
		if (is_array($text))
		{
			// join messages, separated by an empty line
			$text = implode('<br /><br />', $text);
		}

		$config = VREFactory::getConfig();

		// get administrators e-mail
		$adminmails = self::getAdminMailList();
		// get sender e-mail address
		$sendermail = self::getSenderMail();
		// get restaurant name
		$fromname = $config->getString('restname');
		
		// fetch e-mail subject
		$subject = sprintf('Invalid Payment Received #%d - %s', $id, $fromname);

		$vik = VREApplication::getInstance();

		$sent = false;
		
		// iterate e-mails to notify
		foreach ($adminmails as $recipient)
		{
			// send the e-mail notification
			$sent = $vik->sendMail($sendermail, $fromname, $recipient, $recipient, $subject, $text) || $sent;
		}

		return $sent;
	}
	
	/**
	 * Helper method used to refresh the deals that should be
	 * applied to the cart.
	 *
	 * @param 	Cart  &$cart  The cart instance.
	 *
	 * @return 	boolean       True in case of deals, false otherwise.
	 *
	 * @since 	1.7
	 */
	public static function checkForDeals(&$cart)
	{
		// create deals handler
		$handler = new E4J\VikRestaurants\Deals\DealsHandler($cart);

		// get all deals available for the current date and time
		$deals = $handler->getAvailableDeals();

		// prepare deals before application
		$handler->setup();

		$applied = false;
		
		// apply deals only in case of active deals and added products
		if (count($deals) && count($cart))
		{
			// iterate deals
			foreach ($deals as $deal)
			{
				/**
				 * Let the deals handler applies the offer.
				 *
				 * @since 1.8
				 */
				$applied = $handler->serve($deal) || $applied;
			}
		}

		return $applied;
	}

	/**
	 * Resets the deals applied to the items within the cart.
	 * In addition checks whether there are some items that
	 * are no more available for the selected date and time.
	 *
	 * @param   Cart   $cart     The cart instance.
	 * @param   mixed  $hourmin  The optional check-in time.
	 *
	 * @return  void
	 *
	 * @since   1.7
	 */
	public static function resetDealsInCart($cart, $hourmin = null)
	{
		$config = VREFactory::getConfig();

		$filters = [];
		$filters['date'] = date($config->get('dateformat'), $cart->getCheckinTimestamp());

		/**
		 * Try to recover check-in time from cart.
		 *
		 * @since 1.8
		 */
		if (is_null($hourmin))
		{
			$hourmin = $cart->getCheckinTime();
		}

		/**
		 * If specified, consider also the time when
		 * fetching the available menus.
		 *
		 * @since 1.8
		 */
		if ($hourmin)
		{
			$filters['hourmin'] = $hourmin;
		}

		// get list of available menus
		$menus = self::getAllTakeawayMenusOn($filters);

		/** @var E4J\VikRestaurants\TakeAway\Cart\Deals */
		$deals = $cart->deals();

		// check if we have a coupon discount
		$couponIndex = $deals->indexOfType('coupon');

		if ($couponIndex !== false && $couponIndex >= 0)
		{
			$couponDiscount = $deals->get($couponIndex);
		}
		else
		{
			$couponDiscount = null;
		}
		
		$deals->clear();
		
		foreach ($cart->getItems() as $item)
		{
			// unset deal
			$item->setDealID(-1);
			$item->setPrice($item->getOriginalPrice());
			$item->setDealQuantity(0);
			$item->setRemovable(true);
			
			if (!in_array($item->getMenuID(), $menus))
			{
				// the item is not more available, unset it
				$item->setQuantity(0);
			}
		}

		if ($couponDiscount)
		{
			// re-add coupon discount within the list of deals
			$deals->insert($couponDiscount);
		}
	}

	/**
	 * Calculate remaining availability in stock for the given product.
	 *
	 * @param 	integer  $eid 	 The product ID.
	 * @param 	integer  $oid 	 The variation ID (optional).
	 * @param 	integer  $index  The product of the database to ignore.
	 *
	 * @return 	integer  The remaining quantity (-1 if unlimited).
	 *
	 * @since 	1.7
	 */
	public static function getTakeawayItemRemainingInStock($eid, $oid = 0, $index = 0)
	{
		if (!VREFactory::getConfig()->getBool('tkenablestock'))
		{
			return -1;
		}

		$dbo = JFactory::getDbo();

		$eid = intval($eid);
		$oid = intval($oid);

		if ($oid > 0)
		{
			$q = $dbo->getQuery(true)
				->select($dbo->qn('stock_enabled'))
				->from($dbo->qn('#__vikrestaurants_takeaway_menus_entry_option'))
				->where($dbo->qn('id') . ' = ' . $oid);

			$dbo->setQuery($q, 0, 1);

			if (!$dbo->loadResult())
			{
				// unset option ID as the stock should refer to the parent item
				$oid = 0;
			}
		}

		$where = '';

		// get any reserved status codes
		$reserved = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => true, 'reserved' => 1]);
		
		if ($reserved)
		{
			// filter by reserved status
			$where .= " AND " . $dbo->qn('r.status') . ' IN (' . implode(',', array_map(array($dbo, 'q'), $reserved)) . ')';
		}
		
		if ($index > 0)
		{
			// exclude item stored in database
			$where .= " AND `i`.`id` <> " . intval($index);
		}

		// build query used to retrieve items with low stocks
		$q = "SELECT
			IF(
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, 
				(
					IFNULL(
						(
							SELECT SUM(`so`.`items_available`) 
							FROM `#__vikrestaurants_takeaway_stock_override` AS `so` 
							WHERE `so`.`id_takeaway_entry` = `e`.`id` AND `so`.`id_takeaway_option` IS NULL
						), `e`.`items_in_stock`
					)
				), (
					IFNULL(
						(
							SELECT SUM(`so`.`items_available`) 
							FROM `#__vikrestaurants_takeaway_stock_override` AS `so` 
							WHERE `so`.`id_takeaway_entry` = `e`.`id` AND `so`.`id_takeaway_option` = `o`.`id`
						), `o`.`items_in_stock`
					)
				)
			) AS `products_in_stock`,

			IF(
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, 
				(
					IFNULL(
						(
							SELECT SUM(`i`.`quantity`)
							FROM `#__vikrestaurants_takeaway_reservation` AS `r` 
							LEFT JOIN `#__vikrestaurants_takeaway_res_prod_assoc` AS `i` ON `i`.`id_res` = `r`.`id`
							LEFT JOIN `#__vikrestaurants_takeaway_menus_entry_option` AS `io` ON `i`.`id_product_option` = `io`.`id`
							WHERE `i`.`id_product` = `e`.`id`
							AND (`o`.`id` IS NULL OR `io`.`stock_enabled` = 0)
							{$where}
						), 0
					)
				), (
					IFNULL(
						(
							SELECT SUM(`i`.`quantity`)
							FROM `#__vikrestaurants_takeaway_reservation` AS `r` 
							LEFT JOIN `#__vikrestaurants_takeaway_res_prod_assoc` AS `i` ON `i`.`id_res` = `r`.`id`
							WHERE `i`.`id_product` = `e`.`id` AND `i`.`id_product_option` = `o`.`id`
							{$where}
						), 0
					)
				)
			) AS `products_used`

			FROM
				`#__vikrestaurants_takeaway_menus_entry` AS `e`
			LEFT JOIN
				`#__vikrestaurants_takeaway_menus_entry_option` AS `o` ON `e`.`id` = `o`.`id_takeaway_menu_entry`
			LEFT JOIN
				`#__vikrestaurants_takeaway_menus` AS `m` ON `m`.`id` = `e`.`id_takeaway_menu` 
			WHERE
				`e`.`id` = {$eid}";

		if ($oid > 0)
		{
			$q .= " AND `o`.`id` = {$oid}";
		}
		else
		{
			// do not take option with self stock
			$q .= " AND (`o`.`id` IS NULL OR `o`.`stock_enabled` = 0)";
		}

		$dbo->setQuery($q, 0, 1);
		$row = $dbo->loadObject();

		if ($row)
		{
			return (int) $row->products_in_stock - (int) $row->products_used;
		}

		return 0;
	}

	/**
	 * Checks whether all the ordered products are still
	 * available for the purchase. In case the stock of one
	 * or more items is no more available, an error message
	 * will be enqueued and the missing units will be removed
	 * from the cart instance.
	 *
	 * @param   Cart   $cart    The current user cart.
	 * @param   mixed  $errors  The array where the errors will be injected (@since 1.9).
	 *
	 * @return  bool   False in case something has been removed from the cart.
	 *
	 * @since   1.7
	 */
	public static function checkCartStockAvailability($cart, array &$errors = null)
	{
		if ($errors === null)
		{
			$errors = [];
		}

		if (!VREFactory::getConfig()->getBool('tkenablestock'))
		{
			// do not go ahead in case the stock system is disabled
			return true;
		}

		$ok = true;

		// iterate ordered items
		foreach ($cart->getItems() as $item)
		{
			// find remaining units of the current item/variation
			$in_stock = self::getTakeawayItemRemainingInStock($item->getItemID(), $item->getOptionID(), -1);

			// get item/variation ordered units
			$stock_item_quantity = $cart->getQuantityItems($item->getItemID(), $item->getOptionID());
		
			if ($in_stock - $stock_item_quantity < 0)
			{
				// there are not enough units available, remove missing
				// ones from the user cart
				$removed_items = $stock_item_quantity - $in_stock;
				$item->remove($removed_items);

				if ($stock_item_quantity == $removed_items)
				{
					// no more items, all the units have been removed from the cart
					$errors[] = JText::sprintf('VRTKSTOCKNOITEMS', $item->getName());
				}
				else
				{
					// only some units have been removed from the cart
					$errors[] = JText::sprintf('VRTKSTOCKREMOVEDITEMS', $item->getName(), $removed_items);
				}

				$ok = false;
			}
		}

		return $ok;
	}

	/**
	 * Returns a full address string based on the specified delivery details.
	 * The address is built by following this structure:
	 * [ADDRESS] [ADDRESS_2], [ZIP] [CITY] [STATE], [COUNTRY]
	 *
	 * @param 	mixed 	$address   Either an object or an array containing the address details.
	 * @param 	array 	$excluded  A list of properties to exclude while creating the address.
	 *
	 * @return 	string 	The full address.
	 *
	 * @since 	1.7
	 */
	public static function deliveryAddressToStr($address, array $excluded = [])
	{
		// always treat the address as an array
		$address = (array) $address;

		$str = array();

		// route + street number
		$app = array();

		if (!empty($address['address']) && !in_array('address', $excluded))
		{
			$app[] = trim($address['address']);
		}

		// info address
		if (!empty($address['address_2']) && !in_array('address_2', $excluded))
		{
			$app[] = trim($address['address_2']);
		}

		// insert first block
		if ($app)
		{
			$str[] = implode(' ', $app);
		}

		// zip
		$app = array();

		if (!empty($address['zip']) && !in_array('zip', $excluded))
		{
			$app[] = trim($address['zip']);
		}

		// city
		if (!empty($address['city']) && !in_array('city', $excluded))
		{
			$app[] = trim($address['city']);
		}

		// state
		if (!empty($address['state']) && !in_array('state', $excluded))
		{
			$app[] = trim($address['state']);
		}

		// insert second block
		if ($app)
		{
			$str[] = implode(' ', $app);
		}

		// country name or country code
		if (!empty($address['country']) && !in_array('country', $excluded))
		{
			if (!empty($address['country_name']))
			{
				$str[] = $address['country_name'];
			}
			else if (!empty($address['countryName']))
			{
				$str[] = $address['countryName'];
			}
			else
			{
				$str[] = $address['country'];
			}
		}

		// join fetched address parts
		return implode(', ', $str);
	}

	/**
	 * Compares 2 addresses to check if they are equals.
	 *
	 * @param 	array 	 $addr 	The associative array containing the
	 * 							address details fetched by VikRestaurants.
	 * @param 	array 	 $resp 	The associative array containing the
	 * 							address details fetched by Google.
	 *
	 * @return 	boolean  True if equals, otherwise false.
	 *
	 * @since 	1.7.4
	 */
	public static function compareAddresses($addr, E4J\VikRestaurants\DeliveryArea\DeliveryQuery $query)
	{
		/**
		 * When specified, try to calculate the distance between
		 * the coordinates and evaluate whether they are so close
		 * to be considered the same address.
		 *
		 * @since 1.8.3
		 */
		if (!empty($addr['latitude']) && !empty($addr['longitude']) && ($queryLatLng = $query->getCoordinates()))
		{
			// check whether the 2 points have a distance equals or lower
			// than a meter, which means that we have the same address at
			// least for 99% of the times 
			$intersect = E4J\VikRestaurants\Graphics2D\GeometryHelper::isPointInsideCircleOnEarth(
				// create a circle with center at the address latitude and longitude
				// and with a radius of 1 meter
				new E4J\VikRestaurants\Graphics2D\Circle2D(0.001, $addr['latitude'], $addr['longitude']),
				// create a point with coordinates equals to the searched address
				new E4J\VikRestaurants\Graphics2D\Point($queryLatLng->latitude, $queryLatLng->longitude)
			);

			if ($intersect)
			{
				// we have the same address
				return true;
			}
		}

		// create extended string from the typed address information
		$addrFullAddress = implode(' ', array_filter([
			$addr['country'] ?? '',
			$addr['state']   ?? '',
			$addr['city']    ?? '',
			$addr['zip']     ?? '',
			$addr['address'] ?? '',
		]));

		// create extended string from the searched query
		$queryFullAddress = implode(' ', array_filter([
			$query->getComponent('country'),
			$query->getComponent('state'),
			$query->getCity(),
			$query->getZipCode(),
			// The address may be returned as an array of "route" and "street number".
			// Therefore we should always treat the value as an array and implode the
			// contained information
			implode(' ', (array) $query->getAddress()),
		]));

		// get rid of commas and duplicate spaces
		$addrFullAddress  = preg_replace("/,/", '', $addrFullAddress);
		$addrFullAddress  = preg_replace("/[\s]{2,}/", ' ', $addrFullAddress);
		$queryFullAddress = preg_replace("/,/", '', $queryFullAddress);
		$queryFullAddress = preg_replace("/[\s]{2,}/", ' ', $queryFullAddress);

		return strcasecmp($addrFullAddress, $queryFullAddress) === 0;
	}

	/**
	 * Returns the details of the given customer.
	 *
	 * @param 	mixed  $id  The customer ID. If not specified,
	 * 						the customer assigned to the current
	 * 						user will be retrieved, if any.
	 *
	 * @return 	mixed  The customer object if exists, NULL otherwise
	 *
	 * @since 	1.4
	 */
	public static function getCustomer($id = null)
	{
		$jid = null;

		if (is_null($id))
		{
			// get current CMS user
			$user = JFactory::getUser();

			// make sure the user is not a guest
			if ($user->guest)
			{
				return null;
			}

			// get CMS user ID
			$jid = $user->id;
		}
		else
		{
			$id = (int) $id;
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);
		
		// get customer details
		$q->select('c.*');
		$q->from($dbo->qn('#__vikrestaurants_users', 'c'));

		// get billing country name
		$q->select($dbo->qn('country.country_name'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_countries', 'country') . ' ON ' . $dbo->qn('country.country_2_code') . ' = ' . $dbo->qn('c.country_code'));

		// get CMS user details
		$q->select($dbo->qn('u.name', 'user_name'));
		$q->select($dbo->qn('u.username', 'user_username'));
		$q->select($dbo->qn('u.email', 'user_email'));
		$q->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('c.jid'));

		// get delivery locations
		$q->select($dbo->qn('d.id', 'delivery_id'));
		$q->select($dbo->qn('d.country', 'delivery_country'));
		$q->select($dbo->qn('d.state', 'delivery_state'));
		$q->select($dbo->qn('d.city', 'delivery_city'));
		$q->select($dbo->qn('d.address', 'delivery_address'));
		$q->select($dbo->qn('d.address_2', 'delivery_address_2'));
		$q->select($dbo->qn('d.zip', 'delivery_zip'));
		$q->select($dbo->qn('d.latitude', 'delivery_latitude'));
		$q->select($dbo->qn('d.longitude', 'delivery_longitude'));
		$q->select($dbo->qn('d.type', 'delivery_type'));
		$q->select($dbo->qn('d.note', 'delivery_note'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_user_delivery', 'd') . ' ON ' . $dbo->qn('c.id') . ' = ' . $dbo->qn('d.id_user'));

		// get location country name
		$q->select($dbo->qn('country2.country_name', 'delivery_country_name'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_countries', 'country2') . ' ON ' . $dbo->qn('country2.country_2_code') . ' = ' . $dbo->qn('d.country'));

		if (is_null($id))
		{
			// get customer by CMS user
			$q->where($dbo->qn('u.id') . ' = ' . $jid);
		}
		else
		{
			// get customer by ID
			$q->where($dbo->qn('c.id') . ' = ' . $id);
		}

		$q->order($dbo->qn('d.ordering') . ' ASC');

		$dbo->setQuery($q);
		$app = $dbo->loadObjectList();

		if (!$app)
		{
			// no matching customers
			return null;
		}		

		$customer = new stdClass;
		$customer->id                = $app[0]->id;
		$customer->billing_name      = $app[0]->billing_name;
		$customer->billing_mail      = $app[0]->billing_mail;
		$customer->billing_phone     = $app[0]->billing_phone;
		$customer->country_code      = $app[0]->country_code;
		$customer->country           = $app[0]->country_name;
		$customer->billing_state     = $app[0]->billing_state;
		$customer->billing_city      = $app[0]->billing_city;
		$customer->billing_address   = $app[0]->billing_address;
		$customer->billing_address_2 = $app[0]->billing_address_2;
		$customer->billing_zip       = $app[0]->billing_zip;
		$customer->company           = $app[0]->company;
		$customer->vatnum            = $app[0]->vatnum;
		$customer->ssn               = $app[0]->ssn;
		$customer->notes             = $app[0]->notes;
		$customer->image             = $app[0]->image;

		$customer->fields = new stdClass;
		$customer->fields->restaurant = (array) json_decode((string) $app[0]->fields, true);
		$customer->fields->takeaway   = (array) json_decode((string) $app[0]->tkfields, true); 

		$customer->user = new stdClass;
		$customer->user->id       = $app[0]->jid;
		$customer->user->name     = $app[0]->user_name;
		$customer->user->username = $app[0]->user_username;
		$customer->user->email    = $app[0]->user_email;

		$customer->locations = array();

		foreach ($app as $d)
		{
			if (!empty($d->delivery_address))
			{
				$addr = new stdClass;
				$addr->id          = $d->delivery_id;
				$addr->country     = $d->delivery_country;
				$addr->countryName = $d->delivery_country_name;
				$addr->state       = $d->delivery_state;
				$addr->city        = $d->delivery_city;
				$addr->address     = $d->delivery_address;
				$addr->address_2   = $d->delivery_address_2;
				$addr->zip         = $d->delivery_zip;
				$addr->type        = $d->delivery_type;
				$addr->note        = $d->delivery_note;
				$addr->latitude    = $d->delivery_latitude;
				$addr->longitude   = $d->delivery_longitude;
				
				// get a string representation of the delivery address (exclude country and address notes)
				$addr->fullString  = VikRestaurants::deliveryAddressToStr($addr, ['country', 'address_2']);

				$customer->locations[] = $addr;
			}
		}

		return $customer;
	}

	/**
	 * Extracts the first name and last name from the user address
	 * and pre-fill the custom fields, in case they are empty.
	 *
	 * @param 	array 	 $cf       A list of custom fields.
	 * @param 	array 	 &$fields  Where to inject the fetched data.
	 * @param 	boolean  $first    True whether the first name is usually
	 * 							   specified before the last name.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 * @deprecated 1.10  Use E4J\VikRestaurants\CustomFields\Helpers\FieldsHelper::autoPopulate() instead.
	 */
	public static function extractNameFields($cf, &$fields, $first = true)
	{
		E4J\VikRestaurants\CustomFields\Helpers\FieldsHelper::autoPopulate($fields, $cf, null, $first);
	}

	/**
	 * Fetches an associative array containing the value that each
	 * custom field of "address" type could assume. In case an address
	 * has been already validated (e.g. through the MAP module), the
	 * fetched parts will be retrieved and assigned to the related field
	 * in order to perfectly fit a valid address.
	 *
	 * @param 	array 	$cf       A list of custom fields.
	 * @param 	array 	&$fields  Where to inject the fetched data.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 * @deprecated 1.10  Use E4J\VikRestaurants\CustomFields\Helpers\FieldsHelper::autoPopulate() instead.
	 */
	public static function extractAddressFields($cf, &$fields)
	{
		E4J\VikRestaurants\CustomFields\Helpers\FieldsHelper::autoPopulate($fields, $cf);
	}

	/**
	 * Checks whether the current user is able to leave a review
	 * for the specified take-away product.
	 *
	 * @param 	integer  $id_product  The product ID.
	 *
	 * @return 	boolean  True if possible, false otherwise.
	 *
	 * @since 	1.7
	 */
	public static function canLeaveTakeAwayReview($id_product)
	{
		$dbo  = JFactory::getDbo();
		$user = JFactory::getUser();

		// get leave review mode:
		// - 0 	anyone
		// - 1  registered user
		// - 2  verified purchase
		$mode = VREFactory::getConfig()->getUint('revleavemode');

		if ($mode > 0)
		{
			// user must be logged in
			if ($user->guest)
			{
				return false;
			}
		}

		$id_product = (int) $id_product;

		// check if the user already left a review
		if (self::isAlreadyTakeAwayReviewed($id_product, $user->id))
		{
			// the user already wrote a review
			return false;
		}

		if ($mode != 2)
		{
			return true;
		}

		// make sure the user is a verified purchaser by checking whether
		// the date of the purchase of this product exists and it is in the past
		if (self::isVerifiedTakeAwayReview($id_product, $user))
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks whether the specified product has been
	 * already reviewed by the user.
	 * 
	 * @param 	integer  $id_product  The product to look for.
	 * @param 	integer  $id_user     The CMS user ID. If not provided, the current one
	 * 								  will be used.
	 *
	 * @return 	boolean  True if already rated, false otherwise.
	 *
	 * @since 	1.7
	 */
	public static function isAlreadyTakeAwayReviewed($id_product, $id_user = null)
	{
		$dbo = JFactory::getDbo();

		if ($id_user === null)
		{
			// take current user
			$id_user = JFactory::getUser()->id;
		}

		// get user IP address
		$ip_addr = JFactory::getApplication()->input->server->get('REMOTE_ADDR');

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikrestaurants_reviews'))
			->where($dbo->qn('id_takeaway_product') . ' = ' . (int) $id_product);

		if ($id_user > 0)
		{
			// search by user ID
			$q->where($dbo->qn('jid') . ' = ' . (int) $id_user);
		}
		else
		{
			// search by IP address
			$q->where($dbo->qn('ipaddr') . ' = ' . $dbo->q($ip_addr));
		}

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		return (bool) $dbo->getNumRows();
	}

	/**
	 * Checks whether the specified user made a purchase in the past
	 * for the selected take-away product. Needed to check when
	 * the leave review mode is set to 2: verified purchaser.
	 *
	 * @param 	integer  $id_product  The product to look for.
	 * @param 	mixed 	 $user 		  Either the user id or an object. If not
	 * 								  specified, the current user will be taken.
	 *
	 * @return 	boolean  True if verified purchaser, false otherwise.
	 *
	 * @since 	1.7
	 */
	public static function isVerifiedTakeAwayReview($id_product, $user = null)
	{
		if (is_null($user))
		{
			// take current user
			$user = JFactory::getUser();	
		}
		else if (is_scalar($user))
		{
			// get specified user
			$user = JFactory::getUser($user);
		}

		if ($user->guest)
		{
			// guest user cannot be a verified purchaser
			return false;
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select(1)
			->from($dbo->qn('#__vikrestaurants_takeaway_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'i') . ' ON ' . $dbo->qn('r.id') . ' = ' . $dbo->qn('i.id_res'))
			->leftjoin($dbo->qn('#__vikrestaurants_users', 'u') . ' ON ' . $dbo->qn('r.id_user') . ' = ' . $dbo->qn('u.id'))
			->where($dbo->qn('u.jid') . ' = ' . $user->id)
			->where($dbo->qn('i.id_product') . ' = ' . (int) $id_product)
			->where($dbo->qn('r.checkin_ts') . ' < ' . static::now());

		// get any approved codes
		if ($approved = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]))
		{
			// filter by approved status
			$q->where($dbo->qn('r.status') . ' IN (' . implode(',', array_map(array($dbo, 'q'), $approved)) . ')');
		}

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		return (bool) $dbo->getNumRows();
	}

	// ORDER STATUS

	/**
	 * Creates or updates the given order status.
	 * An order cannot have 2 or more statuses with the same code.
	 *
	 * @param 	integer  $oid 		The order ID.
	 * @param 	integer  $code_id 	The code ID.
	 * @param 	integer  $group 	The group type (1: restaurants, 2: take-away).
	 * @param 	mixed 	 $notes 	Some notes about the order status.
	 * 								If null, they won't be altered in case of update.
	 *
	 * @return 	integer  The ID of the order status.
	 *
	 * @since 	1.7
	 */
	public static function insertOrderStatus($oid, $code_id, $group, $notes = null)
	{
		if ($code_id <= 0)
		{
			return null;
		}

		$dbo = JFactory::getDbo();

		$oid 		= intval($oid);
		$code_id 	= intval($code_id);
		$group 		= ($group == 1 ? 1 : 2);

		// check if we have an order status with the specified code
		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->from($dbo->qn('#__vikrestaurants_order_status'))
			->where($dbo->qn('id_order') . ' = ' . $oid)
			->where($dbo->qn('id_rescode') . ' = ' . $code_id)
			->where($dbo->qn('group') . ' = ' . $group);

		$dbo->setQuery($q, 0, 1);
		$pk = $dbo->loadResult();

		$data = new stdClass;
		$data->id_order   = $oid;
		$data->id_rescode = $code_id;
		$data->createdby  = JFactory::getUser()->id;
		$data->createdon  = static::now();
		$data->group 	  = $group;
		
		if ($notes !== null)
		{
			$data->notes = $notes;
		}

		if ($pk)
		{
			// update
			$data->id = (int) $pk;

			$dbo->updateObject('#__vikrestaurants_order_status', $data, 'id');
		}
		else
		{
			// insert
			$dbo->insertObject('#__vikrestaurants_order_status', $data, 'id');
		}

		return $data->id;
	}

	/**
	 * Loads the Graphics2D dependencies.
	 *
	 * @return 	void
	 *
	 * @since 	1.7
	 * @deprecated 1.10  Directly use the Graphics2D namespace.
	 */
	public static function loadGraphics2D()
	{
		VRELoader::import('library.graphics2d.graphics2d');
	}

	/**
	 * Checks whether the system supports certain types of
	 * delivery areas.
	 *
	 * @param 	array 	 $types  A list of delivery types. If not specified,
	 * 							 all the types will be retrieved.
	 *
	 * @return 	boolean  True if supported, false otherwise.
	 *
	 * @since 	1.7
	 * @deprecated 1.10  Use E4J\VikRestaurants\DeliveryArea\AreasCollection instead.
	 */
	public static function hasDeliveryAreas(array $types = [])
	{
		/** @var E4J\VikRestaurants\DeliveryArea\AreasCollection */
		$areas = E4J\VikRestaurants\DeliveryArea\AreasCollection::getInstance()
			->filter(new E4J\VikRestaurants\DeliveryArea\Filters\PublishedFilter);

		if ($types)
		{
			$areas = $areas->filter(new E4J\VikRestaurants\DeliveryArea\Filters\TypesFilter($types));
		}

		return (bool) count($areas);
	}

	/**
	 * Returns a list of delivery areas.
	 *
	 * @param 	boolean  $published  True to obtain only the published areas.
	 *
	 * @return 	array 	 A list of areas.
	 *
	 * @since 	1.7
	 * @deprecated 1.10  Use E4J\VikRestaurants\DeliveryArea\AreasCollection instead.
	 */
	public static function getAllDeliveryAreas($published = false)
	{
		/** @var E4J\VikRestaurants\DeliveryArea\AreasCollection */
		$areas = E4J\VikRestaurants\DeliveryArea\AreasCollection::getInstance();

		if ($published)
		{
			$areas = $areas->filter(new E4J\VikRestaurants\DeliveryArea\Filters\PublishedFilter);
		}

		/**
		 * For site client only, filter the delivery areas according
		 * to the configuration of the special days.
		 *
		 * @since 1.8.2
		 */
		if ($app->isClient('site'))
		{
			$tmp = $areas->filter(new E4J\VikRestaurants\DeliveryArea\Filters\SpecialDaysFilter);

			// Make sure we have some delivery areas, because otherwise the assigned ones
			// might have been deleted. In that case, we should ignore the special day filter.
			if (count($tmp) > 0)
			{
				// apply filter
				$areas = $tmp;
			}
		}

		return $areas;
	}

	/**
	 * Returns the delivery area that matches the specified coordinates at best.
	 *
	 * @param 	mixed 	$lat   The latitude.
	 * @param 	mixed 	$lng   The longitude.
	 * @param 	mixed 	$zip   The ZIP code.
	 * @param 	mixed 	$city  The city name.
	 *
	 * @return 	mixed   The matching area on success, null otherwise.
	 *
	 * @since 	1.7
	 * @deprecated 1.10  Use E4J\VikRestaurants\DeliveryArea\DeliveryChecker::search() instead.
	 */
	public static function getDeliveryAreaFromCoordinates($lat = null, $lng = null, $zip = null, $city = null)
	{
		/** @var E4J\VikRestaurants\DeliveryArea\AreasCollection */
		$zones = E4J\VikRestaurants\DeliveryArea\AreasCollection::getInstance()
			->filter(new E4J\VikRestaurants\DeliveryArea\Filters\PublishedFilter);

		/** @var E4J\VikRestaurants\DeliveryArea\Area|null */
		return (new E4J\VikRestaurants\DeliveryArea\DeliveryChecker($zones))->search([
			'latitude'  => $lat,
			'longitude' => $lng,
			'zip'       => $zip,
			'city'      => $city,
		]);
	}
	
	// LANGUAGE TRANSLATIONS

	/**
	 * Returns the default language of the website.
	 *
	 * @param 	string 	$client  The client to check (site or admin).
	 *
	 * @return 	string 	The default language tag.
	 *
	 * @since 	1.4
	 */
	public static function getDefaultLanguage($client = 'site')
	{
		// get default language for the specified client
		return JComponentHelper::getParams('com_languages')->get($client);
	}
	
	/**
	 * Loads the specified language.
	 *
	 * @param 	string 	$tag     The language tag to load.
	 * @param 	mixed 	$client  The base path of the language.
	 *
	 * @return 	void
	 *
	 * @since 	1.4
	 */
	public static function loadLanguage($tag, $client = null)
	{
		if (!empty($tag))
		{
			/**
			 * Added support for client argument to allow also
			 * the loading of back-end languages.
			 *
			 * @since 1.8
			 */
			if (is_null($client))
			{
				$client = JPATH_SITE;
			}
			/**
			 * Auto-detect the correct client depending on the current session.
			 * 
			 * @since 1.9
			 */
			else if ($client === 'auto')
			{
				$client = JFactory::getApplication()->isClient('site') ? JPATH_SITE : JPATH_ADMINISTRATOR;
			}

			$lang = JFactory::getLanguage();

			/**
			 * In case the extension doesn't support the specified language,
			 * Joomla loads by default the default en-GB version.
			 * So, we don't need to add a fallback.
			 */
			$lang->load('com_vikrestaurants', $client, $tag, true);

			/**
			 * Reload system language too.
			 *
			 * @since 1.8.1
			 */
			$lang->load('joomla', $client, $tag, true);
		}
	}
	
	/**
	 * Returns a list of installed languages.
	 *
	 * @return 	array
	 *
	 * @since 	1.4
	 */
	public static function getKnownLanguages()
	{
		// get default language
		$def_lang = self::getDefaultLanguage('site');

		// get installed languages
		$known_languages = VREApplication::getInstance()->getKnownLanguages();
		
		$languages = array();

		foreach ($known_languages as $k => $v)
		{
			if ($k == $def_lang)
			{
				// move default language in first position
				array_unshift($languages, $k);
			}
			else
			{
				// otherwise insert at the end
				array_push($languages, $k);
			}
		}
		
		return $languages;
	}

	/**
	 * Translates a list of rooms.
	 *
	 * @param 	array 	&$rooms  A list of rooms (objects or arrays).
	 * @param 	string  $lang    An optional language to use. If not
	 * 							 specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateRooms(&$rooms, $lang = null)
	{
		self::translateRecords('room', $rooms, $lang);
	}

	/**
	 * Translates a list of menus.
	 *
	 * @param 	array 	&$menus  A list of menus (objects or arrays).
	 * @param 	string  $lang    An optional language to use. If not
	 * 							 specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateMenus(&$menus, $lang = null)
	{
		self::translateRecords('menu', $menus, $lang);
	}

	/**
	 * Translates a list of products.
	 *
	 * @param 	array 	&$products  A list of products (objects or arrays).
	 * @param 	string  $lang       An optional language to use. If not
	 * 							    specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateMenusProducts(&$products, $lang = null)
	{
		self::translateRecords('menusproduct', $products, $lang);
	}

	/**
	 * Translates a list of payments.
	 *
	 * @param 	array 	&$payments  A list of payments (objects or arrays).
	 * @param 	string  $lang       An optional language to use. If not
	 * 							    specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translatePayments(&$payments, $lang = null)
	{
		self::translateRecords('payment', $payments, $lang);
	}

	/**
	 * Translates a list of take-away menus.
	 *
	 * @param 	array 	&$menus  A list of menus (objects or arrays).
	 * @param 	string  $lang    An optional language to use. If not
	 * 							 specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateTakeawayMenus(&$menus, $lang = null)
	{
		self::translateRecords('tkmenu', $menus, $lang);
	}

	/**
	 * Translates a list of take-away products.
	 *
	 * @param 	array 	&$items  A list of products (objects or arrays).
	 * @param 	string  $lang    An optional language to use. If not
	 * 							 specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateTakeawayProducts(&$items, $lang = null)
	{
		self::translateRecords('tkentry', $items, $lang);
	}

	/**
	 * Translates a list of take-away product variations.
	 *
	 * @param 	array 	&$options  A list of variations (objects or arrays).
	 * @param 	string  $lang      An optional language to use. If not
	 * 							   specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateTakeawayProductOptions(&$options, $lang = null)
	{
		self::translateRecords('tkentryoption', $options, $lang);
	}

	/**
	 * Translates a list of take-away attributes.
	 *
	 * @param 	array 	&$attributes  A list of attributes (objects or arrays).
	 * @param 	string  $lang         An optional language to use. If not
	 * 							      specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 */
	public static function translateTakeawayAttributes(&$attributes, $lang = null)
	{
		self::translateRecords('tkattr', $attributes, $lang);
	}

	/**
	 * Translates a list of take-away deals.
	 *
	 * @param 	array 	&$deals  A list of deals (objects or arrays).
	 * @param 	string  $lang    An optional language to use. If not
	 * 							 specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 *
	 * @uses 	translateRecords()
	 */
	public static function translateTakeawayDeals(&$deals, $lang = null)
	{
		self::translateRecords('tkdeal', $deals, $lang);
	}

	/**
	 * Translates a list of take-away toppings groups.
	 * All the assigned toppings will be translated too.
	 *
	 * @param 	array 	&$groups  A list of groups (objects or arrays).
	 * @param 	string  $lang     An optional language to use. If not
	 * 							  specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 */
	public static function translateTakeawayToppingsGroups(&$groups, $lang = null)
	{
		self::translateRecords('tkentrygroup', $groups, $lang);

		foreach ($groups as &$group)
		{
			if (isset($group->list))
			{
				$k = 'list';
			}
			else if (isset($group->toppings))
			{
				$k = 'toppings';
			}
			else
			{
				$k = null;
			}

			// use title as description if not specified
			$group->description = $group->description ? $group->description : $group->title;

			if ($k)
			{
				// translate toppings if specified
				foreach ($group->{$k} as &$toppings)
				{
					self::translateTakeawayToppings($toppings, $lang);
				}
			}
		}
	}

	/**
	 * Translates a list of take-away toppings.
	 *
	 * @param 	array 	&$toppings  A list of toppings (objects or arrays).
	 * @param 	string  $lang       An optional language to use. If not
	 * 							    specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 */
	public static function translateTakeawayToppings(&$toppings, $lang = null)
	{
		self::translateRecords('tktopping', $toppings, $lang);
	}

	/**
	 * Translates a configuration setting.
	 *
	 * @param 	string 	$param  The setting name to translate.
	 * @param 	string  $lang   An optional language to use. If not
	 * 							specified, the current one will be used.
	 *
	 * @return 	string  The translated setting.
	 *
	 * @since 	1.8
	 */
	public static function translateSetting($param, $lang = null)
	{
		$settings = array();
		$settings[$param] = VREFactory::getConfig()->get($param);

		// translate setting
		self::translateConfig($settings, $lang);

		// return single translation
		return $settings[$param];
	}

	/**
	 * Translates a list of configuration settings.
	 *
	 * @param 	array 	&$settings  A list of settings (objects or arrays).
	 * @param 	string  $lang       An optional language to use. If not
	 * 							    specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 */
	public static function translateConfig(&$settings, $lang = null)
	{
		$tmp = $settings;

		if (is_string(key($tmp)))
		{
			// adjust settings array
			$settings = array();

			foreach ($tmp as $k => $v)
			{
				$settings[] = array(
					'param'   => $k,
					'setting' => $v,
				);
			}
		}

		// translate settings
		self::translateRecords('config', $settings, $lang);

		// reset array
		reset($tmp);

		if (is_string(key($tmp)))
		{
			// back to associative array
			$tmp = $settings;
			$settings = array();

			foreach ($tmp as $r)
			{
				$settings[$r['param']] = $r['setting'];
			}
		}
	}

	/**
	 * Translates a list of generic translatable records.
	 *
	 * @param 	string 	$table     The translatable table name.
	 * @param 	array 	&$records  A list of records (objects or arrays).
	 * @param 	string  $lang      An optional language to use. If not
	 * 							   specified, the current one will be used.
	 *
	 * @return 	void
	 *
	 * @since 	1.8
	 */
	public static function translateRecords($table, &$records, $lang = null)
	{
		// make sure multi-language is supported
		if (!$records || !static::isMultilanguage())
		{
			return false;
		}

		if (!$lang)
		{
			// get current language tag if not specified
			$lang = JFactory::getLanguage()->getTag();
		}

		// get translator
		$translator = VREFactory::getTranslator();

		// get translation table foreign key
		$fk = $translator->getTable($table)->getLinkedPrimaryKey();

		if (!is_array($records))
		{
			// always use an array
			$records = array($records);
			// remember that the argument was NOT an array
			$was_array = false;
		}
		else
		{
			// remember that the argument was already an array
			$was_array = true;
		}

		// extract IDs from records
		$ids = array();

		foreach ($records as $item)
		{
			$ids[] = is_object($item) ? $item->{$fk} : $item[$fk];
		}

		// preload table translations
		$tbLang = $translator->load($table, array_unique($ids), $lang);

		foreach ($records as &$item)
		{
			$id = is_object($item) ? $item->{$fk} : $item[$fk];

			// translate record for the given language
			$tx = $tbLang->getTranslation($id, $lang);

			if ($tx)
			{
				// get translations columns lookup
				$columns = $tbLang->getContentColumns($original = true);

				// iterate all the columns
				foreach ($columns as $colName)
				{
					// inject translation within the record
					if (is_object($item))
					{
						// treat record as object
						$item->{$colName} = $tx->{$colName};
					}
					else
					{
						// treat record as associative array
						$item[$colName] = $tx->{$colName};
					}
				}
			}
		}

		if (!$was_array)
		{
			// revert to original value
			$records = array_shift($records);
		}
	}
	
	// OPERATORS LOGS
	
	/**
	 * Returns a list of e-mail addresses that belong to the
	 * operators that should receive notifications for the
	 * specified group.
	 *
	 * @param 	integer  $group  The group to check (0: both, 1: restaurant, 2: takeaway).
	 * @param 	mixed 	 $order  The details of the order.
	 *
	 * @return 	array 	 A list of e-mails.
	 *
	 * @since 	1.5
	 */
	public static function getNotificationOperatorsMails($group = 0, $order = null)
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);

		$q->select('*')
			->from($dbo->qn('#__vikrestaurants_operator'))
			->where($dbo->qn('mail_notifications') . ' = 1')
			->where($dbo->qn('email') . '<> ""');

		if ($group > 0)
		{
			$q->where($dbo->qn('group') . ' IN (0, ' . (int) $group . ')');
		}

		$dbo->setQuery($q);
		$rows = $dbo->loadObjectList();

		if (!$rows)
		{
			return [];
		}

		VRELoader::import('library.operator.user');

		$operators = [];
		
		/**
		 * Take only the operators that are able to access the
		 * room assigned to the specified order.
		 *
		 * @since 1.8
		 */
		foreach ($rows as $operator)
		{
			// instantiate operator
			$operator = new VREOperatorUser($operator);

			$add = true;

			if ($order)
			{
				// validate for restaurant group only
				if ($group == 1)
				{
					// include operator only in case it can access the room of the order
					$add = $operator->canAccessRoom($order->room->id);
				}
			}

			if ($add)
			{
				// include e-mail only
				$operators[] = $operator->get('email');
			}
		}

		return $operators;
	}	
}
