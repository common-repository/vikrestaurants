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
 * VikRestaurants operator table.
 *
 * @since 1.8
 */
class VRETableOperator extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_operator', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'firstname';
		$this->_requiredFields[] = 'lastname';
		$this->_requiredFields[] = 'email';
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
			if (empty($src['jid']))
			{
				// register user fields to create a new user account
				$user = array();
				$user['usertype']      = $src['user']['type'];
				$user['user_name']     = $src['firstname'] . ' ' . $src['lastname'];
				$user['user_mail']     = $src['email'];
				$user['user_username'] = $src['user']['username'];
				$user['user_pwd1']     = $src['user']['password'];
				$user['user_pwd2']     = $src['user']['confirm'];
			}

			// always unset 'user' attribute before saving an operator
			unset($src['user']);
		}

		if (isset($src['rooms']) && is_array($src['rooms']))
		{
			// stringify rooms list
			$src['rooms'] = implode(',', $src['rooms']);
		}

		if (isset($src['products']) && is_array($src['products']))
		{
			// stringify products list
			$src['products'] = implode(',', $src['products']);
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
}
