<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Providers\DatabaseProvider;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Interface used to provide a dataset of delivery areas into a collection.
 * 
 * @since 1.9
 */
class AreasDatabaseProvider extends DatabaseProvider
{
	/** @var DispatcherInterface */
	protected $dispatcher;

	/**
	 * Class constructor.
	 * 
	 * @param  JDatabaseDriver      $db
	 * @param  DispatcherInterface  $dispatcher
	 */
	public function __construct($db, DispatcherInterface $dispatcher = null)
	{
		parent::__construct($db);

		if ($dispatcher)
		{
			$this->dispatcher = $dispatcher;
		}
		else
		{
			// dispatcher not provider, use the default one
			$this->dispatcher = \VREFactory::getPlatform()->getDispatcher();
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getQuery()
	{
		$query = $this->db->getQuery(true);

		// query to load all the supported columns
		$columns = $this->db->getTableColumns('#__vikrestaurants_takeaway_delivery_area');

		// select all columns from delivery area table
		foreach ($columns as $field => $type)
		{
			$query->select($this->db->qn('a.' . $field));
		}

		$query->from($this->db->qn('#__vikrestaurants_takeaway_delivery_area', 'a'));
		$query->where(1);

		// group records since the query might use aggregators
		$query->group($this->db->qn('a.id'));
		// always sort records by ascending ordering
		$query->order($this->db->qn('a.ordering') . ' ASC');

		/**
		 * Trigger hook to allow external plugins to manipulate the query used
		 * to load the delivery areas through this helper class.
		 *
		 * @param   mixed  &$query  A query builder object.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$this->dispatcher->trigger('onBeforeQueryDeliveryAreas', [&$query]);

		return $query;
	}

	/**
	 * @inheritDoc
	 */
	protected function map(object $item)
	{
		// decode JSON-encoded properties
		$item->content    = $item->content    ? json_decode($item->content)    : new \stdClass;
		$item->attributes = $item->attributes ? json_decode($item->attributes) : new \stdClass;

		return Area::getInstance($item);
	}
}
