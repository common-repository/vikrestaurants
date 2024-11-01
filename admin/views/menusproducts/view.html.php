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
 * VikRestaurants menus products view.
 *
 * @since 1.4
 */
class VikRestaurantsViewmenusproducts extends JViewVRE
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

		$filters = array();
		$filters['search']  = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['id_menu'] = $app->getUserStateFromRequest($this->getPoolName() . '.id_menu', 'id_menu', 0, 'uint');
		$filters['status']  = $app->getUserStateFromRequest($this->getPoolName() . '.status', 'status', '', 'string');
		$filters['tag']     = $app->getUserStateFromRequest($this->getPoolName() . '.tag', 'tag', '', 'string');

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'p.ordering', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `p`.*')
			->from($dbo->qn('#__vikrestaurants_section_product', 'p'))
			->where(1)
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$q->where($dbo->qn('p.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}
		
		if (strlen($filters['status']))
		{
			if ($filters['status'] < 2)
			{
				// published/unpublished
				$q->where([
					$dbo->qn('p.hidden') . ' = 0',
					$dbo->qn('p.published') . ' = ' . (int) $filters['status'],
				]);
			}
			else
			{
				// hidden
				$q->where($dbo->qn('p.hidden') . ' = 1');
				// always unset menus filtering
				$filters['id_menu'] = 0;
			}
		}
		else
		{
			// all except for hidden products
			$q->where($dbo->qn('p.hidden') . ' = 0');
		}

		if ($filters['id_menu'])
		{
			$q->leftjoin($dbo->qn('#__vikrestaurants_section_product_assoc', 'a') . ' ON ' . $dbo->qn('a.id_product') . ' = ' . $dbo->qn('p.id'));
			$q->leftjoin($dbo->qn('#__vikrestaurants_menus_section', 's') . ' ON ' . $dbo->qn('a.id_section') . ' = ' . $dbo->qn('s.id'));
			$q->where($dbo->qn('s.id_menu') . ' = ' . $filters['id_menu']);
		}

		if ($filters['tag'])
		{
			$q->andWhere([
				// only one tag
				$dbo->qn('p.tags') . ' = ' . $dbo->q($filters['tag']),
				// tag in the middle
				$dbo->qn('p.tags') . ' LIKE ' . $dbo->q("%,{$filters['tag']},%"),
				// first tag available
				$dbo->qn('p.tags') . ' LIKE ' . $dbo->q("{$filters['tag']},%"),
				// last tag available
				$dbo->qn('p.tags') . ' LIKE ' . $dbo->q("%,{$filters['tag']}"),
			], 'OR');
		}

		$this->filters = $filters;
		
		/**
		 * It is possible to lean on the "onBeforeListQueryMenusproducts" plugin event
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
				'menusproduct',
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
		$this->addToolBar($filters['status']);
		
		// display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @param 	mixed  $status  The status filter set.
	 *
	 * @return 	void
	 */
	private function addToolBar($status)
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWMENUSPRODUCTS'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('menusproduct.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('menusproduct.edit');
			
			// status not "hidden"
			if ($status != 2)
			{
				JToolbarHelper::publishList('menusproduct.publish');
				JToolbarHelper::unpublishList('menusproduct.unpublish');
			}
		}

		if ($this->rows)
		{
			// display export button only if we have at least a record
			JToolbarHelper::custom('export', 'download', 'download', JText::translate('VREXPORT'), false);
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'menusproduct.delete');
		}

		if ($user->authorise('core.create', 'com_vikrestaurants') || $user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::link('index.php?option=com_vikrestaurants&view=tags&group=products', JText::translate('VRGOTOTAGS'), 'pin');
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
		return (strlen($this->filters['status']) || $this->filters['id_menu'] || $this->filters['tag']);
	}
}
