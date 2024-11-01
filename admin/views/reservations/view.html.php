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
 * VikRestaurants restaurant reservations view.
 *
 * @since 1.0
 */
class VikRestaurantsViewreservations extends JViewVRE
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
		$filters['date']        = $app->getUserStateFromRequest($this->getPoolName() . '.date', 'date', '', 'string');
		$filters['shift']       = $app->getUserStateFromRequest($this->getPoolName() . '.shift', 'shift', '', 'string');
		$filters['search']      = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');
		$filters['status']      = $app->getUserStateFromRequest($this->getPoolName() . '.status', 'status', '', 'string');
		$filters['id_room']     = $app->getUserStateFromRequest($this->getPoolName() . '.id_room', 'id_room', 0, 'uint');
		$filters['id_operator'] = $app->getUserStateFromRequest($this->getPoolName() . '.id_operator', 'id_operator', 0, 'uint');

		// this filter comes only from details info, when a shared table hosts more than one reservation
		$filters['ids']	= $app->getUserStateFromRequest($this->getPoolName() . '.ids', 'ids', [], 'uint');

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'r.id', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'DESC', 'string');

		$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$lim0 	= $this->getListLimitStart($filters + ['limit' => $lim]);
		$navbut	= "";

		$rows = [];

		/**
		 * Stop fetching the order status count within the main query because the time required to 
		 * execute the query increases by O(n^2).
		 */
		// $inner = $dbo->getQuery(true)
		// 	->select('COUNT(1)')
		// 	->from($dbo->qn('#__vikrestaurants_order_status', 'os'))
		// 	->where($dbo->qn('os.id_order') . ' = ' . $dbo->qn('r.id'))
		// 	->where($dbo->qn('os.group') . ' = 1');

		/**
		 * Stop fetching the clusters within the main query because the time required to 
		 * execute the query increases by O(n^2).
		 */
		// $cluster = $dbo->getQuery(true)
		// 	->select('GROUP_CONCAT(' . $dbo->qn('ti.name') . ')')
		// 	->from($dbo->qn('#__vikrestaurants_reservation', 'ri'))
		// 	->leftjoin($dbo->qn('#__vikrestaurants_table', 'ti') . ' ON ' . $dbo->qn('ri.id_table') . ' = ' . $dbo->qn('ti.id'))
		// 	->where($dbo->qn('ri.id_parent') . ' = ' . $dbo->qn('r.id'));

		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS r.*')
			->select($dbo->qn('t.name', 'table_name'))
			->select($dbo->qn('p.name', 'payment_name'))
			->select($dbo->qn('u.name', 'createdby_name'))
			->select([
				$dbo->qn('c.code'),
				$dbo->qn('c.icon', 'code_icon'),
			])
			// ->select('(' . $inner . ') AS ' . $dbo->qn('order_status_count'))
			// ->select('(' . $cluster . ') AS ' . $dbo->qn('cluster'))
			->from($dbo->qn('#__vikrestaurants_reservation', 'r'))
			->leftjoin($dbo->qn('#__vikrestaurants_table', 't') . ' ON ' . $dbo->qn('r.id_table') . ' = ' . $dbo->qn('t.id'))
			->leftjoin($dbo->qn('#__vikrestaurants_gpayments', 'p') . ' ON ' . $dbo->qn('r.id_payment') . ' = ' . $dbo->qn('p.id'))
			->leftjoin($dbo->qn('#__vikrestaurants_res_code', 'c') . ' ON ' . $dbo->qn('r.rescode') . ' = ' . $dbo->qn('c.id'))
			->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('r.created_by') . ' = ' . $dbo->qn('u.id'))
			->where(1)
			->order($dbo->qn($this->ordering) . ' ' . $this->orderDir);

		/**
		 * Always hide records that belong to a parent reservation
		 *
		 * @since 1.8
		 */
		$q->where($dbo->qn('r.id_parent') . ' = 0');

		/**
		 * In case the room should be displayed within the list
		 * join the rooms table.
		 *
		 * @since 1.8
		 */
		if (in_array('rname', VikRestaurants::getListableFields()))
		{
			$q->select($dbo->qn('rm.name', 'room_name'));
			$q->leftjoin($dbo->qn('#__vikrestaurants_room', 'rm') . ' ON ' . $dbo->qn('rm.id') . ' = ' . $dbo->qn('t.id_room'));
		}

		/**
		 * In case we are sorting the reservations by BILL VALUE or 
		 * PURCHASER NAME, we should hide all the CLOSURES as they
		 * might distort the first records (when ascending).
		 *
		 * @since 1.8
		 */
		if (in_array($this->ordering, ['r.bill_value', 'r.purchaser_nominative']))
		{
			// display only NON-CLOSURE reservations
			$q->where($dbo->qn('r.closure') . ' = 0');
		}
		
		if ($filters['date'])
		{
			$start = VikRestaurants::createTimestamp($filters['date'],  0,  0);
			$end   = VikRestaurants::createTimestamp($filters['date'], 23, 59);

			$q->where($dbo->qn('r.checkin_ts') . ' BETWEEN ' . $start . ' AND ' . $end);

			if ($filters['shift'])
			{
				// get shift time
				$time = explode('-', $filters['shift']);

				// Do not include MINUTES in query.
				// Make sure the UNIX timestamps are converted to the timezone
				// used by the server, so that the hours won't be shifted.
				$q->where(sprintf(
					'DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(%s), @@session.time_zone, \'%s\'), \'%%H\') BETWEEN %d AND %d',
					$dbo->qn('r.checkin_ts'),
					date('P'), // returns the string offset '+02:00'
					floor(($time[0] ?? 0) / 60),
					floor(($time[1] ?? 23) / 60)
				));
			}
		}
		
		if ($filters['search'])
		{
			$where = [
				$dbo->qn('r.purchaser_nominative') . ' LIKE ' . $dbo->q("%{$filters['search']}%"),
				$dbo->qn('r.purchaser_mail') . ' LIKE ' . $dbo->q("%{$filters['search']}%"),
			];

			/**
			 * Get rid of any white spaces to improve the search by phone number.
			 * 
			 * @since 1.9
			 */
			$where[] = sprintf(
				'REPLACE(%s, \' \', \'\') LIKE %s',				
				$dbo->qn('r.purchaser_phone'),
				$dbo->q('%' . preg_replace("/\s+/", '', $filters['search']) . '%')
			);

			/**
			 * Reverse the search key in order to try finding
			 * users by name even if it was wrote in the opposite way.
			 * If we searched by "John Smith", the system will search
			 * for "Smith John" too.
			 *
			 * @since 1.8
			 */
			$reverse = preg_split("/\s+/", $filters['search']);
			$reverse = array_reverse($reverse);
			$reverse = implode(' ', $reverse);

			$where[] = $dbo->qn('r.purchaser_nominative') . ' LIKE ' . $dbo->q("%{$reverse}%");

			/**
			 * It is now possible to search reservations by coupon code through
			 * the main key search input, as the coupon input has been removed.
			 *
			 * @since 1.8
			 */
			$where[] = $dbo->qn('r.coupon_str') . ' LIKE ' . $dbo->q("{$filters['search']}%");

			/**
			 * It is now possible to search reservations by ID/SID through
			 * the main key search input, as the ordnum input has been removed.
			 *
			 * @since 1.8
			 */
			if (preg_match("/^[A-Z0-9]{16,16}$/i", $filters['search']))
			{
				// alphanumeric string of 16 characters, we are probably searching for "SID"
				$where[] = $dbo->qn('r.sid') . ' = ' . $dbo->q($filters['search']);
			}
			else if (preg_match("/^\d+\-[A-Z0-9]{16,16}$/i", $filters['search']))
			{
				// we are probably searching for "ID" - "SID"
				$where[] = sprintf('CONCAT_WS(\'-\', %s, %s) = %s', $dbo->qn('r.id'), $dbo->qn('r.sid'), $dbo->q($filters['search']));
			}
			else if (preg_match("/^id:\s*(\d+)/i", $filters['search'], $match))
			{
				// we are searching by ID
				$where[] = $dbo->qn('r.id') . ' = ' . (int) $match[1];
			}

			$q->andWhere($where, 'OR');
		}

		if ($filters['status'])
		{
			if ($filters['status'] == 'CLOSURE')
			{
				$q->where($dbo->qn('r.closure') . ' = 1');
			}
			else
			{
				$q->where($dbo->qn('r.status') . ' = ' . $dbo->q($filters['status']));
				$q->where($dbo->qn('r.closure') . ' = 0');
			}
		}

		/**
		 * Filter reservations by selected room.
		 *
		 * @since 1.8
		 */
		if ($filters['id_room'])
		{
			$q->where($dbo->qn('t.id_room') . ' = ' . $filters['id_room']);
		}

		/**
		 * Filter reservations by selected operator.
		 *
		 * @since 1.8
		 */
		if ($filters['id_operator'])
		{
			$q->where($dbo->qn('r.id_operator') . ' = ' . $filters['id_operator']);
		}

		// Check if the IDs filter is not empty.
		// Also make sure that the first element is not equals to 0,
		// otherwise it would mean that we cleared the filters.
		if (count($filters['ids']) && $filters['ids'][0] > 0)
		{
			// reset WHERE
			$q->clear('where')->where($dbo->qn('r.id') . ' IN (' . implode(',', $filters['ids']) . ')');

			// reset filters
			foreach ($filters as $k => $v)
			{
				if ($k != 'ids')
				{
					$filters[$k] = '';
				}
			}
		}
		else
		{
			// unset IDs filter
			$this->filters['ids'] = null;
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryReservations" plugin event
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

		/**
		 * Speed up the loading process by fetching the clusters one by one.
		 * 
		 * @since 1.8.6
		 */
		foreach ($rows as $i => $row)
		{
			$cluster = $dbo->getQuery(true)
				->select($dbo->qn('ti.name'))
				->from($dbo->qn('#__vikrestaurants_reservation', 'ri'))
				->leftjoin($dbo->qn('#__vikrestaurants_table', 'ti') . ' ON ' . $dbo->qn('ri.id_table') . ' = ' . $dbo->qn('ti.id'))
				->where($dbo->qn('ri.id_parent') . ' = ' . $row['id']);

			$dbo->setQuery($cluster);
			
			$rows[$i]['cluster'] = array_merge([$row['table_name']], $dbo->loadColumn());

			$inner = $dbo->getQuery(true)
				->select('COUNT(1)')
				->from($dbo->qn('#__vikrestaurants_order_status', 'os'))
				->where($dbo->qn('os.id_order') . ' = ' . $row['id'])
				->where($dbo->qn('os.group') . ' = 1');

			$dbo->setQuery($inner);
			$rows[$i]['order_status_count'] = (int) $dbo->loadResult();
		}

		/**
		 * Retrieve custom fields for the restaurant section by using the related helper.
		 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
		 *
		 * @since 1.9
		 */
		$this->customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter)
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();
		
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
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWRESERVATION'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('reservation.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{	
			JToolbarHelper::editList('reservation.edit');
		}
			
		JToolbarHelper::custom('exportres.add', 'out', 'out', JText::translate('VREXPORT'), false, false);
		JToolbarHelper::link('index.php?option=com_vikrestaurants&view=statistics&group=restaurant', JText::translate('VRSTAT'), 'chart');
		JToolbarHelper::custom('printorders', 'print', 'print', JText::translate('VRPRINT'), true, false);

		if ($user->authorise('core.edit', 'com_vikrestaurants') || $user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolBarHelper::custom('invoice.generate', 'vcard', 'vcard', JText::translate('VRINVOICE'), true);
		}

		if ($this->isApiSmsConfigured())
		{
			JToolbarHelper::custom('reservation.sendsms', 'comment', 'comment', JText::translate('VRSENDSMS'), true);
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'reservation.delete');
		}
	}

	/**
	 * Checks whether the SMS provider has been configured.
	 *
	 * @return 	boolean
	 */
	protected function isApiSmsConfigured()
	{
		// first of all, check ACL
		if (!JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants'))
		{
			return false;
		}
		
		try
		{
			// try to instantiate the SMS API provider
			$provider = VREApplication::getInstance()->getSmsInstance();
		}
		catch (Exception $e)
		{
			// SMS provider not configured
			return false;
		}

		// provider (probably) configured
		return true;
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
		return ($this->filters['status']
			|| $this->filters['date']
			|| $this->filters['id_room']
			|| $this->filters['id_operator']);
	}
}
