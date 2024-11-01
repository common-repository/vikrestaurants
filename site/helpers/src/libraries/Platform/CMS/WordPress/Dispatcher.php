<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\WordPress;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Event\EventResponse;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Implements the event dispatcher interface for the WordPress platform.
 * 
 * @since 1.9
 */
class Dispatcher implements DispatcherInterface
{
	/**
	 * @inheritDoc
	 */
	public function trigger(string $event, array $args = [])
	{
		\do_action_ref_array($this->getHook($event), $args);
	}

	/**
	 * @inheritDoc
	 */
	public function filter(string $event, array $args = [])
	{
		// inject argument at the beginning of the list, which will be
		// used as return value by the WordPress filtering technique
		array_unshift($args, null);

		$return = \apply_filters_ref_array($this->getHook($event), $args);

		if (is_null($return))
		{
			// no attached hooks
			$return = [];
		}
		else
		{
			// wrap returned value into an array
			$return = [$return];
		}

		return new EventResponse($return);
	}

	/**
	 * Checks whether the specified event uses the Joomla notation.
	 * In that case, rebuild the event to look more similar to
	 * WordPress hooks.
	 *
	 * @param 	string  $event  The event to check.
	 *
	 * @return 	string  The modified event, if needed.
	 */
	protected function getHook($event)
	{
		// make sure it starts with "on"
		if (preg_match("/^on[A-Z]/", $event))
		{
			// remove initial "on"
			$event = preg_replace("/^on/", '', $event);

			// remove plugin name from event and prepend it at the beginning
			$event = 'vikrestaurants' . preg_replace("/vikrestaurants/i", '', $event);

			// place an underscore between each camelCase
			$event = strtolower(preg_replace("/([a-z])([A-Z])/", '$1_$2', $event));
		}

		return $event;
	}
}
