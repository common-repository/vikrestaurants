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
 * Creates a datasheet for the restaurant products stored in the database.
 * 
 * @since 1.9
 */
#[\AllowDynamicProperties]
class RestaurantProductsDataSheet extends DatabaseDataSheet
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
		// Tags
		$head[] = \JText::translate('VRTAGS');

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
			// beutify tags
			$record->tags = $record->tags ? implode(', ', array_map('ucwords', explode(',', $record->tags))) : '/';
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
		$row[] = $record->image;
		// Published
		$row[] = $record->published;
		// Tags
		$row[] = $record->tags;

		return $row;
	}

	/**
	 * @inheritDoc
	 * 
	 * Replicates the list query used by the restaurant products view in the back-end.
	 * 
	 * @todo Reuse the restaurant products list model once it will be implemented.
	 */
	protected function getListQuery()
	{
		$app = \JFactory::getApplication();

		// define default filters
		$this->options->def('search', $app->getUserState('vremenusproducts.search', ''));
		$this->options->def('id_menu', $app->getUserState('vremenusproducts.id_menu', 0));
		$this->options->def('status', $app->getUserState('vremenusproducts.status', ''));
		$this->options->def('tag', $app->getUserState('vremenusproducts.tag', ''));

		// define default ordering
		$this->options->def('ordering', $app->getUserState('vremenusproducts.ordering', 'p.ordering'));
		$this->options->def('direction', $app->getUserState('vremenusproducts.orderdir', 'ASC'));
		
		$filters = [];
		$filters['search']  = $this->options->get('search', '');
		$filters['id_menu'] = $this->options->get('id_menu', '');
		$filters['status']  = $this->options->get('status', '');
		$filters['tag']     = $this->options->get('tag', '');

		$this->filters = $filters;

		$this->ordering = $this->options->get('ordering', '');
		$this->orderDir = $this->options->get('direction', '');

		$query = $this->db->getQuery(true)
			->select('p.*')
			->select($this->db->qn('o.id', 'option_id'))
			->select($this->db->qn('o.name', 'option_name'))
			->select($this->db->qn('o.inc_price', 'option_price'))
			->from($this->db->qn('#__vikrestaurants_section_product', 'p'))
			->leftjoin($this->db->qn('#__vikrestaurants_section_product_option', 'o') . ' ON ' . $this->db->qn('p.id') . ' = ' . $this->db->qn('o.id_product'))
			->where(1)
			->order($this->db->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$query->where($this->db->qn('p.name') . ' LIKE ' . $this->db->q("%{$filters['search']}%"));
		}
		
		if (strlen($filters['status']))
		{
			if ($filters['status'] < 2)
			{
				// published/unpublished
				$query->where([
					$this->db->qn('p.hidden') . ' = 0',
					$this->db->qn('p.published') . ' = ' . (int) $filters['status'],
				]);
			}
			else
			{
				// hidden
				$query->where($this->db->qn('p.hidden') . ' = 1');
				// always unset menus filtering
				$filters['id_menu'] = 0;
			}
		}
		else
		{
			// all except for hidden products
			$query->where($this->db->qn('p.hidden') . ' = 0');
		}

		if ($filters['id_menu'])
		{
			$query->leftjoin($this->db->qn('#__vikrestaurants_section_product_assoc', 'a') . ' ON ' . $this->db->qn('a.id_product') . ' = ' . $this->db->qn('p.id'));
			$query->leftjoin($this->db->qn('#__vikrestaurants_menus_section', 's') . ' ON ' . $this->db->qn('a.id_section') . ' = ' . $this->db->qn('s.id'));
			$query->where($this->db->qn('s.id_menu') . ' = ' . (int) $filters['id_menu']);
		}

		if ($filters['tag'])
		{
			$query->andWhere([
				// only one tag
				$this->db->qn('p.tags') . ' = ' . $this->db->q($filters['tag']),
				// tag in the middle
				$this->db->qn('p.tags') . ' LIKE ' . $this->db->q("%,{$filters['tag']},%"),
				// first tag available
				$this->db->qn('p.tags') . ' LIKE ' . $this->db->q("{$filters['tag']},%"),
				// last tag available
				$this->db->qn('p.tags') . ' LIKE ' . $this->db->q("%,{$filters['tag']}"),
			], 'OR');
		}

		/**
		 * @todo replicate this query in the restaurant products view too
		 */
		if ($ids = $this->options->get('cid', []))
		{
			// filter restaurant products by ID
			$query->where($this->db->qn('p.id') . ' IN (' . implode(',', array_map('intval', (array) $ids)) . ')');
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryMenusproducts" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		\VREFactory::getEventDispatcher()->trigger('onBeforeListQueryMenusproducts', [&$query, $this]);
		
		return $query;
	}
}
