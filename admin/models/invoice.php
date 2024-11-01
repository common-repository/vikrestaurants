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
 * VikRestaurants invoice model.
 *
 * @since 1.9
 */
class VikRestaurantsModelInvoice extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return 	mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		// load item through parent
		$item = parent::getItem($pk, $new);

		if ($item->inv_number)
		{
			// split invoice number
			list($item->number, $item->suffix) = explode('/', $item->inv_number);
		}
		else
		{
			// unset invoice number and suffix
			$item->number = $item->suffix = null;
		}

		return $item;
	}

	/**
	 * Generates the invoices in mass.
	 *
	 * @param 	mixed  $data  Either an array or an object of data to save.
	 *
	 * @return 	array  An array of imported rows on success.
	 */
	public function saveMass($data)
	{
		$db = JFactory::getDbo();

		$generated = 0;
		$notified  = 0;

		// retrieve all reservation/orders
		$query = $db->getQuery(true);
		
		// select only the ID
		$query->select($db->qn('id'));

		// load the correct table
		if ($data['group'] == 0)
		{
			// restaurant reservations
			$query->from($db->qn('#__vikrestaurants_reservation'));

			// get any approved codes
			$approved = JHtml::fetch('vrehtml.status.find', 'code', ['restaurant' => 1, 'approved' => 1]);

			// exclude any closure
			$query->where($db->qn('closure') . ' = 0');
			// take only the reservations without a parent
			$query->where($db->qn('id_parent') . ' <= 0');
		}
		else
		{
			// take-away orders
			$query->from($db->qn('#__vikrestaurants_takeaway_reservation'));

			// get any approved codes
			$approved = JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'approved' => 1]); 
		}

		if (!empty($data['cid']))
		{
			// get specified orders
			$query->where($db->qn('id') . ' IN (' . implode(',', array_map('intval', $data['cid'])) . ')');
		}
		else
		{
			// create range of dates
			$start = mktime(0, 0, 0, $data['month'], 1, $data['year']);
			$end   = mktime(0, 0, 0, $data['month'] + 1, 1, $data['year']);

			// get orders with creation date in the specified month
			$query->where($db->qn('created_on') . ' >= ' . $start);
			$query->where($db->qn('created_on') . ' < ' . $end);
		}

		if ($approved)
		{
			// filter by approved status
			$query->where($db->qn('status') . ' IN (' . implode(',', array_map(array($db, 'q'), $approved)) . ')');
		}

		// order by ascending date
		$query->order($db->qn('created_on') . ' ASC');

		$db->setQuery($query);
		
		// generate invoices one by one
		foreach ($db->loadColumn() as $orderId)
		{
			// reset ID
			$data['id'] = 0;
			// specify order ID
			$data['id_order'] = $orderId;

			if ($this->save($data))
			{
				// update generated count on success
				$generated++;

				if ($this->isNotified())
				{
					// increase notified count in case the invoice was sent to the customer
					$notified++;
				}
			}
		}

		return [
			'generated' => $generated,
			'notified'  => $notified,
		];
	}

	/**
	 * Basic save implementation.
	 *
	 * @param 	mixed  $data  Either an array or an object of data to save.
	 *
	 * @return 	mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$db = JFactory::getDbo();

		$data = (array) $data;

		if (empty($data['id']))
		{
			if (empty($data['id_order']) || !isset($data['group']))
			{
				// ID order is mandatory when creating an invoice
				$this->setError('Missing Order ID');
				return false;
			}

			// check if there is already an invoice for the given order
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn('#__vikrestaurants_invoice'))
				->where($db->qn('id_order') . ' = ' . (int) $data['id_order'])
				->where($db->qn('group') . ' = ' . (int) $data['group']);

			$db->setQuery($query, 0, 1);
			$data['id'] = (int) $db->loadResult();
		}

		if (!empty($data['id']) && empty($data['id_order']))
		{
			// retrieve order ID of the stored invoice
			$query = $db->getQuery(true)
				->select($db->qn('id_order'))
				->from($db->qn('#__vikrestaurants_invoice'))
				->where($db->qn('id') . ' = ' . (int) $data['id']);

			$db->setQuery($query, 0, 1);
			$data['id_order'] = (int) $db->loadResult();

			if (!$data['id_order'])
			{
				// invoice not found, abort
				$this->setError(sprintf('Invoice [%d] not found', $data['id']));
				return false;
			}
		}

		if ($data['id'] && empty($data['overwrite']))
		{
			// do not overwrite existing record (error not needed)
			return false;
		}

		// generate invoice and obtain resulting data
		$invoice = $this->generateInvoice($data);

		if (!$invoice)
		{
			return false;
		}

		// inject found data into the array to bind
		$data = array_merge($data, $invoice);

		// attempt to save the invoice
		$id = parent::save($data);

		if (!$id)
		{
			// an error occurred, do not go ahead
			return false;
		}

		// get generator instance
		$generator = $this->createGenerator($data);

		$this->_notified = false;

		if (!isset($data['notify']))
		{
			// rely on the global configuration
			$data['notify'] = (bool) $generator->getParams()->sendinvoice;
		}

		if ($data['notify'])
		{
			// send e-mail notification
			$this->_notified = $generator->send($data['path']);
		}

		return $id;
	}

	/**
	 * Returns whether the customer has been notified or not.
	 *
	 * @return 	boolean
	 */
	public function isNotified()
	{
		return !empty($this->_notified);
	}

	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function delete($ids)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		$db = JFactory::getDbo();

		// get all invoice files
		$query = $db->getQuery(true)
			->select($db->qn(['file', 'group']))
			->from($db->qn('#__vikrestaurants_invoice'))
			->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');

		$db->setQuery($query);
		$files = $db->loadObjectList();

		if (!$files)
		{
			// nothing to delete
			return false;
		}

		// invoke parent first
		if (!parent::delete($ids))
		{
			// nothing to delete
			return false;
		}

		// delete invoices from file system
		foreach ($files as $inv)
		{
			// create invoice object to obtain the right destination folder
			$invoice = E4J\VikRestaurants\Invoice\Factory::getInvoice(null, $inv->group);
			$path = $invoice->getInvoiceFolderPath() . DIRECTORY_SEPARATOR . $inv->file;

			// delete file only if exists
			if (JFile::exists($path))
			{
				JFile::delete($path);
			}
		}

		return true;
	}

	/**
	 * Helper method used to generate an invoice.
	 *
	 * @param 	mixed 	$data  Either an array or an object of data helpful
	 *                         for the creation of the invoice.
	 *
	 * @return 	mixed   The invoice path on success, false otherwise.
	 */
	public function generateInvoice($data)
	{
		$data = (array) $data;

		try
		{
			// load order details according to the specified group
			if ($data['group'] == 0)
			{
				// restaurant reservation
				$order = VREOrderFactory::getReservation($data['id_order']);
			}
			else
			{
				// take-away orders
				$order = VREOrderFactory::getOrder($data['id_order']);
			}
		}
		catch (Exception $e)
		{
			// probably order not found
			$this->setError($e);
			return false;
		}

		// get invoices generator
		$generator = $this->createGenerator($data);

		try
		{
			// load invoice data
			$invoice = E4J\VikRestaurants\Invoice\Factory::getInvoice($order, $data['group']);
		}
		catch (Exception $e)
		{
			// an error occurred, register it and abort
			$this->setError($e);
			return false;
		}
		
		// attach invoice to generator
		$generator->setInvoice($invoice);

		if (isset($data['increase']))
		{
			// increase only if specified
			$increaseNumber = (bool) $data['increase'];
		}
		else
		{
			// increase invoice number only on insert
			$increaseNumber = empty($data['id']);
		}

		try
		{
			// try to generate the invoice and return the resulting path
			$path = $generator->generate($increaseNumber);
		}
		catch (Exception $e)
		{
			// an error occurred, register it and abort
			$this->setError($e);

			return false;
		}

		return $path;
	}

	/**
	 * Creates the invoices generator by passing the specified
	 * parameters and constraints. Notice that the given data
	 * will be injected within the generator only once.
	 *
	 * In order to force the parameters, it will be needed to
	 * manually chain setParams() after getting the generator
	 * instance.
	 *
	 * @param 	mixed 	$data  Either an object or an array.
	 *
	 * @return 	E4J\VikRestaurants\Invoice\InvoiceGenerator
	 */
	public function createGenerator($data)
	{	
		if (!isset($this->_invoiceGenerator))
		{
			$data = (array) $data;

			// create invoice generator only once
			$this->_invoiceGenerator = new E4J\VikRestaurants\Invoice\Generators\PDFGenerator(null, $data);

			if (!empty($data['params']))
			{
				// inject passed parameters
				$this->_invoiceGenerator->setParams($data['params']);
			}

			if (!empty($data['constraints']))
			{
				// inject passed constraints
				$this->_invoiceGenerator->setConstraints($data['constraints']);
			}
		}

		return $this->_invoiceGenerator;
	}

	/**
	 * Method to download one or more invoices.
	 *
	 * @param   mixed  $ids  Either the record ID or a list of records.
	 *
	 * @return  mixed  The path of the file to download (either a PDF or a ZIP).
	 * 				   Returns false in case of errors.
	 */
	public function download($ids)
	{
		if (!$ids)
		{
			// nothing to search
			return false;
		}

		$db = JFactory::getDbo();

		// get all invoice files
		$query = $db->getQuery(true)
			->select($db->qn(['file', 'group']))
			->from($db->qn('#__vikrestaurants_invoice'));

		if (is_string($ids) && preg_match("/^([0-9]+)-([0-9]+)$/", $ids, $match))
		{
			$start_ts = mktime(0, 0, 0, (int) $match[1]    , 1, (int) $match[2]);
			$end_ts   = mktime(0, 0, 0, (int) $match[1] + 1, 1, (int) $match[2]) - 1;

			// search by month
			$query->where($db->qn('inv_date') . ' BETWEEN ' . $start_ts . ' AND ' . $end_ts);
		}
		else
		{
			// search by ID
			$query->where($db->qn('id') . ' IN (' . implode(',', array_map('intval', (array) $ids)) . ')');
		}

		$db->setQuery($query);
		$files = $db->loadObjectList();

		if (!$files)
		{
			// abort, nothing else to do here
			return false;
		}

		// create invoice object to obtain the right destination folder
		$invoice = E4J\VikRestaurants\Invoice\Factory::getInvoice(null, $files[0]->group);

		if (count($files) == 1)
		{
			$path = $invoice->getInvoiceFolderPath() . DIRECTORY_SEPARATOR . $files[0]->file;

			if (!JFile::exists($path))
			{
				// file not found, raise error
				$this->setError(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
				return false;
			}

			// only one record, return the base path of the file to download
			return $path;
		}

		// create a package to download multiple files at once
		if (!class_exists('ZipArchive'))
		{
			// ZipArchive class is mandatory to create a package
			$this->setError('The ZipArchive class is not installed on your server.');
			return false;
		}

		$name = JHtml::fetch('date', 'now', 'Y-m-d H_i_s');
		$zipname = $invoice->getInvoiceFolderPath() . DIRECTORY_SEPARATOR . 'invoices-' . $name . '.zip';
		
		// init package
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);

		// add files to the package
		foreach ($files as $inv)
		{
			// create invoice object to obtain the right destination folder
			$invoice = E4J\VikRestaurants\Invoice\Factory::getInvoice(null, $inv->group);
			$path = $invoice->getInvoiceFolderPath() . DIRECTORY_SEPARATOR . $inv->file;
			
			// make sure the file exists before adding it
			if (JFile::exists($path))
			{
				$zip->addFile($path, basename($path));
			}
		}

		// compress the package
		$zip->close();

		// return the path of the archive
		return $zipname;
	}

	/**
	 * Returns the invoice details of the given order.
	 *
	 * @param 	integer  $id     The order ID.
	 * @param 	string 	 $group  The group to which the order belongs.
	 *
	 * @return 	mixed    The invoice details on success, false otherwise.
	 */
	public function getInvoice($id, $group)
	{
		// prepare conditions
		$where = [
			'id_order' => (int) $id,
			'group'    => (int) $group,
		];

		// load invoice
		$invoice = $this->getItem($where);

		if (!$invoice)
		{
			// invoice not found
			return false;
		}

		// create invoice instance
		$instance = E4J\VikRestaurants\Invoice\Factory::getInvoice(null, $group);

		// set invoice path and URI
		$invoice->path = $instance->getInvoiceFolderPath() . DIRECTORY_SEPARATOR . $invoice->file;
		$invoice->uri  = $instance->getInvoiceFolderURI() . $invoice->file;

		if (!JFile::exists($invoice->path))
		{
			// the invoice was created but the file is missing...
			return false;
		}

		return $invoice;
	}

	/**
	 * The all the months and years that contain at least an invoice.
	 * 
	 * @param   int|null  $group  The group (0: restaurant, 1: take-away)
	 * @param   bool      $flat   True to void grouping the months by year.
	 * 
	 * @return  array
	 */
	public function getInvoicesTree($group = null, $flat = false)
	{
		$db = JFactory::getDbo();
		
		$tree = [];

		$query = $db->getQuery(true)
			->select('COUNT(1) as ' . $db->qn('count'))
			->select(sprintf('DATE_FORMAT(FROM_UNIXTIME(%s), \'%%Y\') AS %s', $db->qn('inv_date'), $db->qn('year')))
			->select(sprintf('DATE_FORMAT(FROM_UNIXTIME(%s), \'%%c\') AS %s', $db->qn('inv_date'), $db->qn('month')))
			->from($db->qn('#__vikrestaurants_invoice'))
			->group($db->qn('year'))
			->group($db->qn('month'))
			->order(sprintf('CAST(%s AS unsigned) DESC', $db->qn('year')))
			->order(sprintf('CAST(%s AS unsigned) DESC', $db->qn('month')));

		if (strlen((string) $group))
		{
			$query->where($db->qn('group') . ' = ' . (int) $group);
		}
		
		$db->setQuery($query);

		foreach ($db->loadObjectList() as $r)
		{
			if (empty($r->year))
			{
				$r->year = $r->month = 0;
			}
			
			if (empty($tree[$r->year]))
			{
				$tree[$r->year] = [];
			}

			$tree[$r->year][$r->month] = [
				'year'  => $r->year,
				'month' => $r->month,
				'count' => $r->count,
				'label' => JHtml::fetch('date', $r->year . '-' . $r->month . '-1 00:00:00', 'F Y', 'UTC'),
			];
		}

		if ($flat)
		{
			$tmp = [];

			// ungroup the months
			foreach ($tree as $months)
			{
				foreach ($months as $month)
				{
					$tmp[$month['month'] . '-' . $month['year']] = $month;
				}
			}

			$tree = $tmp;
		}

		return $tree;
	}
}
