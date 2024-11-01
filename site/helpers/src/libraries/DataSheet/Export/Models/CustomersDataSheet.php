<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Export\Models;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DataSheet\Models\DatabaseDataSheet;

/**
 * Creates a datasheet for the customers stored in the database.
 * 
 * @since 1.9
 */
#[\AllowDynamicProperties]
class CustomersDataSheet extends DatabaseDataSheet
{
	use \E4J\VikRestaurants\DataSheet\Helpers\CmsUserFormatter;
	use \E4J\VikRestaurants\DataSheet\Helpers\CountryFormatter;
	use \E4J\VikRestaurants\DataSheet\Helpers\CurrencyFormatter;

	/**
	 * The datasheet configuration.
	 * 
	 * @var \JRegistry
	 */
	protected $options;

	/**
	 * Class constructor.
	 * 
	 * @param  array|object     $options
	 * @param  JDatabaseDriver  $db
	 */
	public function __construct($options = [], $db = null)
	{
		$this->options = new \JRegistry($options);

		if ($db)
		{
			$this->db = $db;	
		}
		else
		{
			$this->db = \JFactory::getDbo();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		// Customers
		return \JText::translate('VRMENUCUSTOMERS');
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		$head = [];

		// ID
		$head[] = \JText::translate('VRMANAGECUSTOMER1');
		// Name
		$head[] = \JText::translate('VRMANAGECUSTOMER2');
		// E-Mail
		$head[] = \JText::translate('VRMANAGECUSTOMER3');
		// Phone
		$head[] = \JText::translate('VRMANAGECUSTOMER4');
		// User Account
		$head[] = \JText::translate('VRMANAGECUSTOMER12');
		// Country
		$head[] = \JText::translate('VRMANAGECUSTOMER5');
		// State / Province
		$head[] = \JText::translate('VRMANAGECUSTOMER6');
		// City
		$head[] = \JText::translate('VRMANAGECUSTOMER7');
		// Address
		$head[] = \JText::translate('VRMANAGECUSTOMER8');
		// Address 2
		$head[] = \JText::translate('VRMANAGECUSTOMER19');
		// ZIP Code
		$head[] = \JText::translate('VRMANAGECUSTOMER9');
		// Company Name
		$head[] = \JText::translate('VRMANAGECUSTOMER10');
		// Vat Number
		$head[] = \JText::translate('VRMANAGECUSTOMER11');
		// SSN / Fiscal Code
		$head[] = \JText::translate('VRMANAGECUSTOMER20');

		if (\VikRestaurants::isRestaurantEnabled())
		{
			// Reservations
			$head[] = \JText::translate('VRMANAGECUSTOMER18');
		}

		if (\VikRestaurants::isTakeAwayEnabled())
		{
			// Orders
			$head[] = \JText::translate('VRMANAGECUSTOMER21');
		}

		// Total Earning
		$head[] = \JText::translate('VRSTATISTICSTH4');
		// Notes
		$head[] = \JText::translate('VRMANAGECUSTOMERTITLE4');

		return $head;
	}

	/**
	 * @inheritDoc
	 */
	public function formatRow(object $record)
	{
		// calculate total earning
		$totalEarning = (float) ($record->restotal ?? 0) + (float) ($record->ordtotal ?? 0);

		// check whether we should display raw contents or not
		if ($this->options->get('raw', false) == false)
		{
			// create a clone of the record to avoid manipulating the
			// default object by reference
			$record = clone $record;

			// convert user ID into an username
			$record->jid = $this->toUsername((int) $record->jid);
			// convert ISO 3166 code into a country name
			$record->country_code = $this->toCountryName($record->country_code);
			// convert total earning into a currency
			$totalEarning = $this->toCurrency($totalEarning);
		}

		$row = [];

		// ID
		$row[] = (int) $record->id;
		// Name
		$row[] = $record->billing_name;
		// E-Mail
		$row[] = $record->billing_mail;
		// Phone
		$row[] = $record->billing_phone;
		// User Account
		$row[] = $record->jid;
		// Country
		$row[] = $record->country_code;
		// State / Province
		$row[] = $record->billing_state;
		// City
		$row[] = $record->billing_city;
		// Address
		$row[] = $record->billing_address;
		// Address 2
		$row[] = $record->billing_address_2;
		// ZIP Code
		$row[] = $record->billing_zip;
		// Company Name
		$row[] = $record->company;
		// Vat Number
		$row[] = $record->vatnum;
		// SSN / Fiscal Code
		$row[] = $record->ssn;

		if (\VikRestaurants::isRestaurantEnabled())
		{
			// Reservations
			$row[] = (int) $record->rescount;
		}

		if (\VikRestaurants::isTakeAwayEnabled())
		{
			// Orders
			$row[] = (int) $record->ordcount;
		}

		// Total Earning
		$row[] = $totalEarning;
		// Notes
		$row[] = $record->notes;

		return $row;
	}

	/**
	 * @inheritDoc
	 * 
	 * Replicates the list query used by the customers view in the back-end.
	 * 
	 * @todo Reuse the customers list model once it will be implemented.
	 */
	protected function getListQuery()
	{
		$app = \JFactory::getApplication();

		// define default filters
		$this->options->def('search', $app->getUserState('vrecustomers.search', ''));
		$this->options->def('country', $app->getUserState('vrecustomers.country', ''));

		// define default ordering
		$this->options->def('ordering', $app->getUserState('vrecustomers.ordering', 'u.id'));
		$this->options->def('direction', $app->getUserState('vrecustomers.orderdir', 'ASC'));
		
		$filters = [];
		$filters['search']  = $this->options->get('search', '');
		$filters['country'] = $this->options->get('country', '');

		$this->filters = $filters;

		$this->ordering = $this->options->get('ordering', '');
		$this->orderDir = $this->options->get('direction', '');

		$query = $this->db->getQuery(true)
			->select('u.*')
			->from($this->db->qn('#__vikrestaurants_users', 'u'))
			// hide customers with empty name
			->where($this->db->qn('u.billing_name') . ' <> ' . $this->db->q(''))
			->order($this->db->qn($this->ordering) . ' ' . $this->orderDir);

		// get any reserved codes (both restaurant and take-away for a better ease of use)
		$reserved = \JHtml::fetch('vrehtml.status.find', 'code', ['reserved' => 1]);

		// calculate reservations count
		$resCount = $this->db->getQuery(true)
			->select('COUNT(1)')
			->from($this->db->qn('#__vikrestaurants_reservation', 'r'))
			->where($this->db->qn('r.id_user') . ' = ' . $this->db->qn('u.id'));
		
		if ($reserved)
		{
			// filter by reserved status
			$resCount->where($this->db->qn('r.status') . ' IN (' . implode(',', array_map(array($this->db, 'q'), $reserved)) . ')');
		}

		/**
		 * @since 1.9  A reservation is owned by a customer also when the e-mail of the reservation matches the e-mail
		 *             of the customer (in this case, the reservation  must not be assigned to anyone).
		 *             (r.id_user = u.id OR (r.id_user <= 0 AND r.purchaser_mail = u.billing_mail))
		 */
		$resCount->andWhere([
			$this->db->qn('r.id_user') . ' > 0 AND ' . $this->db->qn('r.id_user') . ' = ' . $this->db->qn('u.id'),
			$this->db->qn('r.id_user') . ' <= 0 AND ' . $this->db->qn('r.purchaser_mail') . ' = ' . $this->db->qn('u.billing_mail'),
		], 'OR');

		if (\VikRestaurants::isRestaurantEnabled())
		{
			$query->select('(' . $resCount . ') AS ' . $this->db->qn('rescount'));

			/**
			 * @todo replicate this query in the customers view too
			 */
			$resTotal = clone $resCount;
			$resTotal->clear('select')->select('SUM(r.bill_value)');

			$query->select('(' . $resTotal . ') AS ' . $this->db->qn('restotal'));
		}

		// calculate orders count
		$resCount->clear('from')->from($this->db->qn('#__vikrestaurants_takeaway_reservation', 'r'));

		if (\VikRestaurants::isTakeAwayEnabled())
		{
			$query->select('(' . $resCount . ') AS ' . $this->db->qn('ordcount'));

			/**
			 * @todo replicate this query in the customers view too
			 */
			$ordTotal = clone $resCount;
			$ordTotal->clear('select')->select('SUM(r.total_to_pay)');

			$query->select('(' . $ordTotal . ') AS ' . $this->db->qn('ordtotal'));
		}

		if ($filters['search'])
		{
			$where = [
				$this->db->qn('u.billing_name') . ' LIKE ' . $this->db->q("%{$filters['search']}%"),
				$this->db->qn('u.billing_mail') . ' LIKE ' . $this->db->q("%{$filters['search']}%"),
			];

			/**
			 * Get rid of any white spaces to improve the search by phone number.
			 * 
			 * @since 1.9
			 */
			$where[] = sprintf(
				'REPLACE(%s, \' \', \'\') LIKE %s',				
				$this->db->qn('u.billing_phone'),
				$this->db->q('%' . preg_replace("/\s+/", '', $filters['search']) . '%')
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

			$where[] = $this->db->qn('u.billing_name') . ' LIKE ' . $this->db->q("%{$reverse}%");

			// search by address
			$where[] = $this->db->qn('u.billing_address') . ' LIKE ' . $this->db->q("%{$filters['search']}%");

			$query->andWhere($where, 'OR');
		}

		if (strlen($filters['country']))
		{
			$query->where($this->db->qn('u.country_code') . ' = ' . $this->db->q($filters['country']));
		}

		/**
		 * @todo replicate this query in the customers view too
		 */
		if ($ids = $this->options->get('cid', []))
		{
			// filter customers by ID
			$query->where($this->db->qn('u.id') . ' IN (' . implode(',', array_map('intval', (array) $ids)) . ')');
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryCustomers" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		\VREFactory::getEventDispatcher()->trigger('onBeforeListQueryCustomers', [&$query, $this]);
		
		return $query;
	}
}
