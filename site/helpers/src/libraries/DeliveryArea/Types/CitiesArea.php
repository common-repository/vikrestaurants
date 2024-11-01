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
 * VikRestaurants delivery area cities holder.
 *
 * @since 1.9
 */
class CitiesArea extends Area
{
	/** @var object[] */
	protected $cities = [];

	/**
	 * inheritDoc
	 */
	public function __construct($data)
	{
		parent::__construct($data);

		// validate cities
		foreach ($this->get('content', []) as $city)
		{
			// make sure we have a valid city
			if (empty($city->name))
			{
				continue;
			}

			if (!isset($city->published))
			{
				$city->published = true;   
			}

			$this->cities[] = $city;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRTKAREATYPE4');
	}

	/**
	 * @inheritDoc
	 */
	public function canAccept(DeliveryQuery $query)
	{
		// extract city name from search query
		$cityName = $query->getCity();

		if (!$cityName)
		{
			// city name not provided
			return false;
		}

		// scan all the cities
		foreach ($this->cities as $city)
		{
			// compare cities without caring of the lower/upper case characters
			if (strcasecmp($cityName, $city->name) === 0)
			{
				return true;
			}
		}

		// city not found
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
