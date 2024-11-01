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
 * Widget class used to display an overview of the dishes
 * to prepare for the kitchen.
 *
 * @since 1.8
 * @since 1.9.1  The widget is available also for the take-away section.
 */
class VREStatisticsWidgetKitchen extends VREStatisticsWidget
{
	/**
	 * @override
	 * Returns the form parameters required to the widget.
	 *
	 * @return 	array
	 */
	public function getForm()
	{
		$form = [];

		/**
		 * Flag used to choose whether to show or not the
		 * outgoing courses next to the wall of bills.
		 *
		 * @var checkbox
		 */
		$form['outgoing'] = [
			'type'    => 'checkbox',
			'label'   => JText::translate('VRE_STATS_WIDGET_KITCHEN_OUTGOING_COURSES'),
			'default' => 1,
		];

		if ($this->isGroup('restaurant'))
		{
			/**
			 * Flag used to choose whether to show or not tables
			 * that haven't ordered yet anything.
			 *
			 * @var checkbox
			 *
			 * @since 1.8.1
			 */
			$form['emptybill'] = [
				'type'    => 'checkbox',
				'label'   => JText::translate('VRE_STATS_WIDGET_KITCHEN_EMPTY_BILL'),
				'help'    => JText::translate('VRE_STATS_WIDGET_KITCHEN_EMPTY_BILL_HELP'),
				'default' => 1,
			];
		}
			
		return $form;
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		if ($this->isGroup('takeaway'))
		{
			return JText::translate('VRE_STATS_WIDGET_KITCHEN_TAKEAWAY_DESC');
		}
		
		return JText::translate('VRE_STATS_WIDGET_KITCHEN_DESC');
	}

	/**
	 * @override
	 * Loads the dataset(s) that will be recovered asynchronously
	 * for being displayed within the widget.
	 *
	 * It is possible to return an array of records to be passed
	 * to a chart or directly the HTML to replace.
	 *
	 * @return 	mixed
	 */
	public function getData()
	{
		$db = JFactory::getDbo();

		// if we are in the front-end, make sure the
		// user is an operator (throws exception)
		if (JFactory::getApplication()->isClient('site'))
		{
			// import operator user helper
			VRELoader::import('library.operator.user');
			// Load operator details. In case the user is
			// not an operator, an exception will be thrown
			$operator = VREOperatorUser::getInstance();
		}
		else
		{
			$operator = null;
		}

		$now = VikRestaurants::now();

		$data = [];

		$data['filters'] = [];
		$data['filters']['outgoing']  = $this->getOption('outgoing');
		$data['filters']['emptybill'] = $this->getOption('emptybill');

		$data['reservations'] = [];
		$data['waitinglist']  = [];

		if ($this->isGroup('restaurant'))
		{
			$query = $this->getRestaurantReservationsQuery($operator);
		}
		else
		{
			$query = $this->getTakeAwayOrdersQuery($operator);
		}

		// check if the operator can see all the reservations
		if ($operator && !$operator->canSeeAll())
		{
			// check if the operator can self-assign reservations
			if ($operator->canAssign())
			{
				// retrieve reservations assigned to this operator and reservations
				// free of assignments
				$query->andWhere($db->qn('r.id_operator') . ' IN (0, ' . (int) $operator->get('id') . ')');
			}
			else
			{
				// retrieve only the reservations assigned to the operator
				$query->andWhere($db->qn('r.id_operator') . ' = ' . (int) $operator->get('id'));
			}
		}

		$db->setQuery($query);
		
		// group dishes by reservation ID
		foreach ($db->loadObjectList() as $dish)
		{
			// in case of an operator, make sure it can access the dish
			if (!$operator || !isset($dish->tags) || $operator->canSeeProduct($dish->tags))
			{
				if ($dish->status != 1)
				{
					// push dish within the reservations pool
					$pool = &$data['reservations'];
				}
				else
				{
					// push dish within the waiting list
					$pool = &$data['waitinglist'];
				}

				// create reservation record if not set
				if (!isset($pool[$dish->rid]))
				{
					$res = new stdClass;
					$res->id          = $dish->rid;
					$res->operator    = $dish->operator_name;
					$res->lastUpdate  = 0;
					$res->elapsedTime = 0;
					$res->dishes      = [];

					if ($this->isGroup('restaurant'))
					{
						$res->table = new stdClass;
						$res->table->id   = $dish->id_table;
						$res->table->name = $dish->table_name;

						$res->room = new stdClass;
						$res->room->id   = $dish->id_room;
						$res->room->name = $dish->room_name;
					}
					else
					{
						$checkin = $dish->checkin_ts;
						$route = $dish->route ? json_decode($dish->route) : null;

						if (!empty($route->duration) && $dish->service === 'delivery')
						{
							// subtract delivery time to check-in
							$checkin = strtotime('-' . $route->duration . ' seconds', $checkin);
						}

						$res->table = new stdClass;
						$res->table->id   = 0;
						$res->table->name = ($dish->purchaser_nominative ? $dish->purchaser_nominative . ' - ' : '')
							. date(VREFactory::getConfig()->get('timeformat'), $checkin);

						$res->service = JHtml::fetch('vikrestaurants.tkservice', $dish->service);

						// get status code
						$res->rescode = $dish->rescode_order;
						$res->code = JHtml::fetch('vikrestaurants.rescode', $res->rescode, 2, $res->id);
					}

					$pool[$dish->rid] = $res;
				}
			
				// add dish to reservation, if any
				if ($dish->id)
				{
					if ($this->isGroup('takeaway'))
					{
						$dish->name = $dish->product_name . ($dish->option_name ? ' - ' . $dish->option_name : '');

						if ($toppings = $this->getToppings($dish->id))
						{
							$dish->notes = (string) $dish->notes;

							$dish->notes .= '<ul>';

							foreach ($toppings as $topping)
							{
								$dish->notes .= '<li>' . $topping->title . ': <strong>' . $topping->str . '</strong></li>';
							}

							$dish->notes .= '</ul>';
						}
					}

					// get status code
					$dish->code = JHtml::fetch('vikrestaurants.rescode', $dish->rescode, 3, $dish->id);

					// check if the reservation code owns a creation date time
					if ($dish->code && !empty($dish->code->createdon))
					{
						// take the last update time
						$res->lastUpdate = max($res->lastUpdate, $dish->code->createdon);

						if ($res->lastUpdate)
						{
							$res->elapsedTime = floor(($now - $res->lastUpdate) / 60);
						}
					}

					$pool[$dish->rid]->dishes[] = $dish;
				}
			}
		}

		if (!$data['filters']['outgoing'])
		{
			// reset waiting list in case the widget does not need to display it
			$data['waitinglist'] = [];
		}

		// include a reference of this widget
		$data['widget'] = $this;

		// return overview layout
		return JLayoutHelper::render('statistics.widgets.kitchen.wall', $data);
	}

	/**
	 * Builds the database query to use to fetch the items belonging
	 * to the restaurant reservations.
	 * 
	 * @param   mixed  $operator
	 * 
	 * @return  object[]
	 * 
	 * @since   1.9.1
	 */
	protected function getRestaurantReservationsQuery($operator)
	{
		$db = JFactory::getDbo();

		// take all the approved statuses
		$approved = JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]);

		$now = VikRestaurants::now();

		$start = strtotime('00:00:00', $now);
		$end   = strtotime('23:59:59', $now);

		$query = $db->getQuery(true);

		if ($this->getOption('emptybill'))
		{
			// base the query on the reservation in order to retrieve also
			// the tables with empty bills
			$query->from($db->qn('#__vikrestaurants_reservation', 'r'));
			$query->join(
				'LEFT', $db->qn('#__vikrestaurants_res_prod_assoc', 'p') 
				. ' ON ' . $db->qn('p.id_reservation') . ' = ' . $db->qn('r.id')
				// exlcude the products that have been delivered
				. ' AND (' . $db->qn('p.status') . ' <> 2 OR ' . $db->qn('p.status') . ' IS NULL)'
			);
			
		}
		else
		{
			// base the query on the ordered products in order to retrieve
			// only the tables that already ordered something
			$query->from($db->qn('#__vikrestaurants_res_prod_assoc', 'p'));
			$query->join('INNER', $db->qn('#__vikrestaurants_reservation', 'r') . ' ON ' . $db->qn('p.id_reservation') . ' = ' . $db->qn('r.id'));

			// always exclude the products that have been delivered
			$query->where('(' . $db->qn('p.status') . ' <> 2 OR ' . $db->qn('p.status') . ' IS NULL)');
		}
		
		$query->select('p.*')
			->select($db->qn('i.tags'))
			->select($db->qn('r.id_table'))
			->select($db->qn('r.id', 'rid'))
			->select($db->qn('t.id_room'))
			->select($db->qn('t.name', 'table_name'))
			->select($db->qn('rm.name', 'room_name'))
			->select(sprintf(
				'CONCAT_WS(\' \', %s, %s) AS %s',
				$db->qn('o.firstname'),
				$db->qn('o.lastname'),
				$db->qn('operator_name')
			))
			->leftjoin($db->qn('#__vikrestaurants_section_product', 'i') . ' ON ' . $db->qn('p.id_product') . ' = ' . $db->qn('i.id'))
			->leftjoin($db->qn('#__vikrestaurants_table', 't') . ' ON ' . $db->qn('r.id_table') . ' = ' . $db->qn('t.id'))
			->leftjoin($db->qn('#__vikrestaurants_room', 'rm') . ' ON ' . $db->qn('t.id_room') . ' = ' . $db->qn('rm.id'))
			->leftjoin($db->qn('#__vikrestaurants_operator', 'o') . ' ON ' . $db->qn('r.id_operator') . ' = ' . $db->qn('o.id'))
			// take only the reservations in the nearly hours
			->where(array(
				'(' . $db->qn('r.checkin_ts') . ' - 3600 * 2) <= ' . $now,
				'(' . $db->qn('r.checkin_ts') . ' + 3600 * 3) >= ' . $now,
				// $db->qn('p.status') . ' IS NULL',
			))
			// or take the reservations with dishes under preparation
			->orWhere(array(
				$db->qn('p.status') . ' IS NOT NULL',
				$db->qn('p.status') . ' IN (0, 1)',
			), 'AND')
			// then make sure the reservation is within the current day,
			// the status is CONFIRMED and the bill is still open
			->andWhere([
				// exclude closures and children reservations
				$db->qn('r.closure') . ' = 0',
				$db->qn('r.id_parent') . ' <= 0',
				// take only approved reservations, if any status
				$approved ? $db->qn('r.status') . ' IN (' . implode(',', array_map([$db, 'q'], $approved)) . ')' : 1,
				// make sure the bill is not closed
				$db->qn('r.bill_closed') . ' = 0',
				// and the check-in is within the estabilished range (day)
				$db->qn('r.checkin_ts') . ' BETWEEN ' . $start . ' AND ' . $end,
			], 'AND')
			->order($db->qn('r.checkin_ts') . ' ASC')
			->order($db->qn('p.servingnumber') . ' ASC')
			->order($db->qn('p.id') . ' ASC');

		if ($operator && $operator->get('rooms'))
		{
			// take only the supported rooms (already comma-separated)
			$query->andWhere($db->qn('t.id_room') . ' IN (' . $operator->get('rooms') . ')');
		}

		return $query;
	}

	/**
	 * Builds the database query to use to fetch the items belonging
	 * to the take-away orders.
	 * 
	 * @param   mixed  $operator
	 * 
	 * @return  object[]
	 * 
	 * @since   1.9.1
	 */
	protected function getTakeAwayOrdersQuery($operator)
	{
		$db = JFactory::getDbo();

		// take all the approved statuses
		$approved = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]);

		$now = VikRestaurants::now();

		$start = strtotime('00:00:00', $now);
		$end   = strtotime('23:59:59', $now);

		$query = $db->getQuery(true);
		
		$query->select('p.*')
			// ->select($db->qn('i.tags'))
			->select($db->qn('r.id', 'rid'))
			->select($db->qn('r.checkin_ts'))
			->select($db->qn('r.rescode', 'rescode_order'))
			->select($db->qn('r.service'))
			->select($db->qn('r.route'))
			->select($db->qn('r.purchaser_nominative'))
			->select($db->qn('i.name', 'product_name'))
			->select($db->qn('io.name', 'option_name'))
			->select(sprintf(
				'CONCAT_WS(\' \', %s, %s) AS %s',
				$db->qn('o.firstname'),
				$db->qn('o.lastname'),
				$db->qn('operator_name')
			))
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'p'))
			->join('INNER', $db->qn('#__vikrestaurants_takeaway_reservation', 'r') . ' ON ' . $db->qn('p.id_res') . ' = ' . $db->qn('r.id'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'i') . ' ON ' . $db->qn('p.id_product') . ' = ' . $db->qn('i.id'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'io') . ' ON ' . $db->qn('p.id_product_option') . ' = ' . $db->qn('io.id'))
			->leftjoin($db->qn('#__vikrestaurants_operator', 'o') . ' ON ' . $db->qn('r.id_operator') . ' = ' . $db->qn('o.id'))
			// ignore the orders that have been completed
			->where('(' . $db->qn('r.current') . ' <> 0 OR ' . $db->qn('r.current') . ' IS NULL)')
			// always exclude the products that have been delivered
			->where('(' . $db->qn('p.status') . ' <> 2 OR ' . $db->qn('p.status') . ' IS NULL)')
			// take only the reservations in the nearly hours
			->where(array(
				'(' . $db->qn('r.checkin_ts') . ' - 3600 * 2) <= ' . $now,
				'(' . $db->qn('r.checkin_ts') . ' + 3600 * 3) >= ' . $now,
				// $db->qn('p.status') . ' IS NULL',
			))
			// or take the reservations with dishes under preparation
			->orWhere(array(
				$db->qn('p.status') . ' IS NOT NULL',
				$db->qn('p.status') . ' IN (0, 1)',
			), 'AND')
			// then make sure the reservation is within the current day,
			// the status is CONFIRMED and the bill is still open
			->andWhere([
				// take only approved reservations, if any status
				$approved ? $db->qn('r.status') . ' IN (' . implode(',', array_map([$db, 'q'], $approved)) . ')' : 1,
				/**
				 * @todo exclude the delivered orders
				 */
				// and the check-in is within the estabilished range (day)
				$db->qn('r.checkin_ts') . ' BETWEEN ' . $start . ' AND ' . $end,
			], 'AND')
			->order(sprintf(
				'IFNULL(%s, %s) ASC',
				$db->qn('r.preparation_ts'),
				$db->qn('r.checkin_ts')
			))
			->order($db->qn('p.id') . ' ASC');

		return $query;
	}

	/**
	 * Fetches the toppings assigned to the given item ID.
	 * 
	 * @param   int  $itemId
	 * 
	 * @return  array
	 * 
	 * @since   1.9.1
	 */
	protected function getToppings(int $itemId)
	{
		$db = JFactory::getDbo();

		$toppings = [];

		// recover item toppings
		$query = $db->getQuery(true)
			->select($db->qn('a.id_group'))
			->select($db->qn('a.id_topping'))
			->select($db->qn('a.units'))
			->select($db->qn('g.title', 'group_title'))
			->select($db->qn('t.name', 'topping_name'))
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc', 'a'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g') . ' ON ' . $db->qn('a.id_group') . ' = ' . $db->qn('g.id'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_topping', 't') . ' ON ' . $db->qn('a.id_topping') . ' = ' . $db->qn('t.id'))
			->where($db->qn('a.id_assoc') . ' = ' . $itemId);

		$db->setQuery($query);
		
		foreach ($db->loadObjectList() as $t)
		{
			if (!isset($toppings[$t->id_group]))
			{
				$group = new stdClass;
				$group->id    = $t->id_group;
				$group->title = $t->group_title;
				$group->str   = '';
				$group->list  = [];

				$toppings[$t->id_group] = $group;
			}

			if (!empty($t->id_topping))
			{
				$topping = new stdClass;
				$topping->id    = $t->id_topping;
				$topping->name  = $t->topping_name;
				$topping->units = $t->units;

				/**
				 * Concatenate units to topping name in case that
				 * value is higher than 1.
				 *
				 * @since 1.8.2
				 */
				if ($topping->units > 1)
				{
					$topping->name .= ' x' . $topping->units;
				}

				$toppings[$t->id_group]->list[] = $topping;

				// append topping name to group "str" property
				if ($toppings[$t->id_group]->str)
				{
					$toppings[$t->id_group]->str .= ', ';
				}
				
				$toppings[$t->id_group]->str .= $topping->name;
			}
		}

		return $toppings;
	}
}
