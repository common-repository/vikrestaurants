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
 * Creates a datasheet for the reviews stored in the database.
 * 
 * @since 1.9
 */
#[\AllowDynamicProperties]
class ReviewsDataSheet extends DatabaseDataSheet
{
	use \E4J\VikRestaurants\DataSheet\Helpers\CmsUserFormatter;

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
		// Reviews
		return \JText::translate('VRMENUREVIEWS');
	}

	/**
	 * @inheritDoc
	 */
	public function getHead()
	{
		$head = [];

		// ID
		$head[] = \JText::translate('VRMANAGEREVIEW1');
		// Title
		$head[] = \JText::translate('VRMANAGEREVIEW2');
		// Account Name
		$head[] = \JText::translate('VRMANAGEREVIEW3');
		// User Name
		$head[] = \JText::translate('VRMANAGEREVIEW10');
		// E-Mail
		$head[] = \JText::translate('VRMANAGEREVIEW11');
		// IP Address
		$head[] = \JText::translate('VRMANAGEAPIUSER17');
		// Date
		$head[] = \JText::translate('VRMANAGEREVIEW4');
		// Rating
		$head[] = \JText::translate('VRMANAGEREVIEW5');
		// Product
		$head[] = \JText::translate('VRMANAGEREVIEW6');
		// Published
		$head[] = \JText::translate('VRMANAGEREVIEW7');
		// Verified
		$head[] = \JText::translate('VRMANAGEREVIEW12');
		// Language
		$head[] = \JText::translate('VRMANAGEREVIEW8');
		// Comment
		$head[] = \JText::translate('VRMANAGEREVIEW9');

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
			// convert user ID into an username
			$record->jid = $this->toUsername((int) $record->jid);
			// adjust the date to the user timezone
			$record->timestamp = \JHtml::fetch('date', date('Y-m-d H:i:s', $record->timestamp), 'Y-m-d H:i:s');
			// display a label for published/unpublished reviews
			$record->published = \JText::translate($record->published ? 'JYES' : 'JNO');
			// display a label for verified/unverified reviews
			$record->verified = \JText::translate($record->verified ? 'JYES' : 'JNO');
		}
		else
		{
			// use the ISO 8601 format for the date
			$record->timestamp = \JFactory::getDate(date('Y-m-d H:i:s', $record->timestamp))->toISO8601();
			// replace the product name with its ID
			$record->takeaway_product_name = $record->id_takeaway_product;
		}

		$row = [];

		// ID
		$row[] = (int) $record->id;
		// Title
		$row[] = $record->title;
		// Account Name
		$row[] = $record->jid;
		// User Name
		$row[] = $record->name;
		// E-Mail
		$row[] = $record->email;
		// IP Address
		$row[] = $record->ipaddr;
		// Date
		$row[] = $record->timestamp;
		// Rating
		$row[] = $record->rating;
		// Product
		$row[] = $record->takeaway_product_name;
		// Published
		$row[] = $record->published;
		// Verified
		$row[] = $record->verified;
		// Language
		$row[] = $record->langtag;
		// Comment
		$row[] = $record->comment;

		return $row;
	}

	/**
	 * @inheritDoc
	 * 
	 * Replicates the list query used by the reviews view in the back-end.
	 * 
	 * @todo Reuse the reviews list model once it will be implemented.
	 */
	protected function getListQuery()
	{
		$app = \JFactory::getApplication();

		// define default filters
		$this->options->def('search', $app->getUserState('vrereviews.search', ''));
		$this->options->def('status', $app->getUserState('vrereviews.status', ''));
		$this->options->def('verified', $app->getUserState('vrereviews.verified', ''));
		$this->options->def('stars', $app->getUserState('vrereviews.stars', 0));

		// define default ordering
		$this->options->def('ordering', $app->getUserState('vrereviews.ordering', 'r.id'));
		$this->options->def('direction', $app->getUserState('vrereviews.orderdir', 'ASC'));
		
		$filters = [];
		$filters['search']   = $this->options->get('search', '');
		$filters['status']   = $this->options->get('status', '');
		$filters['verified'] = $this->options->get('verified', '');
		$filters['stars']    = $this->options->get('stars', 0);

		$this->filters = $filters;

		$this->ordering = $this->options->get('ordering', '');
		$this->orderDir = $this->options->get('direction', '');

		$query = $this->db->getQuery(true)
			->select('r.*')
			->select($this->db->qn('e.name', 'takeaway_product_name'))
			->from($this->db->qn('#__vikrestaurants_reviews', 'r'))
			->leftjoin($this->db->qn('#__vikrestaurants_takeaway_menus_entry', 'e') . ' ON ' . $this->db->qn('e.id') . ' = ' . $this->db->qn('r.id_takeaway_product'))
			->where(1)
			->order($this->db->qn($this->ordering) . ' ' . $this->orderDir);

		if ($filters['search'])
		{
			$query->andWhere([
				$this->db->qn('r.name') . ' LIKE ' . $this->db->q("%{$filters['search']}%"),
				$this->db->qn('r.title') . ' LIKE ' . $this->db->q("%{$filters['search']}%"),
				$this->db->qn('e.name') . ' LIKE ' . $this->db->q("%{$filters['search']}%"),
			]);
		}

		if ($filters['status'] !== '')
		{
			$query->where($this->db->qn('r.published') . ' = ' . (int) $filters['status']);
		}

		if ($filters['verified'] !== '')
		{
			$query->where($this->db->qn('r.verified') . ' = ' . (int) $filters['verified']);
		}

		if ($filters['stars'])
		{
			$query->where($this->db->qn('r.rating') . ' = ' . (int) $filters['stars']);
		}

		/**
		 * @todo replicate this query in the reviews view too
		 */
		if ($ids = $this->options->get('cid', []))
		{
			// filter reviews by ID
			$query->where($this->db->qn('r.id') . ' IN (' . implode(',', array_map('intval', (array) $ids)) . ')');
		}

		/**
		 * It is possible to lean on the "onBeforeListQueryReviews" plugin event
		 * to manipulate the query used to load the list of records.
		 *
		 * @since 1.9
		 */
		\VREFactory::getEventDispatcher()->trigger('onBeforeListQueryReviews', [&$query, $this]);
		
		return $query;
	}
}
