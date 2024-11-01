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

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants take-away items stock statistics controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerTkstatstocks extends VREControllerAdmin
{
	/**
	 * AJAX end-point used to retrieve some statistics about
	 * the total number of sold items.
	 *
	 * @return 	void
	 */
	public function getchartdata()
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		// check user permissions
		if (!JFactory::getUser()->authorise('core.access.tkorders', 'com_vikrestaurants'))
		{
			// raise error, not authorised to access take-away reservations data
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		$eid = $app->input->getUint('id_product', 0);
		$oid = $app->input->getUint('id_option', 0);

		$start_ts = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($app->input->get('start', '', 'string'), 0, 0);
		$end_ts   = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($app->input->get('end', '', 'string'), 23, 59);

		$q = $db->getQuery(true);

		$q->select($db->qn('e.id', 'eid'));
		$q->select($db->qn('e.name', 'ename'));
		$q->select($db->qn('o.id', 'oid'));
		$q->select($db->qn('o.name', 'oname'));

		// Make sure the UNIX timestamps are converted to the timezone
		// used by the server, so that the hours won't be shifted.
		$q->select(sprintf(
			'DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(%s), @@session.time_zone, \'%s\'), \'%%w\') AS %s',
			$db->qn('r.checkin_ts'),
			date('P'), // returns the string offset '+02:00'
			$db->qn('weekday')
		));

		// Make sure the UNIX timestamps are converted to the timezone
		// used by the server, so that the hours won't be shifted.
		$q->select(sprintf(
			'DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(%s), @@session.time_zone, \'%s\'), \'%%c\') AS %s',
			$db->qn('r.checkin_ts'),
			date('P'), // returns the string offset '+02:00'
			$db->qn('month')
		));

		// Make sure the UNIX timestamps are converted to the timezone
		// used by the server, so that the hours won't be shifted.
		$q->select(sprintf(
			'DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(%s), @@session.time_zone, \'%s\'), \'%%Y\') AS %s',
			$db->qn('r.checkin_ts'),
			date('P'), // returns the string offset '+02:00'
			$db->qn('year')
		));

		$q->select('SUM(' . $db->qn('i.quantity') . ') AS ' . $db->qn('products_used'));

		$q->from($db->qn('#__vikrestaurants_takeaway_reservation', 'r'));
		$q->leftjoin($db->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'i') . ' ON ' . $db->qn('i.id_res') . ' = ' . $db->qn('r.id'));
		$q->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $db->qn('i.id_product') . ' = ' . $db->qn('e.id'));
		$q->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('i.id_product_option') . ' = ' . $db->qn('o.id'));

		$q->where($db->qn('i.id_product') . ' = ' . $eid);

		if ($start_ts > 0)
		{
			$q->where($db->qn('r.checkin_ts') . ' >= ' . $start_ts);
		}

		if ($end_ts > 0)
		{
			$q->where($db->qn('r.checkin_ts') . ' <= ' . $end_ts);
		}

		$q->andWhere([
			$db->qn('i.id_product_option') . ' = ' . $oid,
			$oid . ' = 0',
		], 'OR');

		// get any reserved codes
		$reserved = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'reserved' => 1]);
		
		if ($reserved)
		{
			// filter orders by reserved status
			$q->where($db->qn('r.status') . ' IN (' . implode(',', array_map(array($db, 'q'), $reserved)) . ')');
		}

		$q->group($db->qn('weekday'));
		$q->group($db->qn('month'));
		$q->group($db->qn('year'));

		$q->order($db->qn('year') . ' ASC');
		$q->order($db->qn('month') . ' ASC');
		$q->order($db->qn('weekday') . ' ASC');

		$db->setQuery($q);
		$rows = $db->loadAssocList();

		if (!$rows)
		{
			// no relevant data
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(400, JText::translate('VRTKSTATSTOCKSNODATA'));
		}

		$tree = [
			'eid'      => $rows[0]['eid'],
			'oid'      => $rows[0]['oid'],
			'ename'    => $rows[0]['ename'],
			'oname'    => $rows[0]['oname'],
			'years'    => [],
			'months'   => [],
			'weekdays' => [],
			'children' => [],
		];

		$last_year = $last_month = -1;
		$year_node = $month_node = null;

		foreach ($rows as $r)
		{
			if ($r['year'] != $last_year)
			{
				// update node
				$tree['children'][$r['year']] = [
					'used'     => 0,
					'children' => [],
				];

				$year_node = &$tree['children'][$r['year']];

				$last_year = $r['year'];
			}

			if ($r['month'] != $last_month)
			{
				// update node
				$year_node['children'][$r['month']] = [
					'used'     => 0,
					'children' => [],
				];

				$month_node = &$year_node['children'][$r['month']];

				$last_month = $r['month'];
			}

			$month_node['children'][$r['weekday']] = $r['products_used'];
			
			$year_node['used']  += $r['products_used'];
			$month_node['used'] += $r['products_used'];

			// update root total
			if (empty($tree['years'][$r['year']]))
			{
				$tree['years'][$r['year']] = 0;
			}

			$tree['years'][$r['year']] += $r['products_used'];

			if (empty($tree['months'][$r['month']]))
			{
				$tree['months'][$r['month']] = 0;
			}

			$tree['months'][$r['month']] += $r['products_used'];

			if (empty($tree['weekdays'][$r['weekday']]))
			{
				$tree['weekdays'][$r['weekday']] = 0;
			}

			$tree['weekdays'][$r['weekday']] += $r['products_used'];
		}
		
		$this->sendJSON($tree);
	}
}
