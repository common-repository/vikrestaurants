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
 * VikRestaurants tags view.
 *
 * @since 1.8
 */
class VikRestaurantsViewtags extends JViewVRE
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

		$filters = [];
		$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['group']  = $app->getUserStateFromRequest($this->getPoolName() . '.group', 'group', '', 'string');

		// set the toolbar
		$this->addToolBar($filters['group']);

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 't.ordering', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut = "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS t.*')
			->from($dbo->qn('#__vikrestaurants_tag', 't'))
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['group'] == 'products')
		{
			$count = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikrestaurants_section_product', 'p'))
				->where([
					// only one tag
					$dbo->qn('p.tags') . ' = ' . $dbo->qn('t.name'),
					// tag in the middle
					$dbo->qn('p.tags') . ' LIKE CONCAT(\'%,\', ' . $dbo->qn('t.name') . ', \',%\')',
					// first tag available
					$dbo->qn('p.tags') . ' LIKE CONCAT(' . $dbo->qn('t.name') . ', \',%\')',
					// last tag available
					$dbo->qn('p.tags') . ' LIKE CONCAT(\'%,\', ' . $dbo->qn('t.name') . ')',
				], 'OR');
		}
		else
		{
			$count = null;
		}

		if ($count)
		{
			$q->select('(' . $count . ') AS ' . $dbo->qn('count'));
		}
		else
		{
			$q->select($dbo->q('/') . ' AS ' . $dbo->qn('count'));
		}

		if ($filters['search'])
		{
			$q->where($dbo->qn('t.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}

		if ($filters['group'])
		{
			$q->where($dbo->qn('t.group') . ' = ' . $dbo->q($filters['group']));
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryTags" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		$this->onBeforeListQuery($q);

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

		// assert limit used for list query
		$this->assertListQuery($lim0, $lim);

		if ($dbo->getNumRows() )
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
	 * @param 	string 	$group  The selected group.
	 *
	 * @return 	void
	 */
	private function addToolBar($group)
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWTAGS'), 'vikrestaurants');

		$user = JFactory::getUser();

		switch ($group)
		{
			case 'products':
				$view = 'menusproducts';
				break;

			default:
				$view = 'dashboard';
		}
		
		JToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_vikrestaurants&view=' . $view);
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('tag.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('tag.edit');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'tag.delete');
		}
	}
}
