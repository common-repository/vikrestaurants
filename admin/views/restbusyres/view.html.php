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
 * VikRestaurants reservations busy table view.
 *
 * @since 1.7
 */
class VikRestaurantsViewrestbusyres extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app   = JFactory::getApplication();
		$input = $app->input;
		$dbo   = JFactory::getDbo();

		// force blank component layout
		$input->set('tmpl', 'component');
		
		$filters = [];
		$filters['date']     = $input->get('date', '', 'string');
		$filters['time']     = $input->get('time', '', 'string');
		$filters['interval'] = $app->getUserStateFromRequest($this->getPoolName() . '.interval', 'interval', 60, 'uint');
		$filters['id_room']  = $app->getUserStateFromRequest($this->getPoolName() . '.id_room', 'id_room', 0, 'uint');

		$this->filters = $filters;

		$time = explode(':', $filters['time']);

		if (count($time) < 2)
		{
			$time = array(0, 0);
		}

		$arr = getdate(VikRestaurants::createTimestamp($filters['date'], $time[0], $time[1]));

		$ts1 = mktime($arr['hours'], $arr['minutes'] - $filters['interval'], 0, $arr['mon'], $arr['mday'], $arr['year']);
		$ts2 = mktime($arr['hours'], $arr['minutes'] + $filters['interval'], 0, $arr['mon'], $arr['mday'], $arr['year']);

		// take all the approved statuses
		$approved = JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]);
		// take all the pending statuses
		$pending = JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'reserved' => 1, 'approved' => 0]);
			
		$rows = [];

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn([
			'r.id', 'r.sid', 'r.checkin_ts', 'r.people', 'r.status',
			'r.locked_until', 'r.bill_value', 'r.purchaser_nominative',
			'r.purchaser_mail', 'r.purchaser_prefix', 'r.purchaser_phone',
		]));

		$q->select($dbo->qn('t.name', 'table_name'));
		$q->select($dbo->qn('rm.name', 'room_name'));
		$q->select($dbo->qn('c.icon', 'code_icon'));
		$q->select($dbo->qn('c.code'));

		$q->from($dbo->qn('#__vikrestaurants_reservation', 'r'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_res_code', 'c') . ' ON ' . $dbo->qn('r.rescode') . ' = ' . $dbo->qn('c.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_table', 't') . ' ON ' . $dbo->qn('r.id_table') . ' = ' . $dbo->qn('t.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_room', 'rm') . ' ON ' . $dbo->qn('t.id_room') . ' = ' . $dbo->qn('rm.id'));

		$q->where($dbo->qn('r.checkin_ts') . ' BETWEEN ' . $ts1 . ' AND ' . $ts2);

		/**
		 * Exclude any children reservations.
		 * 
		 * @since 1.9
		 */
		$q->where($dbo->qn('r.id_parent') . ' = 0');

		$statusWhere = [];

		if ($approved)
		{
			// take all the confirmed reservations
			$statusWhere[] = $dbo->qn('r.status') . ' IN (' . implode(',', array_map([$dbo, 'q'], $approved)) . ')';
		}

		if ($pending)
		{
			// take all the pending reservations that can still be confirmed (locked_until in the future)
			$statusWhere[] = $dbo->qn('r.status') . ' IN (' . implode(',', array_map([$dbo, 'q'], $pending)) . ')' . ' AND ' . $dbo->qn('r.locked_until') . ' > ' . VikRestaurants::now();
		}

		if ($statusWhere)
		{
			// filter reservations by status
			$q->andWhere($statusWhere, 'OR');
		}

		if ($filters['id_room'])
		{
			$q->where($dbo->qn('rm.id') . ' = ' . $filters['id_room']);
		}

		$q->order($dbo->qn('r.checkin_ts') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadAssocList() as $row)
		{
			$row['tables'] = [$row['table_name']];
			$rows[$row['id']] = $row;
		}

		if ($rows)
		{
			/**
			 * Recover here the clustered tables.
			 * 
			 * @since 1.9
			 */
			$cluster = $dbo->getQuery(true)
				->select($dbo->qn(['ti.name', 'ri.id_parent']))
				->from($dbo->qn('#__vikrestaurants_reservation', 'ri'))
				->leftjoin($dbo->qn('#__vikrestaurants_table', 'ti') . ' ON ' . $dbo->qn('ri.id_table') . ' = ' . $dbo->qn('ti.id'))
				->where($dbo->qn('ri.id_parent') . ' IN (' . implode(',', array_keys($rows)) . ')');

			$dbo->setQuery($cluster);
			
			foreach ($dbo->loadObjectList() as $table)
			{
				$rows[$table->id_parent]['tables'][] = $table->name;
			}
		}

		$this->rows = array_values($rows);

		// display the template
		parent::display($tpl);
	}
}
