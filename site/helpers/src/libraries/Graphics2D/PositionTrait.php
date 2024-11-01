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
 * Implements the getters and setters needed to a shape that supports a location.
 * 
 * @since 1.9
 */
trait PositionTrait
{
	/**
	 * The position of the rectangle, which starts from the top-left corner.
	 *
	 * @var Point
	 */
	protected $point;

	/**
	 * Sets the coordinates of the shape.
	 * 
	 * @param   Point  $point  The new shape coordinates.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setPoint(Point $point)
	{
		if (!$this->point instanceof Point)
		{
			$this->point = new Point();
		}

		$this->point->setLocation($point->x, $point->y);

		return $this;
	}

	/**
	 * Gets the coordinates of the shape.
	 *
	 * @return  Point  The coordinates of the shape.
	 */
	public function getPoint()
	{
		return $this->point;
	}

	/**
	 * Gets the X (horizontal) position of the shape.
	 *
	 * @return  float  The x position of the shape.
	 */
	public function getX()
	{
		return $this->point->x;
	}

	/**
	 * Sets the X (horizontal) position of the shape.
	 * 
	 * @param   float  $x  The x position of the shape.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setX(float $x)
	{
		$this->point->setX($x);

		return $this;
	}

	/**
	 * Gets the Y (vertical) position of the shape.
	 *
	 * @return  float  The Y position of the shape.
	 */
	public function getY()
	{
		return $this->point->y;
	}

	/**
	 * Sets the Y (vertical) position of the shape.
	 * 
	 * @param   float  $y  The y position of the shape.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setY(float $y)
	{
		$this->point->setY($y);

		return $this;
	}
}
