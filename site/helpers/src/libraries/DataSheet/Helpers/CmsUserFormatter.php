<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Trait used to easily format the CMS user details.
 * 
 * @since 1.9
 */
trait CmsUserFormatter
{
	/**
	 * Internal property used to cache the fetched details.
	 * 
	 * @var array
	 */
	private $cmsUsersLookup = [];

	/**
	 * Fetches the username of the matching user ID.
	 * 
	 * @param   int     $userId  The ID of the user to search.
	 * 
	 * @return  string  The matching username.
	 */
	public function toUsername(int $userId)
	{
		// check whether the username of this ID has been already fetched
		if (!isset($this->cmsUsersLookup[$userId]))
		{
			// cache username for later use
			$this->cmsUsersLookup[$userId] = (string) (new \JUser($userId))->username;
		}

		return $this->cmsUsersLookup[$userId];
	}

	/**
	 * Fetches the ID of the matching username.
	 * 
	 * @param   string  $username  The username of the user to search.
	 * 
	 * @return  int     The matching user ID.
	 */
	public function toUserID(string $username)
	{
		// check whether the ID of this username has been already fetched
		if (!isset($this->cmsUsersLookup[$username]))
		{
			// access database instance if available, otherwise access the global one
			if (property_exists($this, 'db'))
			{
				$db = $this->db;
			}
			else
			{
				$db = \JFactory::getDbo();
			}

			// search user ID by username
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn('#__users'))
				->where($db->qn('username') . ' = ' . $db->q($username));

			$db->setQuery($query, 0, 1);

			// cache user ID for later use
			$this->cmsUsersLookup[$username] = (int) $db->loadResult();
		}

		return $this->cmsUsersLookup[$username];
	}
}
