<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Graphics2D;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Handles circle shapes.
 *
 * @since 1.7
 * @since 1.9 Renamed from \Circle
 */
class Circle implements Shape
{
	/**
	 * The radius of the circle.
	 *
	 * @var float
	 */
	private $radius = 0;

	/**
	 * Class constructor.
	 *
	 * @param  float  $radius  The radius of the circle.
	 */
	public function __construct(float $radius)
	{
		$this->setRadius($radius);
	}

	/**
	 * Sets the radius of the circle as the absolute passed value.
	 *
	 * @param   float  $radius  The circle radius.
	 *
	 * @return  self   This object to support chaining.
	 */
	final public function setRadius(float $radius)
	{
		$this->radius = abs($radius);

		return $this;
	}

	/**
	 * Gets the radius of the circle.
	 *
	 * @return  float  The circle radius.
	 */
	final public function getRadius()
	{
		return $this->radius;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the perimeter of the circle with the formula:
	 * 2P = PI * R * 2.
	 */
	final public function perimeter()
	{
		return M_PI * $this->radius * 2;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the area of the circle with the formula:
	 * A = PI * R^2.
	 */
	final public function area()
	{
		return M_PI * $this->radius * $this->radius; 
	}

	/**
	 * @inheritDoc
	 * 
	 * The centroid is always equals to the size of the radius.
	 */
	public function centroid()
	{
		return new Point($this->radius, $this->radius);
	}

	/**
	 * @inheritDoc
	 * 
	 * A point is wrapped in a circle when the distance between the circle center 
	 * and the point is equals or higher than the radius of the circle.
	 * 
	 * @since 1.9
	 */
	public function containsPoint(Point $point)
	{
		return $this->centroid()->getDistance($point) <= $this->getRadius();
	}
}
