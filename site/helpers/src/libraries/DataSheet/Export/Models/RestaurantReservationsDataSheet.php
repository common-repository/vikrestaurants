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
use E4J\VikRestaurants\Helpers\DateHelper;

/**
 * Creates a datasheet for the restaurant reservations stored in the database.
 * 
 * @since 1.9
 */
#[\AllowDynamicProperties]
class RestaurantReservationsDataSheet extends DatabaseDataSheet
{
	use \E4J\VikRestaurants\DataSheet\Helpers\CurrencyFormatter;

	/**
	 * The datasheet configuration.
	 * 
	 * @var \JRegistry
	 */
	protected $options;

	/**
	 * This property tracks the totals that should be displayed within the footer.
	 * 
	 * @var array
	 */
	protected $totals = [];

	/** @var E4J\VikRestaurants\CustomFields\FieldsCollection */
	protected $customFields;

	/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
	protected $dispatcher;

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

		// create a collection containing all the displayable custom fields for the restaurant group
		$this->customFields = \E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new \E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new \E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true))
			->filter(new \E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter);

		// use the global event dispatcher
		$this->dispatcher = \VREFactory::getPlatform()->getDispatcher();
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		// Products
		return \JText::translate('VRMENURESERVATIONS');
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		$head = [];

		// Order Number
		$head[] = \JText::translate('VRMANAGERESERVATION1');
		// Order Key
		$head[] = \JText::translate('VRMANAGERESERVATION2');
		// Creation Date
		$head[] = \JText::translate('VRCREATEDON');
		// Check-in
		$head[] = \JText::translate('VRMANAGERESERVATION3');
		// People
		$head[] = \JText::translate('VRMANAGERESERVATION4');
		// Room
		$head[] = \JText::translate('VRMANAGETABLE4');
		// Table
		$head[] = \JText::translate('VRMANAGERESERVATION5');
		// Payment Method
		$head[] = \JText::translate('VRMANAGERESERVATION20');
		// Payment Charge
		$head[] = \JText::translate('VRMANAGETKRES30');
		// Total Net
		$head[] = \JText::translate('VRMANAGETKORDDISC2');
		// Total Taxes
		$head[] = \JText::translate('VRMANAGETKRES21');
		// Bill Value
		$head[] = \JText::translate('VRMANAGERESERVATION10');
		// Tip
		$head[] = \JText::translate('VRTIP');
		// Discount
		$head[] = \JText::translate('VRDISCOUNT');
		// Purchaser Nominative
		$head[] = \JText::translate('VRMANAGERESERVATION18');
		// Purchaser E-mail
		$head[] = \JText::translate('VRMANAGERESERVATION6');
		// Purchaser Phone
		$head[] = \JText::translate('VRMANAGERESERVATION16');
		// Coupon
		$head[] = \JText::translate('VRMANAGERESERVATION8');
		// Status
		$head[] = \JText::translate('VRMANAGERESERVATION12');
		// Notes
		$head[] = \JText::translate('VRMANAGERESERVATIONTITLE3');

		// iterate fields and push them within the head
		foreach ($this->customFields as $field)
		{
			// exclude custom fields that are already displayed by
			// using the purchaser information
			if ($field->get('rule') !== 'nominative'
				&& $field->get('rule') !== 'email'
				&& $field->get('rule') !== 'phone')
			{
				$head[] = \JText::translate($field->getName());
			}
		}


		// check if the items should be included
		if ($this->options->get('useitems', false))
		{
			// Items
			$head[] = \JText::translate('VRMANAGETKRES22');
		}

		/**
		 * Trigger event to allow the plugins to manipulate the heading of
		 * the datasheet. Here it is possible to attach new columns, detach
		 * existing columns and rearrange them. Notice that the same changes
		 * must be applied to the body of the datasheet, otherwise the columns
		 * might result shifted.
		 *
		 * @param   array       &$head    The datasheet head array.
		 * @param   \JRegistry  $options  The datasheet configuration.
		 *
		 * @return  void
		 *
		 * @since   1.8.5
		 * @since   1.9    Renamed from "onBuildHeadCSV".
		 */
		$this->dispatcher->trigger('onBuildHeadRestaurantReservationsDatasheet', [&$head, $this->options]);

		return $head;
	}

	/**
	 * @inheritDoc
	 */
	public function getBody()
	{
		// always reset totals
		$this->totals = [
			'gross'    => 0,
			'net'      => 0,
			'tax'      => 0,
			'payment'  => 0,
			'tip'      => 0,
			'discount' => 0,
		];

		return parent::getBody();
	}

	/**
	 * @inheritDoc
	 */
	public function formatRow(object $record)
	{
		// create a clone of the record to avoid manipulating the
		// default object by reference
		$record   = clone $record;
		$original = clone $record;

		// increase totals before formatting the values
		$this->totals['gross']    += $record->bill_value;
		$this->totals['net']      += $record->total_net;
		$this->totals['tax']      += $record->total_tax;
		$this->totals['payment']  += $record->payment_charge;
		$this->totals['tip']      += $record->tip_amount;
		$this->totals['discount'] += $record->discount_val;

		// check whether we should display raw contents or not
		if ($this->options->get('raw', false) == false)
		{
			// adjust the creation date to the user timezone
			$record->created_on = !DateHelper::isNull($record->created_on) ? \JHtml::fetch('date', date('Y-m-d H:i:s', $record->created_on), 'Y-m-d H:i:s') : '/';
			// adjust the check-in date to the user timezone
			$record->checkin_ts = !DateHelper::isNull($record->checkin_ts) ? \JHtml::fetch('date', date('Y-m-d H:i:s', $record->checkin_ts), 'Y-m-d H:i') : '/';
			// format payment charge
			$record->payment_charge = $this->toCurrency($record->payment_charge);
			// format total net as currency
			$record->total_net = $this->toCurrency($record->total_net);
			// format total tax as currency
			$record->total_tax = $this->toCurrency($record->total_tax);
			// format bill value as currency
			$record->bill_value = $this->toCurrency($record->bill_value);
			// format tip amount as currency
			$record->tip_amount = $this->toCurrency($record->tip_amount);
			// format discount value as currency
			$record->discount_val = $this->toCurrency($record->discount_val);
			// take the name of the status code
			$record->status = \JHtml::fetch('vrehtml.status.display', $record->status, 'plain');
			// remove HTML tags from notes
			$record->notes = strip_tags((string) $record->notes);

			// extract coupon details
			if (strlen((string) $record->coupon_str))
			{
				list($coupon_code, $coupon_amount, $coupon_type) = explode(';;', $record->coupon_str);
				$record->coupon_str = $coupon_code . ' : ' . ($coupon_type == 1 ? $coupon_amount . '%' : $this->toCurrency($coupon_amount)); 
			}
		}
		else
		{
			// use the ISO 8601 format for the creation date
			$record->created_on = !DateHelper::isNull($record->created_on) ? \JFactory::getDate(date('Y-m-d H:i:s', $record->created_on))->toISO8601() : null;
			// use the ISO 8601 format for the check-in date
			$record->checkin_ts = !DateHelper::isNull($record->checkin_ts) ? \JFactory::getDate(date('Y-m-d H:i:s', $record->checkin_ts))->toISO8601() : null;
			// display the room ID instead of its name
			$record->room_name = $record->id_room;
			// display the table ID instead of its name
			$record->table_name = $record->id_table;
			// display the payment ID instead of its name
			$record->payment_name = $record->id_payment;
		}

		$row = [];

		// Order Number
		$row[] = $record->id;
		// Order Key
		$row[] = $record->sid;
		// Creation Date
		$row[] = $record->created_on;
		// Check-in
		$row[] = $record->checkin_ts;
		// People
		$row[] = $record->people;
		// Room
		$row[] = $record->room_name;

		// Table
		if (!$record->cluster)
		{
			$row[] = $record->table_name;
		}
		else
		{
			// get list of all the booked tables
			$tables = explode(',', $record->cluster);
			// push original table in first position
			array_unshift($tables, $record->table_name);
			// join all the tables and push within the csv
			$row[] = implode(', ', $tables);
		}

		// Payment Method
		$row[] = $record->payment_name;
		// Payment Charge
		$row[] = $record->payment_charge;
		// Total Net
		$row[] = $record->total_net;
		// Total Taxes
		$row[] = $record->total_tax;
		// Bill Value
		$row[] = $record->bill_value;
		// Tip
		$row[] = $record->tip_amount;
		// Discount
		$row[] = $record->discount_val;
		// Purchaser Name
		$row[] = $record->purchaser_nominative;
		// Purchaser E-mail
		$row[] = $record->purchaser_mail;
		// Purchaser Phone
		$row[] = $record->purchaser_phone;
		// Coupon
		$row[] = $record->coupon_str;
		// Status
		$row[] = $record->status;
		// Notes
		$row[] = $record->notes;

		// decode stored CF data
		$cf_json = (array) json_decode($record->custom_f, true);

		// translate custom fields
		$cf_json = \VRCustomFields::translateObject($cf_json, $this->customFields);

		// add custom fields
		foreach ($this->customFields as $field)
		{
			// exclude custom fields that are already displayed by
			// using the purchaser information
			if ($field->get('rule') !== 'nominative'
				&& $field->get('rule') !== 'email'
				&& $field->get('rule') !== 'phone')
			{
				$row[] = $cf_json[$field->getName()] ?? '';
			}
		}

		// check if the items should be included
		if ($this->options->get('useitems', false))
		{
			// Item
			$row[] = $this->formatReservationItems($record->id);
		}

		/**
		 * Trigger event to allow the plugins to manipulate the row that
		 * is going to be added into the datasheet body. Here it is possible
		 * to attach new columns, detach existing columns and rearrange them.
		 * Notice that the same changes must be applied to the head of the
		 * datasheet, otherwise the columns might result shifted.
		 *
		 * @param 	array       &$row     The datasheet body row.
		 * @param   object      $data     The row fetched from the database.
		 * @param 	\JRegistry  $options  The datasheet configuration.
		 *
		 * @return  void
		 *
		 * @since   1.8.5
		 * @since   1.9    Renamed from "onBuildRowCSV".
		 */
		$this->dispatcher->trigger('onBuildBodyRestaurantReservationsDatasheet', [&$row, $original, $this->options]);

		return $row;
	}

	/**
	 * @inheritDoc
	 */
	public function getFooter()
	{
		$isRaw = $this->options->get('raw', false);

		// create an empty row for the footer
		$footer = array_fill(0, count($this->getHead()), '');

		$footer[8]  = $isRaw ? $this->totals['payment']  : $this->toCurrency($this->totals['payment']);
		$footer[9]  = $isRaw ? $this->totals['net']      : $this->toCurrency($this->totals['net']);
		$footer[10] = $isRaw ? $this->totals['tax']      : $this->toCurrency($this->totals['tax']);
		$footer[11] = $isRaw ? $this->totals['gross']    : $this->toCurrency($this->totals['gross']);
		$footer[12] = $isRaw ? $this->totals['tip']      : $this->toCurrency($this->totals['tip']);
		$footer[13] = $isRaw ? $this->totals['discount'] : $this->toCurrency($this->totals['discount']);

		/**
		 * Trigger event to allow the plugins to manipulate the footer of
		 * the datasheet. Here it is possible to attach new columns, detach
		 * existing columns and rearrange them. Notice that the same changes
		 * must be applied to the head of the datasheet, otherwise the columns
		 * might result shifted.
		 *
		 * @param   string[]    &$footer  The datasheet footer array.
		 * @param   \JRegistry  $options  The datasheet configuration.
		 *
		 * @return  void
		 *
		 * @since   1.8.5
		 * @since   1.9    Renamed from "onAfterBuildRowsCSV".
		 */
		$this->dispatcher->trigger('onBuildFooterRestaurantReservationsDatasheet', [&$footer, $this->options]);

		return $footer;
	}

	/**
	 * @inheritDoc
	 */
	protected function getListQuery()
	{
		$isRaw = (bool) $this->options->get('raw', false);

		$query = $this->db->getQuery(true);

		// select all reservation columns
		$query->select('r.*');
		$query->select($this->db->qn('gp.name', 'payment_name'));

		// load restaurant reservations
		$query->from($this->db->qn('#__vikrestaurants_reservation', 'r'));

		// get table details
		$query->select($this->db->qn('t.name', 'table_name'));
		$query->leftjoin($this->db->qn('#__vikrestaurants_table', 't') . ' ON ' . $this->db->qn('r.id_table') . ' = ' . $this->db->qn('t.id'));

		// get room details
		$query->select($this->db->qn('rm.id', 'id_room'));
		$query->select($this->db->qn('rm.name', 'room_name'));
		$query->leftjoin($this->db->qn('#__vikrestaurants_room', 'rm') . ' ON ' . $this->db->qn('t.id_room') . ' = ' . $this->db->qn('rm.id'));

		// DO NOT take closures
		$query->where($this->db->qn('r.closure') . ' = 0');

		// DO NOT take children reservations
		$query->where($this->db->qn('r.id_parent') . ' = 0');

		$cluster = $this->db->getQuery(true)
			->select('GROUP_CONCAT(' . $this->db->qn($isRaw ? 'ti.id' : 'ti.name') . ')')
			->from($this->db->qn('#__vikrestaurants_reservation', 'ri'))
			->leftjoin($this->db->qn('#__vikrestaurants_table', 'ti') . ' ON ' . $this->db->qn('ri.id_table') . ' = ' . $this->db->qn('ti.id'))
			->where($this->db->qn('ri.id_parent') . ' = ' . $this->db->qn('r.id'));

		// recover all assigned tables
		$query->select('(' . $cluster . ') AS ' . $this->db->qn('cluster'));

		// take all the approved statuses
		$approved = \JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]);

		/**
		 * Added support for payment name.
		 *
		 * @since 1.8.5
		 */
		$query->leftjoin($this->db->qn('#__vikrestaurants_gpayments', 'gp') . ' ON ' . $this->db->qn('r.id_payment') . ' = ' . $this->db->qn('gp.id'));

		if ($this->options->get('confirmed', false) && $approved)
		{
			// take only APPROVED records
			$query->where($this->db->qn('r.status') . ' IN (' . implode(',', array_map([$this->db, 'q'], $approved)) . ')');
		}

		// include records with checkin equals or higher than 
		// the specified starting date
		$from = $this->options->get('fromdate');

		if ($from && $from !== $this->db->getNullDate())
		{
			$query->where($this->db->qn('r.checkin_ts') . ' >= ' . \VikRestaurants::createTimestamp($from, 0, 0));
		}

		// include records with checkin equals or lower than 
		// the specified ending date
		$to = $this->options->get('todate');

		if ($to && $to !== $this->db->getNullDate())
		{
			$query->where($this->db->qn('r.checkin_ts') . ' <= ' . \VikRestaurants::createTimestamp($to, 23, 59));
		}

		// retrieve only the selected records, if any
		$ids = $this->options->get('cid', []);

		if ($ids)
		{
			$query->where($this->db->qn('r.id') . ' IN (' . implode(',', array_map('intval', $ids)) . ')');
		}

		// order by ascending checkin
		$query->order($this->db->qn('r.checkin_ts') . ' ASC');

		/**
		 * Trigger event to allow the plugins to manipulate the query used to retrieve
		 * a standard list of records.
		 *
		 * @param   mixed       &$query   The query string or a query builder object.
		 * @param   \JRegistry  $options  The datasheet configuration.
		 *
		 * @return  void
		 *
		 * @since   1.8.5
		 * @since   1.9    Renamed from "onBeforeListQueryExportCSV".
		 */
		$this->dispatcher->trigger('onBeforeListQueryRestaurantReservationsDatasheet', [&$query, $this->options]);
		
		return $query;
	}

	/**
	 * Loads the items of the provided reservation and formats them.
	 * 
	 * @param   int  $reservationId
	 * 
	 * @return  string
	 */
	private function formatReservationItems(int $reservationId)
	{
		$items = [];

		$query = $this->db->getQuery(true);

		// get item details
		$query->select($this->db->qn('i.name', 'item_name'));
		$query->select($this->db->qn('i.quantity', 'item_quantity'));
		$query->select($this->db->qn('i.gross', 'item_gross'));
		$query->from($this->db->qn('#__vikrestaurants_res_prod_assoc', 'i'));
		$query->where($this->db->qn('i.id_reservation') . ' = ' . $reservationId);

		$this->db->setQuery($query);

		foreach ($this->db->loadObjectList() as $item)
		{
			$items[] = sprintf(
				"%dx %s\t(%s)",
				$item->item_quantity,
				$item->item_name,
				$this->toCurrency((float) $item->item_gross)
			);
		}

		return implode("\r\n", $items);
	}
}
