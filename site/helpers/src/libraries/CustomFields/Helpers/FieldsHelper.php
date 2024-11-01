<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\FieldsCollection;

/**
 * VikRestaurants custom fields global helper.
 *
 * @since 1.9
 */
abstract class FieldsHelper
{
	/**
	 * The default country code in case it is not specified.
	 *
	 * @var string
	 */
	public static $defaultCountry = 'US';

	/**
	 * Translates the specified data array collected through the custom fields.
	 *
	 * @param   array             $data     The associative array with the CF data.
	 * @param   FieldsCollection  $fields   The custom fields (MUST BE already translated).
	 * @param   string            $langtag  The language tag to use.
	 *
	 * @return  array   The translated data array.
	 */
	public static function translateObject(array $data, FieldsCollection $fields, string $langtag = null)
	{
		$tmp = [];

		if ($langtag)
		{
			// reload system language
			\VikRestaurants::loadLanguage($langtag);
		}
		else
		{
			// use the default language tag
			$langtag = \JFactory::getLanguage()->getTag();
		}

		foreach ($fields as $field)
		{
			$fieldName = $field->get('name');

			if (!$fieldName || !array_key_exists($fieldName, $data))
			{
				// field not found inside the given object, go to next one
				continue;
			}

			// inject specified language tag
			$field->set('langtag', $langtag);

			// get a more readable text of the saved value
			$tmp[$fieldName] = $field->getReadableValue($data[$fieldName]);
		}

		return $tmp;
	}

	/**
	 * Return the default country code assigned to the phone number custom field.
	 *
	 * @param   string  $langtag  The langtag to retrieve the proper country depending
	 *                            on the current language.
	 * @param   mixed   $default  The default return value in case of missing field.
	 *
	 * @return  string  The default country code.
	 */
	public static function getDefaultCountryCode(string $langtag = null, $default = true)
	{
		$db = \JFactory::getDbo();

		/**
		 * Auto-detect language tag if not specified.
		 *
		 * @since 1.8
		 */
		if (!$langtag)
		{
			$langtag = \JFactory::getLanguage()->getTag();
		}

		$query = $db->getQuery(true);

		$query->select([
				$db->qn('c.id'),
				$db->qn('c.choose'),
				$db->qn('l.choose', 'lang_choose'),
				$db->qn('l.tag'),
			])
			->from($db->qn('#__vikrestaurants_custfields', 'c'))
			->leftjoin($db->qn('#__vikrestaurants_lang_customf', 'l') 
				. ' ON ' . $db->qn('l.id_customf') . ' = ' . $db->qn('c.id')
				. ' AND ' . $db->qn('l.tag') . ' = ' . $db->q($langtag))
			->where($db->qn('c.rule') . ' = ' . $db->q('phone'));

		/**
		 * Search for take-away fields only in case the restaurant is disabled.
		 *
		 * @since 1.8.1
		 */
		if (\VikRestaurants::isRestaurantEnabled() == false)
		{
			$query->where($db->qn('c.group') . ' = 1');
		}

		/**
		 * Search for restaurant fields only in case the take-away is disabled.
		 *
		 * @since 1.8.1
		 */
		if (\VikRestaurants::isTakeAwayEnabled() == false)
		{
			$query->where($db->qn('c.group') . ' = 0');
		}

		$db->setQuery($query, 0, 1);
		$field = $db->loadObject();

		if (!$field)
		{
			/**
			 * Evaluate to return default country code or the specified value.
			 *
			 * @since 1.8
			 */
			return $default === true ? self::$defaultCountry : $default;
		}

		// make sure we found a matching custom field
		if ($field->tag == $langtag && strlen($field->lang_choose))
		{
			// use country code defined in langtag
			$field->choose = $field->lang_choose;
		}
		// check if we should return the specified default value
		else if ($default !== true)
		{
			// unset string to return default value
			$field->choose = '';
		}

		$default = $default === true ? self::$defaultCountry : $default;

		// if we have a valid country code, return it, otherwise return the default value
		return strlen($field->choose) ? $field->choose : $default;
	}

	/**
	 * Tries to populate the custom fields values according to the details
	 * of the currently logged-in user.
	 *
	 * @param   mixed             &$data   Where to inject the fetched data.
	 * @param   FieldsCollection  $fields  An array of custom fields.
	 * @param   mixed             $user    The details of the user. If not specified, they will be loaded
	 *                                     from the account of the currently logged-in user.
	 * @param   bool              $first   True whether the first name is usually
	 *                                     specified before the last name.
	 *
	 * @return 	void
	 */
	public static function autoPopulate(&$data, FieldsCollection $fields, $user = null, $first = true)
	{
		// make sure the custom fields specify at least a value
		if (is_array($data) && array_filter($data))
		{
			// user details already populated, we don't need to go ahead
			return;
		}

		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		if (is_null($user))
		{
			// Go ahead even if the user might not be logged in, so that we can
			// properly fire the "onAutoPopulateCustomFields" hook.
			$user = \JFactory::getUser();
		}

		// treat user as object
		$user = (object) $user;

		// clone the user object
		$tmp = clone $user;

		if (empty($tmp->name))
		{
			// extract name from billing details
			$tmp->name = isset($user->purchaser_nominative) ? $user->purchaser_nominative : '';
		}

		if (empty($tmp->email))
		{
			// extract e-mail from billing details
			$tmp->email= isset($user->purchaser_mail) ? $user->purchaser_mail : '';
		}

		if (empty($tmp->phone))
		{
			// extract phone from billing details
			$tmp->phone = isset($user->purchaser_phone) ? $user->purchaser_phone : '';
		}

		$user = $tmp;

		/**
		 * Trigger hook to allow external plugins to prepare the user data
		 * before auto-populating the custom fields.
		 *
		 * @param   mixed  &$user  The details of the user.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onBeforeAutoPopulateCustomFields', [&$user]);

		$nameFields = $mailFields = $phoneFields = $cityFields = $addrFields = $zipFields = [];

		foreach ($fields as $field)
		{
			if ($field->get('rule') === 'nominative')
			{
				$nameFields[] = $field;
			}
			else if ($field->get('rule') === 'email')
			{
				$mailFields[] = $field;
			}
			else if ($field->get('rule') === 'phone')
			{
				$phoneFields[] = $field;
			}
			else if ($field->get('rule') === 'city')
			{
				$cityFields[] = $field;
			}
			else if ($field->get('rule') === 'address')
			{
				$addressFields[] = $field;
			}
			else if ($field->get('rule') === 'zip')
			{
				$zipFields[] = $field;
			}
		}

		// check if we have only one nominative custom field
		if (count($nameFields) == 1)
		{
			// we have a generic nominative, use the full name
			$data[$nameFields[0]->get('name')] = $user->name;
		}
		else if (count($nameFields) > 1)
		{
			// get name chunks
			$chunks = preg_split("/\s+/", $user->name);

			// extract last name from the list
			$lname = array_pop($chunks);
			// join remaining chunks into the first name
			$fname = implode(' ', $chunks);

			if (!$fname)
			{
				// first name missing, switch with last name because
				// the customers usually writes the first name instead
				// of the last name
				$fname = $lname;
				$lname = '';
			}

			if ($first)
			{
				// show first name and last name
				$data[$nameFields[0]->get('name')] = $fname;
				$data[$nameFields[1]->get('name')] = $lname;
			}
			else
			{
				// show last name and first name
				$data[$nameFields[0]->get('name')] = $lname;
				$data[$nameFields[1]->get('name')] = $fname;	
			}
		}

		if ($mailFields)
		{
			// auto-populate only the first available field with the
			// e-mail address of the current user
			$data[$mailFields[0]->get('name')] = $user->email;
		}

		if ($phoneFields && !empty($user->phone))
		{
			// auto-populate only the first available field with the
			// phone number of the current user
			$data[$phoneFields[0]->get('name')] = $user->phone;
		}

		///////////////////
		///// ADDRESS /////
		///////////////////

		// get latest searched address
		$delivery_address_object = \JModelVRE::getInstance('takeawayconfirm')->getDeliveryAddress();

		if ($delivery_address_object)
		{
			/** @var object */
			$parts = clone $delivery_address_object;

			// init address components
			$components = [
				'address' => [],
				'city'    => '',
				'zip'     => '',
			];

			// start from base address (street name + street number)
			if (!empty($parts->address))
			{
				$components['address'][] = trim($parts->address['name'] . ' ' . $parts->address['number']);
			}

			// fetch ZIP code
			$components['zip'] = $parts->zip ?? '';

			// fetch city
			$components['city'] = $parts->city ?? '';
			
			if (!$components['city'])
			{
				// fallback to state in case the city is not provided
				$components['city'] = $parts->state ?? '';
			}

			if ($components['city'])
			{
				if ($cityFields)
				{
					// register city value for custom field
					$data[$cityFields[0]->get('name')] = $components['city'];
				}
				else
				{
					// city field not found, add city to base address
					$components['address'][] = $components['city'];
				}
			}

			if ($components['zip'])
			{
				if ($zipFields)
				{
					// register ZIP value for custom field
					$data[$zipFields[0]->get('name')] = $components['zip'];
				}
				else
				{
					// ZIP field not found, add ZIP to base address
					$components['address'][] .= $components['zip'];
				}
			}

			if ($components['address'] && $addressFields)
			{
				// register address value for custom field
				$data[$addressFields[0]->get('name')] = implode(', ', $components['address']);
			}
		}

		/**
		 * Trigger hook to allow external plugins to auto-populate the custom fields
		 * with other details that are not supported by default by the user instance.
		 *
		 * @param   array             &$data   Where to inject the fetched data.
		 * @param   FieldsCollection  $fields  The custom fields list to display.
		 * @param   mixed             $user    The details of the user.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onAutoPopulateCustomFields', [&$data, $fields, $user]);
	}
}
