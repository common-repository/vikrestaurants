<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Interface used to check whether a user is or should be banned.
 * 
 * @see User
 * 
 * @since 1.9
 */
interface BlockEngine
{
	/**
	 * Checks whether the provided user has been banned.
	 * This action is executed only before the authentication.
	 * The ban could be evaluated on the name of the user and on the IP origin.
	 *
	 * @param   User  $user  The object of the user.
	 *
	 * @return  bool  True is the user is banned, otherwise false.
	 */
	public function isBanned(User $user);

	/**
	 * Evaluates if the provided user needs to be banned.
	 * This action is executed only after a failed authentication.
	 *
	 * @param   User  $user  The object of the user.
	 *
	 * @return  bool  Return true if the user should be banned, otherwise false.
	 */
	public function needBan(User $user);

	/**
	 * Registers a new ban for the provided user.
	 *
	 * @param   User  $user  The object of the user.
	 * 
	 * @return  void
	 */
	public function ban(User $user);

	/**
	 * Resets or removes the ban of the provided user.
	 *
	 * @param   User  $user  The object of the user.
	 * 
	 * @return  void
	 */
	public function unban(User $user);
}
