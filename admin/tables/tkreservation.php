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

// track log if the user is an operator
VRELoader::import('library.operator.logger');

/**
 * VikRestaurants take-away order table.
 *
 * @since 1.8
 */
class VRETableTkreservation extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_reservation', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'sid';
		$this->_requiredFields[] = 'checkin_ts';
		$this->_requiredFields[] = 'service';
	}

	/**
	 * Method to bind an associative array or object to the Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind($src, $ignore = array())
	{
		$src = (array) $src;

		// check if new record
		if (empty($src['id']))
		{
			// generate serial ID if not specified
			if (!isset($src['sid']))
			{
				$src['sid'] = VikRestaurants::generateSerialCode(16, 'order-sid');
			}

			// generate confirmation code if not specified
			if (!isset($src['conf_key']))
			{
				$src['conf_key'] = VikRestaurants::generateSerialCode(12, 'order-confkey');
			}

			// register current user as author, if not specified
			if (!isset($src['created_by']))
			{
				$src['created_by'] = JFactory::getUser()->id;
			}

			// register current datetime as creation date, if not specified
			if (!isset($src['created_on']))
			{
				$src['created_on'] = VikRestaurants::now();
			}
		}
		else
		{
			// register current datetime as modified date, if not specified
			if (!isset($src['modified_on']))
			{
				$src['modified_on'] = VikRestaurants::now();
			}
		}

		// create checkin timestamp in case the date attribute is set
		if (!isset($src['checkin_ts']) && isset($src['date']))
		{
			// extract hours and minutes
			if (!empty($src['hourmin']))
			{
				list($hour, $min) = explode(':', $src['hourmin']);
			}
			else
			{
				$hour = isset($src['hour']) ? $src['hour'] : 0;
				$min  = isset($src['min'])  ? $src['min']  : 0;
			}

			$src['checkin_ts'] = VikRestaurants::createTimestamp($src['date'], (int) $hour, (int) $min);
		}

		// encode custom fields in JSON format in case they was passed as array/object
		if (isset($src['custom_f']) && !is_string($src['custom_f']))
		{
			$src['custom_f'] = json_encode($src['custom_f']);
		}

		if (isset($src['fields_data']))
		{
			$src['fields_data'] = (array) $src['fields_data'];

			$lookup = [
				'purchaser_nominative',
				'purchaser_mail',
				'purchaser_phone',
				'purchaser_prefix',
				'purchaser_country',
				'purchaser_address',
				'notes',
			];

			// set up billing according to the specified custom fields,
			// only in case the targeted field is empty
			foreach ($lookup as $k)
			{
				if (empty($src[$k]) && !empty($src['fields_data'][$k]))
				{
					$src[$k] = $src['fields_data'][$k];
				}
			}
		}

		// stringify coupon code in case an array/object was passed
		if (isset($src['coupon']) && !is_string($src['coupon']))
		{
			// always use an object
			$coupon = (object) $src['coupon'];

			// create coupon string
			$src['coupon_str'] = @$coupon->code . ';;' . @$coupon->value . ';;' . @$coupon->percentot;
		}

		if (isset($src['cc_details']) && !is_string($src['cc_details']))
		{
			// encode array/object in JSON format
			$src['cc_details'] = json_encode($src['cc_details']);
		}

		// encode route details in JSON format in case they was passed as array/object
		if (isset($src['route']) && !is_string($src['route']))
		{
			// get rid of empty attributes
			$src['route'] = array_filter((array) $src['route'], function($v)
			{
				return strlen((string) $v);
			});

			if ($src['route'])
			{
				// we have at least an information
				$src['route'] = json_encode($src['route']);
			}
			else
			{
				// no information specified, unset route object
				$src['route'] = 'null'; 
			}
		}

		// always calculate lock time after creating/updating an order
		$src['locked_until'] = strtotime('+' . VREFactory::getConfig()->getUint('tklocktime') . ' minutes');

		if (isset($src['service']))
		{
			// keep registering the delivery_service column for BC
			$src['delivery_service'] = $src['service'] === 'delivery' ? 1 : 0;
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		// invoke parent to store the record
		if (!parent::store($updateNulls))
		{
			// do not proceed in case of error
			return false;
		}

		/**
		 * Unset cached reservation every time something changes.
		 *
		 * @since 1.8.2
		 */
		VREOrderFactory::changed('takeaway', $this->id);

		return true;
	}

	/**
	 * Helper method used to store the user data within the session.
	 *
	 * @param 	mixed 	$data  The array data to store.
	 *
	 * @return 	self    This object to support chaining.
	 * 
	 * @since   1.9
	 */
	public function setUserStateData($data = null)
	{
		if ($data)
		{
			$data = (array) $data;

			if (isset($data['items']))
			{
				foreach ($data['items'] as &$item)
				{
					if (is_string($item))
					{
						$item = json_decode($item);
					}
				}
			}
		}

		return parent::setUserStateData($data);
	}
}
