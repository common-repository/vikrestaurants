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
 * VikRestaurants order history view.
 *
 * @since 1.8
 */
class VikRestaurantsVieworderhistory extends JViewVRE
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
		
		$filters['id']    = $input->get('id', 0, 'uint');
		$filters['group'] = $input->get('group', 1, 'uint');

		$this->filters = $filters;

		// always start from 10 records, due to the size of the blocks
		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', 10, 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `l`.*')
			->select($dbo->qn('o.code'))
			->select($dbo->qn('o.firstname'))
			->select($dbo->qn('o.lastname'))
			->from($dbo->qn('#__vikrestaurants_operator_log', 'l'))
			->leftjoin($dbo->qn('#__vikrestaurants_operator', 'o') . ' ON ' . $dbo->qn('o.id') . ' = ' . $dbo->qn('l.id_operator'))
			->where($dbo->qn('l.id_reservation') . ' = ' . $filters['id'])
			->where($dbo->qn('l.group') . ' = ' . $filters['group'])
			->order($dbo->qn('l.createdon') . ' DESC');

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);

		if ($dbo->getNumRows())
		{
			$rows = $dbo->loadAssocList();
			$dbo->setQuery('SELECT FOUND_ROWS();');
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($dbo->loadResult(), $lim0, $lim);
			$navbut = JLayoutHelper::render('blocks.pagination', ['pageNav' => $pageNav]);
		}

		// load reservation logs

		$paylog = [];

		$q = $dbo->getQuery(true);

		$q->select($dbo->qn('payment_log'));

		if ($filters['group'] == 1)
		{
			$q->from($dbo->qn('#__vikrestaurants_reservation'));
		}
		else
		{
			$q->from($dbo->qn('#__vikrestaurants_takeaway_reservation'));
		}

		$q->where($dbo->qn('id') . ' = ' . $filters['id']);

		$dbo->setQuery($q, 0, 1);
		
		$buffer = (string) $dbo->loadResult();

		// check if we have some logs
		if (preg_match_all("/-+\s+\|([a-zA-Z0-9\/\-\.: ]+)\|\s+-+/", $buffer, $match))
		{
			// split payment logs
			$chunks = preg_split("/-+\s+\|([a-zA-Z0-9\/\-\.: ]+)\|\s+-+/", $buffer);
			// pop first empty string
			array_shift($chunks);

			for ($i = 0; $i < count($chunks); $i++)
			{
				// create log record with date and content
				$tmp = [];
				$tmp['createdon'] = $match[1][$i];
				$tmp['log']       = trim(@$chunks[$i]);

				if (!preg_match("/<pre(.*?)>/", $tmp['log']))
				{
					// wrap log within <pre>
					$tmp['log'] = '<pre>' . $tmp['log'] . '</pre>';
				}

				$paylog[] = $tmp;
			}
		}
		
		$this->rows   = $rows;
		$this->payLog = $paylog;
		$this->navbut = $navbut;
		
		// display the template (default.php)
		parent::display($tpl);
	}
}
