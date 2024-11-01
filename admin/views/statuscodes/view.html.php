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
 * VikRestaurants status codes view.
 *
 * @since 1.9
 */
class VikRestaurantsViewstatuscodes extends JViewVRE
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
		$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['group']  = $app->getUserStateFromRequest($this->getPoolName() . '.group', 'group', '', 'string');	

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 's.ordering', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'ASC', 'string');

		// db object
		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		$q = $dbo->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS s.*')
			->from($dbo->qn('#__vikrestaurants_status_code', 's'))
			->where(1)
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		if (strlen($filters['search']))
		{
			$q->andWhere([
				$dbo->qn('s.name') . ' LIKE ' . $dbo->q("%{$filters['search']}%"),
				$dbo->qn('s.description') . ' LIKE ' . $dbo->q("%{$filters['search']}%"),
			], 'OR');
		}

		if ($filters['group'])
		{
			$q->where($dbo->qn('s.' . $filters['group']) . ' = 1');
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryStatuscodes" plugin event
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
				'statuscode',
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

		$model = JModelVRE::getInstance('statuscode');

		// run some tests to make sure the status codes are properly configured
		if ($model->runTests() === false)
		{
			// display all the error messages
			foreach ($model->getErrors() as $error)
			{
				$app->enqueueMessage($error, 'error');
			}
		}
		
		$this->rows   = $rows;
		$this->navbut = $navbut;
		
		// Display the template (default.php)
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
		JToolBarHelper::title(JText::translate('VRMAINTITLEVIEWSTATUSCODES'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolBarHelper::addNew('statuscode.add', JText::translate('VRNEW'));
			JToolBarHelper::divider();	
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolBarHelper::editList('statuscode.edit', JText::translate('VREDIT'));
			JToolBarHelper::spacer();
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolBarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'statuscode.delete', JText::translate('VRDELETE'));
		}

		if ($user->authorise('core.admin', 'com_vikrestaurants'))
		{
			JToolBarHelper::custom('statuscode.restore', 'loop', 'loop', JText::translate('VRMAPGPRESTOREBUTTON'), false);
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['group']);
	}
}
