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
 * VikRestaurants reservations codes view.
 *
 * @since 1.7
 */
class VikRestaurantsViewrescodesorder extends JViewVRE
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
		$filters['id_order'] = $app->input->get('id_order', 0, 'uint');
		$filters['group']    = $app->input->get('group', 1, 'uint');

		// make sure the group is supported
		$filters['group'] = JHtml::fetch('vrehtml.admin.getgroup', $filters['group'], [1, 2, 3]);

		$this->filters = $filters;

		// set the toolbar
		$this->addToolBar();

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'os.createdon', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'DESC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS os.*')
			->select($dbo->qn('rc.code'))
			->select($dbo->qn('rc.icon'))
			->select($dbo->qn('rc.notes', 'code_notes'))
			->select($dbo->qn('u.name', 'user_name'))
			->from($dbo->qn('#__vikrestaurants_order_status', 'os'))
			->leftjoin($dbo->qn('#__vikrestaurants_res_code', 'rc') . ' ON ' . $dbo->qn('rc.id') . ' = ' . $dbo->qn('os.id_rescode'))
			->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('os.createdby'))
			->where($dbo->qn('os.id_order') . ' = ' . $filters['id_order'])
			->where($dbo->qn('os.group') . ' = ' . $filters['group'])
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		/**
		 * It is possible to lean on the "onBeforeListQueryRescodesorder" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		$this->onBeforeListQuery($q);

		$dbo->setQuery($q, $lim0, $lim);
		$dbo->execute();

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
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWRESCODESORDER'), 'vikrestaurants');

		$user = JFactory::getUser();

		JToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_vikrestaurants&view=' . ($this->filters['group'] == 1 ? 'reservations' : 'tkreservations'));
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('rescodeorder.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('rescodeorder.edit');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'rescodeorder.delete');	
		}
	}
}
