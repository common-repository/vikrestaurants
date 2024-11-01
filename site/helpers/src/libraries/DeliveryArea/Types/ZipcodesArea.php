<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea\Types;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DeliveryArea\Area;
use E4J\VikRestaurants\DeliveryArea\DeliveryQuery;

/**
 * VikRestaurants delivery area ZIP codes holder.
 *
 * @since 1.9
 */
class ZipcodesArea extends Area
{
	/** @var object[] */
	protected $codes = [];

	/**
	 * inheritDoc
	 */
	public function __construct($data)
	{
		parent::__construct($data);

		// create ZIP intervals
		foreach ($this->get('content', []) as $range)
		{
			// make sure we have a valid range
			if (empty($range->from) && empty($range->to))
			{
				continue;
			}

			if (empty($range->from))
			{
				$range->from = $range->to;
			}

			if (empty($range->to))
			{
				$range->to = $range->from;
			}

			if (!isset($range->published))
			{
				$range->published = true;	
			}

			$this->codes[] = $range;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRTKAREATYPE3');
	}

	/**
	 * @inheritDoc
	 */
	public function canAccept(DeliveryQuery $query)
	{
		// extract ZIP code from search query
		$zipCode = $query->getZipCode();

		if (!$zipCode)
		{
			// ZIP code not provided
			return false;
		}

		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		/**
		 * It is possible to use this hook to enhance or change the default algorithm
		 * while checking whether a specific ZIP code is allowed or not.
		 *
		 * @param   DeliveryQuery  $query  The delivery query,
		 * @param   ZipcodesArea   $area   The instance used to validate an address by zip code.
		 *
		 * @return 	bool  Returns true to accept the ZIP code. Return false to deny the
		 *                ZIP Code. Return null to rely on the default algorithm.
		 *
		 * @since 	1.9
		 */
		$result = $dispatcher->filter('onValidateZipCode', [$query, $this]);

		/** @var E4J\VikRestaurants\Event\EventResponse */

		if ($result->isFalse())
		{
			// a plugin denied the ZIP Code
			return false;
		}
		else if ($result->isTrue())
		{
			// a plugin allowed the ZIP Code
			return true;
		}

		// go ahead with the default algorithm
		foreach ($this->codes as $code)
		{
			if ($code->from <= $zipCode && $zipCode <= $code->to)
			{
				// the provided ZIP code is contained between the specified range
				return true;
			}			
		}

		// ZIP code not found
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function onSave(array &$src, $model)
	{
		if (isset($src['content']) && is_array($src['content']))
		{
			// iterate all the provided contents
			foreach ($src['content'] as &$content)
			{
				if (is_string($content))
				{
					// JSON given, decode it
					$content = json_decode($content, true);
				}
			}
		}
		
		return true;
	}
}
