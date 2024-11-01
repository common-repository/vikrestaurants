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
 * Creates a datasheet for the take-away products stored in the database.
 * 
 * @since 1.9
 */
#[\AllowDynamicProperties]
class TakeAwayProductsDataSheet extends DatabaseDataSheet
{
	use \E4J\VikRestaurants\DataSheet\Helpers\CurrencyFormatter;
	use \E4J\VikRestaurants\DataSheet\Helpers\TaxFormatter;

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
		// Products
		return \JText::translate('VRMENUMENUSPRODUCTS');
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		$head = [];

		// ID
		$head[] = \JText::translate('VRMANAGEMENUSPRODUCT1');
		// Name
		$head[] = \JText::translate('VRMANAGEMENUSPRODUCT2');
		// Description
		$head[] = \JText::translate('VRMANAGEMENUSPRODUCT3');
		// Price
		$head[] = \JText::translate('VRMANAGEMENUSPRODUCT4');
		// Tax
		$head[] = \JText::translate('VRETAXFIELDSET');
		// Image
		$head[] = \JText::translate('VRMANAGEMENUSPRODUCT5');
		// Published
		$head[] = \JText::translate('VRMANAGEMENUSPRODUCT6');
		// Ready
		$head[] = \JText::translate('VRMANAGETKMENU9');

		return $head;
	}

	/**
	 * @inheritDoc
	 */
	public function formatRow(object $record)
	{
		// increase product proce
		$totalPrice = (float) $record->price + (float) $record->option_price;

		// check whether we should display raw contents or not
		if ($this->options->get('raw', false) == false)
		{
			// create a clone of the record to avoid manipulating the
			// default object by reference
			$record = clone $record;

			// append option ID to product ID
			$record->id = $record->id . ($record->option_id ? '/' . $record->option_id : '');
			// remove HTML tags from the description
			$record->description = strip_tags((string) $record->description);
			// convert total price into a currency
			$totalPrice = $this->toCurrency($totalPrice);
			// convert tax ID into a readable name
			$record->id_tax = $this->toTaxName($record->id_tax);
			// display a label for published/unpublished products
			$record->published = \JText::translate($record->published ? 'JYES' : 'JNO');
			// display a label for products that require or not a preparation
			$record->ready = \JText::translate($record->ready ? 'JYES' : 'JNO');
		}

		$row = [];

		// ID
		$row[] = $record->id;
		// Name
		$row[] = $record->name . ($record->option_name ? ' - ' . $record->option_name : '');
		// Description
		$row[] = $record->description;
		// Price
		$row[] = $totalPrice;
		// Tax
		$row[] = $record->id_tax;
		// Image
		$row[] = $record->img_path;
		// Published
		$row[] = $record->published;
		// Ready
		$row[] = $record->ready;

		return $row;
	}

	/**
	 * @inheritDoc
	 * 
	 * Replicates the list query used by the take-away products view in the back-end.
	 * 
	 * @todo Reuse the take-away products list model once it will be implemented.
	 */
	protected function getListQuery()
	{
		$app = \JFactory::getApplication();

		// define default filters
		$this->options->def('search', $app->getUserState('vretkproducts.search', ''));
		$this->options->def('id_menu', $app->getUserState('vretkproducts.id_takeaway_menu', 0));
		$this->options->def('status', $app->getUserState('vretkproducts.status', ''));

		// define default ordering
		$this->options->def('ordering', $app->getUserState('vretkproducts.ordering', 'e.ordering'));
		$this->options->def('direction', $app->getUserState('vretkproducts.orderdir', 'ASC'));
		
		$filters = [];
		$filters['search']  = $this->options->get('search', '');
		$filters['id_menu'] = $this->options->get('id_menu', '');
		$filters['status']  = $this->options->get('status', '');

		$this->filters = $filters;

		$this->ordering = $this->options->get('ordering', '');
		$this->orderDir = $this->options->get('direction', '');

		$query = $this->db->getQuery(true)
			->select('e.*')
			->select($this->db->qn('o.id', 'option_id'))
			->select($this->db->qn('o.name', 'option_name'))
			->select($this->db->qn('o.inc_price', 'option_price'))
			->from($this->db->qn('#__vikrestaurants_takeaway_menus_entry', 'e'))
			->leftjoin($this->db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $this->db->qn('e.id') . ' = ' . $this->db->qn('o.id_takeaway_menu_entry'))
			->where(1)
			->order($this->db->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$query->where($this->db->qn('e.name') . ' LIKE ' . $this->db->q("%{$filters['search']}%"));
		}

		if (strlen($filters['status']))
		{
			$query->where($this->db->qn('e.published') . ' = ' . (int) $filters['status']);
		}

		if ($filters['id_menu'])
		{
			$query->where($this->db->qn('e.id_takeaway_menu') . ' = ' . (int) $filters['id_menu']);
		}

		/**
		 * @todo replicate this query in the take-away products view too
		 */
		if ($ids = $this->options->get('cid', []))
		{
			// filter take-away products by ID
			$query->where($this->db->qn('e.id') . ' IN (' . implode(',', array_map('intval', (array) $ids)) . ')');
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryTkproducts" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		\VREFactory::getEventDispatcher()->trigger('onBeforeListQueryTkproducts', [&$query, $this]);
		
		return $query;
	}
}
