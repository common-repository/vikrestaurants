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

use E4J\VikRestaurants\Collection\ProviderCollection;

/**
 * @inheritDoc
 */
class PaymentsCollection extends ProviderCollection
{
	/**
	 * Creates a new instance by loading the payment methods from
	 * the database.
	 * 
	 * @param   JDatabaseDriver|null  $db
	 * 
	 * @return  self  A new collection instance.
	 */
	public static function getInstance($db = null)
	{
		if (!$db)
		{
			$db = \JFactory::getDbo();
		}

		// create provider to load the payment methods from the database
		$provider = new PaymentsDatabaseProvider($db);

		// aggregate provider into a decorator to support payments caching
		$provider = new PaymentsDatabaseCache($provider);

		// construct new payment methods collection
		return new static($provider);
	} 
}
