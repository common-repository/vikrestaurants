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
 * VikRestaurants conditional texts view.
 *
 * @since 1.9
 */
class VikRestaurantsViewmailtexts extends JViewVRE
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

		$filters = [];
		$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'm.ordering', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		// db object
		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS m.*')
			->from($dbo->qn('#__vikrestaurants_mail_text', 'm'))
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if (strlen($filters['search']))
		{
			$q->where($dbo->qn('m.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryMailtexts" plugin event
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

		foreach ($rows as $i => $row)
		{
			// decode filters and actions
			$row['filters'] = $row['filters'] ? (array) json_decode($row['filters']) : [];
			$row['actions'] = $row['actions'] ? (array) json_decode($row['actions']) : [];

			// count filters and actions
			$row['filtersCount'] = count($row['filters']);
			$row['actionsCount'] = count($row['actions']);

			$rows[$i] = $row;
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
	protected function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolBarHelper::title(JText::translate('VRMAINTITLEVIEWMAILTEXTS'), 'vikrestaurants');

		$user = JFactory::getUser();

		JToolBarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_vikrestaurants&view=editconfig');
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolBarHelper::addNew('mailtext.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolBarHelper::editList('mailtext.edit');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolBarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'mailtext.delete');
		}
	}
}
