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
 * VikRestaurants all orders list view model.
 *
 * @since 1.9
 */
class VikRestaurantsModelAllorders extends JModelVRE
{
	/**
	 * The list view pagination object.
	 *
	 * @var JPagination[]
	 */
	protected $pagination = [];

	/**
	 * The total number of fetched rows.
	 *
	 * @var int[]
	 */
	protected $total = [];

	/**
	 * Loads a list of restaurant reservations and take-away orders to be displayed
	 * within the all orders site view.
	 *
	 * @param   string  $group     The group to use ("restaurant" or "takeaway").
	 * @param   array   &$options  An array of options.
	 *
	 * @return  array  A list of orders.
	 */
	public function getItems(string $group, array &$options = [])
	{
		// always reset pagination and total count
		$this->pagination[$group] = null;
		$this->total[$group]      = 0;

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = VREFactory::getPlatform()->getDispatcher();

		// get currently logged in user
		$user = JFactory::getUser();

		if ($user->guest)
		{
			// do not load orders assigned to guest user
			return [];
		}

		if (!array_key_exists('start', $options))
		{
			// start from the beginning
			$options['start'] = 0;
		}

		if (!array_key_exists('limit', $options))
		{
			// use default number of items
			$options['limit'] = 5;
		}

		$db = JFactory::getDbo();

		$items = [];

		$query = $db->getQuery(true);

		$query->select('SQL_CALC_FOUND_ROWS ' . $db->qn('r.id'));

		if ($group === 'restaurant')
		{
			// fetch restaurant reservations
			$query->from($db->qn('#__vikrestaurants_reservation', 'r'));

			// ignore closures and children reservations
			$query->where([
				$db->qn('r.closure') . ' = 0',
				$db->qn('r.id_parent') . ' <= 0',
			]);
		}
		else
		{
			// fetch take-away orders
			$query->from($db->qn('#__vikrestaurants_takeaway_reservation', 'r'));
		}

		$query->leftjoin($db->qn('#__vikrestaurants_users', 'u') . ' ON ' . $db->qn('r.id_user') . ' = ' . $db->qn('u.id'));
		
		// filter reservations by user
		$query->where($db->qn('u.jid') . ' = ' . (int) $user->id);

		// latest reservations come first
		$query->order($db->qn('r.id') . ' DESC');

		/**
		 * Trigger hook to manipulate the query at runtime. Third party plugins
		 * can extend the query by applying further conditions or selecting
		 * additional data.
		 *
		 * @param   mixed   &$query    Either a query builder or a query string.
		 * @param   string  $group     The group to use ("restaurant" or "takeaway").
		 * @param   array   &$options  An array of options.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onBuildAllOrdersQuery', [&$query, $group, &$options]);
		
		$db->setQuery($query, $options['start'], $options['limit']);
		$rows = $db->loadColumn();

		if ($rows)
		{
			// fetch pagination
			$pag = $this->getPagination($group, $options);

			$tag = JFactory::getLanguage()->getTag();

			foreach ($rows as $id_order)
			{
				if ($group === 'restaurant')
				{
					// get restaurant reservation details
					$items[] = VREOrderFactory::getReservation($id_order, $tag);
				}
				else
				{
					// get take-away order details
					$items[] = VREOrderFactory::getOrder($id_order, $tag);
				}
			}
		}

		/**
		 * Trigger hook to manipulate the query response at runtime. Third party
		 * plugins can alter the resulting list of orders.
		 *
		 * @param   array   &$items  An array of fetched orders.
		 * @param   string  $group   The group to use ("restaurant" or "takeaway").
		 * @param   JModel  $model   The current model.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$dispatcher->trigger('onBuildAllOrdersData', [&$items, $group, $this]);

		return $items;
	}

	/**
	 * Returns the list pagination.
	 *
	 * @param   string  $group    The group to use ("restaurant" or "takeaway").
	 * @param   array   $options  An array of options.
	 *
	 * @return  JPagination
	 */
	public function getPagination(string $group, array $options = [])
	{
		if (!isset($this->pagination[$group]))
		{
			jimport('joomla.html.pagination');
			$db = JFactory::getDbo();
			$db->setQuery('SELECT FOUND_ROWS();');
			$this->total[$group] = (int) $db->loadResult();

			$this->pagination[$group] = new JPagination($this->total[$group], $options['start'], $options['limit'], $group);
		}

		return $this->pagination[$group];
	}

	/**
	 * Returns the total number of employees matching the search query.
	 * 
	 * @param   string  $group  The group to use ("restaurant" or "takeaway").
	 *
	 * @return 	int
	 */
	public function getTotal(string $group)
	{
		return $this->total[$group] ?? 0;
	}
}
