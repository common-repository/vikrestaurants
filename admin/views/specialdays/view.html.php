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
 * VikRestaurants special days view.
 *
 * @since 1.0
 */
class VikRestaurantsViewspecialdays extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		// set the toolbar
		$this->addToolBar();

		$filters = array();
		$filters['group']  = $app->getUserStateFromRequest($this->getPoolName() . '.group', 'group', 0, 'uint');
		$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');

		// make sure the group is supported
		$filters['group'] = JHtml::fetch('vrehtml.admin.getgroup', $filters['group'], array(1, 2), true);

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 's.group', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `s`.*')
			->from($dbo->qn('#__vikrestaurants_specialdays', 's'))
			->where(1)
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if ($this->ordering == 's.group')
		{
			// sort from the more important to the less one
			$q->order($dbo->qn('s.priority') . ' DESC');
			$q->order($dbo->qn('s.id') . ' DESC');
		}
		else if ($this->ordering != 's.id')
		{
			// always sort by checking the ID
			$q->order($dbo->qn('s.id') . ' ' . $this->orderDir);
		}

		if ($filters['group'] > 0)
		{
			$q->where($dbo->qn('s.group') . ' = ' . $filters['group']);
		}

		if ($filters['search'])
		{
			$q->where($dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}

		/**
		 * It is possible to lean on the "onBeforeListQuerySpecialdays" plugin event
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
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWSPECIALDAYS'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('specialday.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('specialday.edit');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'specialday.delete');
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
		return ($this->filters['group']);
	}
}
