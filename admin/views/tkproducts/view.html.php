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
 * VikRestaurants take-away menu entries view.
 *
 * @since 1.5
 */
class VikRestaurantsViewtkproducts extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();

		$filters = [];
		$filters['search']           = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['status']           = $app->getUserStateFromRequest($this->getPoolName() . '.status', 'status', '', 'string');
		$filters['id_takeaway_menu'] = $app->getUserStateFromRequest($this->getPoolName() . '.id_takeaway_menu', 'id_takeaway_menu', 0, 'uint');

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'e.ordering', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `e`.*')
			->from($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'e'))
			->where($dbo->qn('e.id_takeaway_menu') . ' = ' . $filters['id_takeaway_menu'])
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$q->where($dbo->qn('e.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}

		if (strlen($filters['status']))
		{
			$q->where($dbo->qn('e.published') . ' = ' . (int) $filters['status']);
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryTkproducts" plugin event
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
		
		if (VikRestaurants::isMultilanguage())
		{
			$translator = VREFactory::getTranslator();

			// find available translations
			$lang = $translator->getAvailableLang(
				'tkentry',
				array_map(function($row) {
					return $row['id'];
				}, $rows)
			);

			// assign languages found to the related elements
			foreach ($rows as $k => $row)
			{
				$rows[$k]['languages'] = isset($lang[$row['id']]) ? $lang[$row['id']] : array();
			}
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
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWTKPRODUCTS'), 'vikrestaurants');

		$user = JFactory::getUser();

		JToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_vikrestaurants&view=tkmenus');
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('tkentry.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('tkentry.edit');
			JToolbarHelper::publishList('tkentry.publish');
			JToolbarHelper::unpublishList('tkentry.unpublish');
		}

		if ($this->rows)
		{
			// display export button only if we have at least a record
			JToolbarHelper::custom('export', 'download', 'download', JText::translate('VREXPORT'), false);
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'tkentry.delete');
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
		return (strlen($this->filters['status']));
	}
}
