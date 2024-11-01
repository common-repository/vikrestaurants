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
 * VikRestaurants customer table.
 *
 * @since 1.8
 */
class VRETableCustomer extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_users', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'billing_name';
		$this->_requiredFields[] = 'billing_mail';
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

		$user = null;

		// check if the user attribute was passed
		if (isset($src['user']))
		{
			// register user fields to create a new user account
			$user = array();
			$user['usertype']      = array();
			$user['user_name']     = $src['billing_name'];
			$user['user_mail']     = $src['user']['usermail'];
			$user['user_username'] = $src['user']['username'];
			$user['user_pwd1']     = $src['user']['password'];
			$user['user_pwd2']     = $src['user']['confirm'];

			// always unset 'user' attribute before saving an operator
			unset($src['user']);
		}

		// JSON encode restaurant fields
		if (isset($src['fields']) && !is_string($src['fields']))
		{
			$src['fields'] = json_encode($src['fields']);
		}

		// JSON encode take-away fields
		if (isset($src['tkfields']) && !is_string($src['tkfields']))
		{
			$src['tkfields'] = json_encode($src['tkfields']);
		}

		if (isset($src['ssn']))
		{
			// make SSN uppercase
			$src['ssn'] = strtoupper($src['ssn']);
		}

		// bind the details before save
		$return = parent::bind($src, $ignore);

		if ($return && $user)
		{
			try
			{
				// try to create a new Joomla User account
				$this->jid = VikRestaurantsHelper::createNewUserAccount($user);
			}
			catch (Exception $e)
			{
				// an error occurred, register error and abort saving
				$this->setError($e);

				return false;
			}
		}

		return $return;
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

			if (isset($data['user']['username']))
			{
				$data['username'] = $data['user']['username'];
			}

			if (isset($data['user']['usermail']))
			{
				$data['usermail'] = $data['user']['usermail'];
			}

			if (isset($data['locations']))
			{
				foreach ($data['locations'] as &$loc)
				{
					if (is_string($loc))
					{
						$loc = json_decode($loc);
					}
				}
			}
		}

		return parent::setUserStateData($data);
	}
}
