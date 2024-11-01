<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Payment\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Filters the payment gateways to obtain only the ones available for trusted customers.
 * 
 * @since 1.9
 */
class PluginAvailabilityFilter implements CollectionFilter
{
	/** @var string */
	protected $group;

	/** @var DispatcherInterface */
	protected $dispatcher;

	/**
	 * Class constructor.
	 * 
	 * @param  string               $group       The current group ("restaurant" or "takeaway").
	 * @param  DispatcherInterface  $dispatcher  The event dispatcher.
	 */
	public function __construct(string $group, DispatcherInterface $dispatcher = null)
	{
		$this->group = $group;

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
	public function match(Item $item)
	{
		/**
		 * Trigger event to let external plugins apply additional filters while 
		 * searching for a compatible payment gateway.
		 * The hook will be executed only in case all the other filters
		 * accepted the payment for the usage.
		 *
		 * @param   object  $payment  The payment database record.
		 * @param   string  $group    The group to check ("restaurant" or "takeaway").
		 *
		 * @return  bool    True to accept the payment gateway, false to discard it.
		 *
		 * @since   1.8.3
		 * @since   1.9    The type of $group has been changed from int (1,2) to a string ("restaurant","takeaway").
		 * @since   1.9    Removed $user and $total arguments.
		 */
		$result = $this->dispatcher->filter('onSearchAvailablePayment', [$item, $this->group]);

		/** @var E4J\VikRestaurants\Event\EventResponse */

		if ($result->isFalse())
		{
			// skip payment in case a plugin returned false
			return false;
		}

		return true;
	}
}
