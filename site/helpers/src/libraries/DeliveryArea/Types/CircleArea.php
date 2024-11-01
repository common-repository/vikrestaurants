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
use E4J\VikRestaurants\Graphics2D\Circle2D;
use E4J\VikRestaurants\Graphics2D\Point;
use E4J\VikRestaurants\Graphics2D\GeometryHelper;

/**
 * VikRestaurants delivery area circle holder.
 *
 * @since 1.9
 */
class CircleArea extends Area
{
	/** @var Circle2D */
	protected $circle;

	/**
	 * inheritDoc
	 */
	public function __construct($data)
	{
		parent::__construct($data);

		$content = $this->get('content');

		// validate circle configuration
		if (isset($content->center->latitude) && isset($content->center->longitude) && isset($content->radius))
		{
			// create circle shape
			$this->circle = new Circle2D((float) $content->radius, (float) $content->center->longitude, (float) $content->center->latitude);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRTKAREATYPE2');
	}

	/**
	 * @inheritDoc
	 */
	public function canAccept(DeliveryQuery $query)
	{
		// extract coordinates from search query
		$coordinates = $query->getCoordinates();

		if (!$coordinates)
		{
			// coordinates not provided
			return false;
		}

		// check whether the specified coordinates are within the circle area
		return GeometryHelper::isPointInsideCircleOnEarth(
			$this->circle,
			new Point($coordinates->longitude, $coordinates->latitude)
		);
	}
}
