<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Payment;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\Collection\Providers\DatabaseProvider;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Interface used to provide a dataset of payment methods into a collection.
 * 
 * @since 1.9
 */
class PaymentsDatabaseProvider extends DatabaseProvider
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
		$columns = $this->db->getTableColumns('#__vikrestaurants_gpayments');

		// select all columns from payment gateways table
		foreach ($columns as $field => $type)
		{
			$query->select($this->db->qn('p.' . $field));
		}

		$query->from($this->db->qn('#__vikrestaurants_gpayments', 'p'));
		$query->where(1);

		// group records since the query might use aggregators
		$query->group($this->db->qn('p.id'));
		// always sort records by ascending ordering
		$query->order($this->db->qn('p.ordering') . ' ASC');

		/**
		 * Trigger hook to allow external plugins to manipulate the query used
		 * to load the payment methods through this helper class.
		 *
		 * @param   mixed  &$query  A query builder object.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		$this->dispatcher->trigger('onBeforeQueryPayments', [&$query]);

		return $query;
	}

	/**
	 * @inheritDoc
	 */
	protected function map(object $item)
	{
		// decode JSON-encoded parameters
		$item->params = $item->params ? (array) json_decode($item->params, true) : [];

		// translate payments in case multi-lingual is supported
		\VikRestaurants::translatePayments($item);

		// wrap payment details into a collectable item instance
		return new Item($item);
	}
}
