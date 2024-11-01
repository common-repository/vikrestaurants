<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API\Framework;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\BlockEngine;
use E4J\VikRestaurants\API\User;

/**
 * Automatically blocks a session when the number of failures reaches the
 * configured threshold.
 * 
 * @see BlockEngine
 * @see User
 * 
 * @since 1.9
 */
class MaxAttemptsBanner implements BlockEngine
{
	/** @var int */
	protected $maxFailureAttempts;

	/**
	 * Class constructor.
	 * 
	 * @param  int  $threshold  The maximum number of allowed failures.
	 */
	public function __construct(int $threshold)
	{
		// should not be lower than 2
		$this->maxFailureAttempts = max(2, (int) $threshold);
	}

	/**
	 * @inheritDoc
	 *
	 * A user is considered banned when its failures are equals or higher
	 * than the maximum number of failure attempts allowed.
	 *
	 * The failure attempts are always increased by the ban() function.
	 */
	public function isBanned(User $user)
	{
		/** @var \stdClass the number of failures associated to the IP address of the user */
		$ban = \JModelVRE::getInstance('apiban')->getItem([
			'ip' => $user->getSourceIp(),
		], $blank = true);

		// in case the failures count is equals or higher than the maximum allowed, it means
		// that the user needs to be banned
		return $ban->fail_count >= $this->maxFailureAttempts;
	}

	/**
	 * @inheritDoc
	 * 
	 * Considering this function is called after every failure, a ban is always needed.
	 * Every time this function is executed, the system will call the ban() function to apply the ban.
	 */
	public function needBan(User $user)
	{
		// all failures need to be banned
		// ban() function is used to increase the number of failures
		return true;
	}

	/**
	 * @inheritDoc
	 * 
	 * Increases the failure attempts of the provided user.
	 * Once this function is terminated, the user is not effectively banned, unless its 
	 * total failures are equals or higher than the maximum number allowed.
	 */
	public function ban(User $user)
	{
		/** @var \stdClass recover the ban details for this IP address */
		$ban = \JModelVRE::getInstance('apiban')->getItem([
			'ip' => $user->getSourceIp(),
		], $blank = true);

		// increase fails count by one
		$ban->fail_count++;

		// save ban through model
		\JModelVRE::getInstance('apiban')->save((array) $ban);
	}

	/**
	 * @inheritDoc
	 * 
	 * Resets the count of failure attempts for the provided user.
	 */
	public function unban(User $user)
	{
		if (!$user->id())
		{
			return false;
		}

		/** @var \stdClass|null recover the ban details for this IP address */
		$ban = \JModelVRE::getInstance('apiban')->getItem([
			'ip' => $user->getSourceIp(),
		]);

		if ($ban)
		{
			// reset count
			$ban->fail_count = 0;

			// save ban through model
			\JModelVRE::getInstance('apiban')->save((array) $ban);
		}

		return true;
	}
}
