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
 * VikRestaurants custom fields view.
 *
 * @since 1.0
 */
class VikRestaurantsViewcustomf extends JViewVRE
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

		// set the toolbar
		$this->addToolBar();

		$filters = [];
		$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['group']  = $app->getUserStateFromRequest($this->getPoolName() . '.group', 'group', 0, 'uint');
		$filters['type']   = $app->getUserStateFromRequest($this->getPoolName() . '.type', 'type', '', 'string');
		$filters['rule']   = $app->getUserStateFromRequest($this->getPoolName() . '.rule', 'rule', '', 'string');
		$filters['status'] = $app->getUserStateFromRequest($this->getPoolName() . '.status', 'status', '', 'string');

		// make sure the group is supported
		$filters['group'] = JHtml::fetch('vrehtml.admin.getgroup', $filters['group']);

		if ($filters['group'] == 1)
		{
			// retrieve service only in case of take-away group
			$filters['service'] = $app->getUserStateFromRequest($this->getPoolName() . '.service', 'service', '', 'string');
		}
		else
		{
			// use an empty string to avoid PHP warnings
			$filters['service'] = '';
		}

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'f.ordering', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS `f`.*')
			->from($dbo->qn('#__vikrestaurants_custfields', 'f'))
			->where($dbo->qn('f.group') . ' = ' . (int) $filters['group'])
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$q->where($dbo->qn('f.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"));
		}

		if ($filters['type'])
		{
			$q->where($dbo->qn('f.type') . ' = ' . $dbo->q($filters['type']));
		}

		if ($filters['rule'])
		{
			$q->where($dbo->qn('f.rule') . ' = ' . $dbo->q($filters['rule']));
		}

		if ($filters['service'])
		{
			$q->where($dbo->qn('f.service') . ' = ' . $dbo->q($filters['service']));
		}

		if (strlen($filters['status']))
		{
			$q->where($dbo->qn('f.required') . ' = ' . (int) $filters['status']);
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryCustomf" plugin event
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
				'custfield',
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

		/** @var array (associative) */
		$this->types = E4J\VikRestaurants\CustomFields\Factory::getSupportedTypes();

		/** @var array (associative) */
		$this->rules = E4J\VikRestaurants\CustomFields\Factory::getSupportedRules();

		/** @var array (associative) */
		$this->services = E4J\VikRestaurants\CustomFields\Factory::getSupportedServices();
		
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
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWCUSTOMFS'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('customf.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('customf.edit');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'customf.delete');
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
		return ($this->filters['type']
			|| $this->filters['rule']
			|| $this->filters['service']
			|| strlen($this->filters['status']));
	}
}
