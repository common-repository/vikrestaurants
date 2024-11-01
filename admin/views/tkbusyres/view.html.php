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
class VikRestaurantsViewtkbusyres extends JViewVRE
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
		$approved = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]);
		// take all the pending statuses
		$pending = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'reserved' => 1, 'approved' => 0]);
		
		$rows = [];

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn([
			'r.id', 'r.sid', 'r.checkin_ts', 'r.preparation_ts', 'r.total_to_pay', 'r.status', 'r.locked_until', 'r.service', 'r.route', 
			'r.purchaser_nominative', 'r.purchaser_mail', 'r.purchaser_prefix', 'r.purchaser_phone', 'r.purchaser_address'
		]));

		$q->select($dbo->qn('c.icon', 'code_icon'));
		$q->select($dbo->qn('c.code'));

		$q->from($dbo->qn('#__vikrestaurants_takeaway_reservation', 'r'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_res_code', 'c') . ' ON ' . $dbo->qn('r.rescode') . ' = ' . $dbo->qn('c.id'));

		$q->where(1);

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

		$q->andWhere(array(
			$dbo->qn('r.checkin_ts') . ' BETWEEN ' . $ts1 . ' AND ' . $ts2,
			'(' .
				$dbo->qn('r.preparation_ts') . ' IS NOT NULL' . ' AND ' . 
				$dbo->qn('r.preparation_ts') . ' BETWEEN ' . $ts1 . ' AND ' . $ts2 .
			')',
		), 'OR');

		$q->order($dbo->qn('r.checkin_ts') . ' ASC');

		// count order items
		$itemsCount = $dbo->getQuery(true)
			->select('SUM(' . $dbo->qn('i.quantity') . ')')
			->from($dbo->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'i'))
			->where($dbo->qn('i.id_res') . ' = ' . $dbo->qn('r.id'));

		$q->select('(' . $itemsCount . ') AS ' . $dbo->qn('items_count'));

		// count order items that requires a preparation
		$prepCount = $dbo->getQuery(true)
			->select('SUM(' . $dbo->qn('i.quantity') . ')')
			->from($dbo->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'i'))
			->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $dbo->qn('i.id_product') . ' = ' . $dbo->qn('e.id'))
			->where($dbo->qn('i.id_res') . ' = ' . $dbo->qn('r.id'))
			->where($dbo->qn('e.ready') . ' = 0');

		$q->select('(' . $prepCount . ') AS ' . $dbo->qn('items_preparation_count'));

		$dbo->setQuery($q);
		$this->rows = $dbo->loadAssocList();

		/** @var array (associative) */
		$this->services = E4J\VikRestaurants\CustomFields\Factory::getSupportedServices();

		// display the template
		parent::display($tpl);
	}
}
