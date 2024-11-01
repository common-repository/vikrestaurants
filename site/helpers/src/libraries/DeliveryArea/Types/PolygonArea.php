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
use E4J\VikRestaurants\Graphics2D\Point;
use E4J\VikRestaurants\Graphics2D\Polygon2D;

/**
 * VikRestaurants delivery area polygon holder.
 *
 * @since 1.9
 */
class PolygonArea extends Area
{
	/** @var Polygon2D */
	protected $polygon;

	/**
	 * inheritDoc
	 */
	public function __construct($data)
	{
		parent::__construct($data);

		$polygon = new Polygon2D();

		// iterate all the added coordinates
		foreach ($this->get('content', []) as $point)
		{
			// make sure we have valid coordinates
			if (!isset($point->latitude) || !isset($point->longitude))
			{
				continue;
			}

			// add a vertex to the polygon
			$polygon->addPoint(new Point((float) $point->longitude, (float) $point->latitude));
		}

		// make sure we have a valid polygon
		if ($polygon->getNumPoints() >= 3)
		{
			$this->polygon = $polygon;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRTKAREATYPE1');
	}

	/**
	 * @inheritDoc
	 */
	public function canAccept(DeliveryQuery $query)
	{
		if (!$this->polygon)
		{
			// invalid polygon
			return false;
		}

		// extract coordinates from search query
		$coordinates = $query->getCoordinates();

		if (!$coordinates)
		{
			// coordinates not provided
			return false;
		}

		// check whether the specified coordinates are within the polygon area
		return $this->polygon->containsPoint(
			new Point($coordinates->longitude, $coordinates->latitude)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function onSave(array &$src, $model)
	{
		if (isset($src['content']) && is_array($src['content']))
		{
			$content = [];

			for ($i = 0; $i < count($src['content']['latitude'] ?? []); $i++)
			{
				if (strlen($src['content']['latitude'][$i] ?? '') === 0)
				{
					// missing latitude, skip
					continue;
				}

				if (strlen($src['content']['longitude'][$i] ?? '') === 0)
				{
					// missing longitude, skip
					continue;
				}

				// register coordinate
				$content[] = [
					'latitude'  => $src['content']['latitude'][$i],
					'longitude' => $src['content']['longitude'][$i],
				];
			}

			$src['content'] = $content;
		}
		
		return true;
	}
}
