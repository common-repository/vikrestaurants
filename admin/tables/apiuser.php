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
 * VikRestaurants API user table.
 *
 * @since 1.8
 */
class VRETableApiuser extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_api_login', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'username';
		$this->_requiredFields[] = 'password';
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

		// properly format "denied plugins" list
		if (isset($src['denied']) && is_array($src['denied']))
		{
			$src['denied'] = json_encode($src['denied']);
		}

		// properly format "allowed IPs" list
		if (isset($src['ips']) && is_array($src['ips']))
		{
			$src['ips'] = json_encode(array_values(array_filter($src['ips'])));
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to
	 * ensure they are safe to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		// check integrity using parent
		if (!parent::check())
		{
			return false;
		}

		// make sure the username doesn't already exist
		if (isset($this->username))
		{
			$db = JFactory::getDbo();

			$q = $db->getQuery(true)
				->select(1)
				->from($db->qn('#__vikrestaurants_api_login'))
				->where($db->qn('username') . ' = ' . $db->q($this->username));

			if ($this->id)
			{
				$q->where($db->qn('id') . ' <> ' . (int) $this->id);
			}
			
			$db->setQuery($q, 0, 1);
			$db->execute();

			if ($db->getNumRows())
			{
				// register error message
				$this->setError(JText::translate('VRAPIUSERUSERNAMEEXISTS'));

				// invalid start date
				return false;
			}
		}

		return true;
	}
}
