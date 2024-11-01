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
 * Event used to return a list of restaurant reservation
 * and take-away order. It is possible to specify a
 * threshold in order to obtain only the reservations
 * with ID higher than the specified amounts.
 *
 * @since 1.7
 * @since 1.9  Renamed from GetOrdersList.
 */
class OrdersListPluginAPI extends Event
{
	/**
	 * @inheritDoc
	 */
	protected function execute(array $args, Response $response)
	{
		$app = \JFactory::getApplication();

		if (!$args)
		{	
			// no payload found, recover arguments from request
			$args['last_id'] = $app->input->get('last_id', [], 'uint');
		}

		if (empty($args['last_id']) || !is_array($args['last_id']) || count($args['last_id']) != 2)
		{
			// unset threshold
			$args['last_id'] = [0, 0];
		}

		// prepare client response
		$response->setStatus(1);

		$obj = new \stdClass;
		$obj->status = 1;
		$obj->orders = [];

		$db = \JFactory::getDbo();

		// fetch restaurant reservations
		$query = $db->getQuery(true)
			->select($db->qn([
				'id',
				'purchaser_nominative',
				'purchaser_mail',
				'created_on',
				'checkin_ts',
			]))
			->select('0 AS ' . $db->qn('group'))
			->from($db->qn('#__vikrestaurants_reservation'))
			/**
			 * Exclude closures and children reservations.
			 * 
			 * @since 1.9
			 */
			->where($db->qn('closure') . ' = 0')
			->where($db->qn('id_parent') . ' <= 0')
			->order($db->qn('id') . ' DESC');

		// get any approved codes
		if ($approved = \JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]))
		{
			// filter by approved status
			$query->where($db->qn('status') . ' IN (' . implode(',', array_map(array($db, 'q'), $approved)) . ')');
		}

		if ($args['last_id'][0])
		{
			// don't get orders already processed
			$query->where($db->qn('id') . ' > ' . (int) $args['last_id'][0]);
		}

		$db->setQuery($query);
		$obj->orders = $db->loadObjectList();

		if ($obj->orders)
		{
			// register response
			$response->appendContent('Restaurant reservations fetched: #' . count($obj->orders) . "\n");
		}

		// get takeaway orders
		$query = $db->getQuery(true)
			->select($db->qn([
				'id',
				'purchaser_nominative',
				'purchaser_mail',
				'created_on',
				'checkin_ts',
			]))
			->select('1 AS ' . $db->qn('group'))
			->from($db->qn('#__vikrestaurants_takeaway_reservation'))
			->order($db->qn('id') . ' DESC');

		// get any approved codes
		if ($approved = \JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]))
		{
			// filter by approved status
			$query->where($db->qn('status') . ' IN (' . implode(',', array_map(array($db, 'q'), $approved)) . ')');
		}

		if ($args['last_id'][1])
		{
			// don't get orders already processed
			$query->where($db->qn('id') . ' > ' . $args['last_id'][1]);
		}

		$db->setQuery($query);
		$orders = $db->loadObjectList();

		if ($orders)
		{
			// register response
			$response->appendContent('Take-Away orders fetched: #' . count($orders) . "\n");

			// merge reservations and orders
			$obj->orders = array_merge($obj->orders, $orders);
		}

		// sort orders by creation date DESC
		usort($obj->orders, function($a, $b)
		{
			return $b->created_on - $a->created_on;
		});

		/**
		 * Convert the timestamps into ISO 8601 dates.
		 * 
		 * @since 1.9
		 */
		foreach ($obj->orders as $order)
		{
			$order->creation = \JFactory::getDate(date('Y-m-d H:i:s', $order->created_on), $app->get('offset', 'UTC'))->toISO8601();
			$order->checkin  = \JFactory::getDate(date('Y-m-d H:i:s', $order->checkin_ts), $app->get('offset', 'UTC'))->toISO8601();
		}

		/**
		 * Let the application framework safely output the response.
		 *
		 * @since 1.8.4
		 */
		return $obj;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		/**
		 * @todo translate
		 */
		return 'Get Orders List';
	}

	/**
	 * @inheritDoc
	 */
	public function getShortDescription()
	{
		/**
		 * @todo translate
		 */
		return 'Download a list containing all the take-away orders and the restaurant reservations.';
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
		return \JLayoutHelper::render('apis.plugins.get_orders_list', ['plugin' => $this]);
	}
}
