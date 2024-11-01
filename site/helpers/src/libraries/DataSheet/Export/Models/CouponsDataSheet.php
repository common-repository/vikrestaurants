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
 * Creates a datasheet for the coupons stored in the database.
 * 
 * @since 1.9
 */
#[\AllowDynamicProperties]
class CouponsDataSheet extends DatabaseDataSheet
{
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
		// Coupons
		return \JText::translate('VRCOUPONS');
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		$head = [];

		// ID
		$head[] = \JText::translate('JGRID_HEADING_ID');
		// Code
		$head[] = \JText::translate('VRMANAGECOUPON1');
		// Type
		$head[] = \JText::translate('VRMANAGECOUPON2');

		if ($this->options->get('raw', false) == true)
		{
			// Percent or Total
			$head[] = \JText::translate('VRMANAGECOUPON3');	
		}
		
		// Value
		$head[] = \JText::translate('VRMANAGECOUPON4');
		// Date Start
		$head[] = \JText::translate('VRMANAGECOUPON5');
		// Date End
		$head[] = \JText::translate('VRMANAGECOUPON6');

		if (\VikRestaurants::isRestaurantEnabled())
		{
			// Min. People
			$head[] = \JText::translate('VRMANAGECOUPON8');
		}

		if (\VikRestaurants::isTakeAwayEnabled())
		{
			// Min. Total Cost
			$head[] = \JText::translate('VRMANAGECOUPON9');
		}

		// Group
		$head[] = \JText::translate('VRMANAGECOUPON10');
		// Max Usages
		$head[] = \JText::translate('VRMANAGECOUPON12');
		// Usages per Customer
		$head[] = \JText::translate('VRMANAGECOUPON13');
		// Total Usages
		$head[] = \JText::translate('VRMANAGECOUPON14');
		// Category
		$head[] = \JText::translate('VRMANAGECOUPON16');
		// Notes
		$head[] = \JText::translate('VRMANAGECUSTOMERTITLE4');

		return $head;
	}

	/**
	 * @inheritDoc
	 */
	public function formatRow(object $record)
	{
		// create a clone of the record to avoid manipulating the
		// default object by reference
		$record = clone $record;

		// check whether we should display raw contents or not
		if ($this->options->get('raw', false) == false)
		{
			// permanent (1) or gift (2)
			$record->type = \JText::translate('VRCOUPONTYPEOPTION' . $record->type);

			if ($record->percentot == 1)
			{
				// convert coupon amount into a percentage
				$record->value = $this->toCurrency($record->value, [
					'symbol'     => '%',
					'position'   => 1,
					'space'      => false,
					'no_decimal' => true,
				]);
			}
			else
			{
				// convert coupon amount into a currency
				$record->value = $this->toCurrency($record->value);
			}

			// adjust the start publishing to the user timezone
			$record->start_publishing = !DateHelper::isNull($record->start_publishing) ? \JHtml::fetch('date', $record->start_publishing, 'Y-m-d H:i') : '/';
			// adjust the end publishing to the user timezone
			$record->end_publishing = !DateHelper::isNull($record->end_publishing) ? \JHtml::fetch('date', $record->end_publishing, 'Y-m-d H:i') : '/';
			// convert coupon min cost into a currency
			$record->mincost = $this->toCurrency($record->mincost);
			// restaurant (0) or take-away (1)
			$record->group = \JText::translate($record->group == 0 ? 'VRMANAGECONFIGTITLE1' : 'VRMANAGECONFIGTITLE2');
			// format max usages
			$record->maxusages = $record->maxusages ?: '/';
			// format category name
			$record->category_name = $record->category_name ?: '/';
		}
		else
		{
			// use the ISO 8601 format for the start publishing date
			$record->start_publishing = !DateHelper::isNull($record->start_publishing) ? \JFactory::getDate($record->start_publishing)->toISO8601() : null;
			// use the ISO 8601 format for the end publishing date
			$record->end_publishing = !DateHelper::isNull($record->end_publishing) ? \JFactory::getDate($record->end_publishing)->toISO8601() : null;
			// replace the category name with its ID
			$record->category_name = $record->id_category;
		}

		$row = [];

		// ID
		$row[] = (int) $record->id;
		// Code
		$row[] = $record->code;
		// Type
		$row[] = $record->type;

		if ($this->options->get('raw', false) == true)
		{
			// Percent or Total
			$row[] = $record->percentot;
		}

		// Value
		$row[] = $record->value;
		// Date Start
		$row[] = $record->start_publishing;
		// Date End
		$row[] = $record->end_publishing;

		if (\VikRestaurants::isRestaurantEnabled())
		{
			// Min. People
			$row[] = $record->minpeople;
		}

		if (\VikRestaurants::isTakeAwayEnabled())
		{
			// Min. Total Cost
			$row[] = $record->mincost;
		}

		// Group
		$row[] = $record->group;
		// Max Usages
		$row[] = $record->maxusages;
		// Usages per Customer
		$row[] = $record->maxperuser;
		// Total Usages
		$row[] = $record->usages;
		// Category
		$row[] = $record->category_name;
		// Notes
		$row[] = $record->notes;

		return $row;
	}

	/**
	 * @inheritDoc
	 * 
	 * Replicates the list query used by the coupons view in the back-end.
	 * 
	 * @todo Reuse the coupons list model once it will be implemented.
	 */
	protected function getListQuery()
	{
		$app = \JFactory::getApplication();

		// define default filters
		$this->options->def('search', $app->getUserState('vrecoupons.search', ''));
		$this->options->def('group', $app->getUserState('vrecoupons.group', ''));
		$this->options->def('type', $app->getUserState('vrecoupons.type', 0));
		$this->options->def('id_category', $app->getUserState('vrecoupons.id_category', ''));

		// define default ordering
		$this->options->def('ordering', $app->getUserState('vrecoupons.ordering', 'c.id'));
		$this->options->def('direction', $app->getUserState('vrecoupons.orderdir', 'ASC'));
		
		$filters = [];
		$filters['search']      = $this->options->get('search', '');
		$filters['group']       = \JHtml::fetch('vrehtml.admin.getgroup', $this->options->get('group', ''), null, true);
		$filters['type']        = $this->options->get('type', 0);
		$filters['id_category'] = $this->options->get('id_category', '');

		$this->filters = $filters;

		$this->ordering = $this->options->get('ordering', '');
		$this->orderDir = $this->options->get('direction', '');

		$query = $this->db->getQuery(true)
			->select('c.*')
			->select($this->db->qn('g.name', 'category_name'))
			->from($this->db->qn('#__vikrestaurants_coupons', 'c'))
			->leftjoin($this->db->qn('#__vikrestaurants_coupon_category', 'g') . ' ON ' . $this->db->qn('g.id') . ' = ' . $this->db->qn('c.id_category'))
			->where(1)
			->order($this->db->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'] !== '')
		{
			$query->where($this->db->qn('c.code') . ' LIKE ' . $this->db->q("%{$filters['search']}%"));
		}

		if ((string) $filters['group'] !== '')
		{
			$query->where($this->db->qn('c.group') . ' = ' . (int) $filters['group']);
		}

		if ($filters['type'])
		{
			$query->where($this->db->qn('c.type') . ' = ' . (int) $filters['type']);
		}

		if ($filters['id_category'] !== '')
		{
			$query->where($this->db->qn('c.id_category') . ' = ' . (int) $filters['id_category']);
		}

		/**
		 * @todo replicate this query in the coupons view too
		 */
		if ($ids = $this->options->get('cid', []))
		{
			// filter coupons by ID
			$query->where($this->db->qn('c.id') . ' IN (' . implode(',', array_map('intval', (array) $ids)) . ')');
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryCoupons" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		\VREFactory::getEventDispatcher()->trigger('onBeforeListQueryCoupons', [&$query, $this]);
		
		return $query;
	}
}
