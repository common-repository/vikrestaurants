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
 * VikRestaurants coupons view.
 *
 * @since 1.0
 */
class VikRestaurantsViewcoupons extends JViewVRE
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

		$filters = [];
		$filters['search']      = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['group']       = $app->getUserStateFromRequest($this->getPoolName() . '.group', 'group', '', 'string');
		$filters['type']        = $app->getUserStateFromRequest($this->getPoolName() . '.type', 'type', 0, 'uint');
		$filters['id_category'] = $app->getUserStateFromRequest($this->getPoolName() . '.id_category', 'id_category', '', 'string');

		// make sure the group is supported
		$filters['group'] = JHtml::fetch('vrehtml.admin.getgroup', $filters['group'], null, true);

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'c.id', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `c`.*')
			->from($dbo->qn('#__vikrestaurants_coupons', 'c'))
			->where(1)
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$q->where($dbo->qn('c.code') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}

		if (strlen((string) $filters['group']))
		{
			$q->where($dbo->qn('c.group') . ' = ' . (int) $filters['group']);
		}

		if ($filters['type'])
		{
			$q->where($dbo->qn('c.type') . ' = ' . (int) $filters['type']);
		}

		if (strlen($filters['id_category']))
		{
			$q->where($dbo->qn('c.id_category') . ' = ' . (int) $filters['id_category']);
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryCoupons" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		$this->onBeforeListQuery($q);

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

		$this->rows   = $rows;
		$this->navbut = $navbut;

		// set the toolbar
		$this->addToolBar();
		
		// display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	private function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWCOUPONS'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('coupon.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('coupon.edit');
		}

		if ($user->authorise('core.create', 'com_vikrestaurants') || $user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::link('index.php?option=com_vikrestaurants&view=couponcategories', JText::translate('VRMENUCATEGORIES'), 'pin');
		}

		if ($this->rows)
		{
			// display export button only if we have at least a record
			JToolbarHelper::custom('export', 'download', 'download', JText::translate('VREXPORT'), false);
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'coupon.delete');
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 *
	 * @since 	1.8
	 */
	protected function hasFilters()
	{
		return (strlen((string) $this->filters['group']) || $this->filters['type'] || strlen($this->filters['id_category']));
	}
}
