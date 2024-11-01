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
 * Handles 2D rectangle shapes (position and size).
 *
 * @since 1.7
 * @since 1.9  Renamed from \Rectangle2D
 */
class Rectangle2D extends Rectangle
{
	use PositionTrait;

	/**
	 * Class constructor.
	 *
	 * @param  float  $x  The rectangle X position.
	 * @param  float  $y  The rectangle Y position.
	 * @param  float  $w  The rectangle width.
	 * @param  float  $h  The rectangle height.
	 */
	public function __construct(float $x = 0, float $y = 0, float $w = 0, float $h = 0)
	{
		parent::__construct($w, $h);
		$this->setPoint(new Point($x, $y));
	}

	/**
	 * @inheritDoc
	 * 
	 * Shifts the default rectangle centroid by the position of this shape.
	 */
	public function centroid()
	{
		$center = parent::getCentroid();

		// shift the centroid by the position of the rectangle
		$center->setX($this->getX() + $center->x);
		$center->setY($this->getY() + $center->y);

		return $center;
	}

	/**
	 * @inheritDoc
	 * 
	 * Applies the position set for the 2D rectangle.
	 * 
	 * @since 1.9
	 */
	public function containsPoint(Point $point)
	{
		return $this->getX() <= $point->x && $point->x <= $this->getX() + $this->getWidth()
			&& $this->getY() <= $point->y && $point->y <= $this->getY() + $this->getHeight();
	}
}
