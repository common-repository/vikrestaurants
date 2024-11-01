<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API\Plugins;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\Event;
use E4J\VikRestaurants\API\Response;

/**
 * Event used to obtain a list of reservations and orders
 * that haven't been downloaded yet.
 *
 * @since 1.8.4
 * @since 1.9    Renamed from PullOrders.
 */
class OrdersPullPluginAPI extends Event
{
	/**
	 * @inheritDoc
	 */
	protected function execute(array $args, Response $response)
	{
		if (!$args)
		{	
			// no payload found, recover arguments from request
			$args = [];
			$args['reset'] = \JFactory::getApplication()->input->getBool('reset', false);
		}

		// wrap arguments in a registry
		$args = new \JRegistry($args);

		$eventArgs = [];
		$eventArgs['last_id'] = [];

		// check whether a reset was asked
		if (!$args->get('reset'))
		{
			// nope, get latest pulled IDs
			$eventArgs['last_id'] = $this->get('last_id', []);
		}
		
		try
		{
			/**
			 * Retrieve the orders and reservations by using a different API plugin.
			 *
			 * @see OrdersPullPluginAPI
			 */
			$json = \VREFactory::getAPI()->dispatch('orderslist', $eventArgs);
		}
		catch (\Exception $e)
		{
			// register response here
			$response->setContent($e->getMessage());
			// propagate exception
			throw $e;
		}

		// prepare client response
		$response->setStatus(1);

		// decode response
		$result = json_decode($json);

		// make sure the plugin fetched at least an order/reservation
		if ($result->status && $result->orders)
		{
			$last_id = $this->get('last_id', [0, 0]);

			foreach ($result->orders as $order)
			{
				// index 0: restaurant, index 1: take-away
				$index = (int) $order->group;

				// take highest ID of the current group
				$last_id[$index] = max([$last_id[$index], $order->id]);
			}

			// save latest IDs within the event configuration
			$this->set('last_id', $last_id);
		}

		// return only the list of orders
		return $result->orders;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		/**
		 * @todo translate
		 */
		return 'Pull Orders';
	}

	/**
	 * @inheritDoc
	 */
	public function getShortDescription()
	{
		/**
		 * @todo translate
		 */
		return 'Downloads a list containing all the take-away orders and the restaurant reservations that haven\'t been yet downloaded.';
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		/**
		 * Read the description HTML from a layout.
		 *
		 * @since 1.8
		 */
		return \JLayoutHelper::render('apis.plugins.pull_orders', ['plugin' => $this]);
	}
}
