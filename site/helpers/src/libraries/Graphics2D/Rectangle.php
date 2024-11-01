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
 * Handle rectangle shapes.
 *
 * @since 1.7
 * @since 1.9  Renamed from \Rectangle
 */
class Rectangle implements Shape
{
	/**
	 * The width of the rectangle.
	 *
	 * @var float
	 */
	private $width = 0;

	/**
	 * The height of the rectangle.
	 *
	 * @var float
	 */
	private $height = 0;

	/**
	 * Class constructor.
	 *
	 * @param  float  $width   The rectangle width.
	 * @param  float  $height  The rectangle height. 
	 */
	public function __construct(float $width = 0, float $height = 0)
	{
		$this->setWidth($width)->setHeight($height);
	}

	/**
	 * Sets the width of the rectangle as the absolute passed value.
	 *
	 * @param   float  $width  The rectangle width.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setWidth(float $width)
	{
		$this->width = abs($width);

		return $this;
	}

	/**
	 * Gets the width of the rectangle.
	 *
	 * @return  float  The rectangle width.
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Sets the height of the rectangle as the absolute passed value.
	 *
	 * @param   float  $height  The rectangle height.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setHeight(float $height)
	{
		$this->height = abs($height);

		return $this;
	}

	/**
	 * Gets the height of the rectangle.
	 *
	 * @return  float  The rectangle height.
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the perimeter of the rectangle with the formula:
	 * 2P = W * 2 + H * 2.
	 */
	final public function perimeter()
	{
		return $this->width * 2 + $this->height * 2;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the area of the rectangle with the formula:
	 * A = W * H
	 */
	final public function area()
	{
		return $this->width * $this->height;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the centroid of the rectangle with the formula:
	 * C = W/2 ; H/2
	 */
	public function centroid()
	{
		return new Point($this->width / 2, $this->height / 2);
	}

	/**
	 * Calculates the diagonal of the rectangle with the formula:
	 * D = √( W^2 + H^2 ) = Pythagorean theorem
	 *
	 * @return  float  The rectangle diagonal.
	 */
	final public function diagonal()
	{
		return sqrt(pow($this->width, 2) + pow($this->height, 2));
	}

	/**
	 * @inheritDoc
	 * 
	 * A point is inside a rectangle when its coordinates do not exceed the 
	 * bounds (width and height) of the shape.
	 * 
	 * @since 1.9
	 */
	public function containsPoint(Point $point)
	{
		return 0 <= $point->x && $point->x <= $this->getWidth()
			&& 0 <= $point->y && $point->y <= $this->getHeight();
	}
}
