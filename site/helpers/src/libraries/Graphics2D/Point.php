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
 * Handles geometric points.
 *
 * @since 1.7
 * @since 1.9 Renamed from \Point
 */
class Point
{
	/**
	 * The X position of the point.
	 *
	 * @var float
	 */
	public $x;

	/**
	 * The Y position of the point.
	 *
	 * @var float
	 */
	public $y;

	/**
	 * Class constructor.
	 *
	 * @param   float  $x  The X point position.
	 * @param   float  $y  The Y point position.
	 */
	public function __construct(float $x = 0, float $y = 0)
	{
		$this->setLocation($x, $y);
	}

	/**
	 * Sets the X and Y position of the point.
	 *
	 * @param   float  $x  The X point position.
	 * @param   float  $y  The Y point position.
	 *
	 * @return  self   This object to support chaining.
	 */
	final public function setLocation(float $x, float $y)
	{
		$this->setX($x)->setY($y);

		return $this;
	}

	/**
	 * Sets the X (horizontal) position of the point.
	 *
	 * @param   float  $x  The X point position.
	 *
	 * @return  self   This object to support chaining.
	 */
	final public function setX(float $x)
	{
		$this->x = $x;

		return $this;
	}

	/**
	 * Sets the Y (vertical) position of the point.
	 *
	 * @param   float  $y  The Y point position.
	 *
	 * @return  self   This object to support chaining.
	 */
	final public function setY(float $y)
	{
		$this->y = $y;

		return $this;
	}

	/**
	 * Gets the X (horizontal) position of the point.
	 *
	 * @return  float  The point X position.
	 */
	final public function getX()
	{
		return $this->x;
	}

	/**
	 * Gets the Y (vertical) position of the point.
	 *
	 * @return  float  The point Y position.
	 */
	final public function getY()
	{
		return $this->y;
	}

	/**
	 * Checks if this point is equals to the given point.
	 * 
	 * @param   Point  $point  The point to compare.
	 *
	 * @return  bool   True if they are equal, otherwise false.
	 */
	public function equalsTo(Point $point)
	{
		return $this->x == $point->x && $this->y == $point->y;
	}

	/**
	 * Gets the distance between this point and the given point.
	 * 
	 * @param   Point  $point  The point to compare.
	 *
	 * @return  float  The distance between the 2 points.
	 */
	public function getDistance(Point $point)
	{
		return self::getDistanceBetweenPoints($this, $point);
	}

	/**
	 * Calculates the distance between 2 given points.
	 * This function assumes that the points are located on a geometric place (2D).
	 *
	 * The distance is calculated with the formula:
	 * D = √( (x1 - x2)^2 + (y1 - y2)^2 )
	 * 
	 * @param   Point  $p1  The first point.
	 * @param   Point  $p2  The second point.
	 *
	 * @return  float  The distance between the 2 points.
	 */
	public static function getDistanceBetweenPoints(Point $p1, Point $p2)
	{
		return sqrt( pow($p1->x - $p2->x, 2) + pow($p1->y - $p2->y, 2) );
	}

	/**
	 * Gets the medium point between this point and the given point.
	 * 
	 * @param   Point  $point  The point to compare.
	 *
	 * @return  Point  The medium point between the 2 points.
	 */
	public function getMediumPoint(Point $point)
	{
		return self::getMediumBetweenPoints($this, $point);
	}

	/**
	 * Calculates the medium point between 2 given points.
	 * This function assumes that the points are located on a geometric place (2D).
	 *
	 * The medium point is calculated with the formula:
	 *     x1 + x2   y1 + y2
	 * M = ------- ; -------
	 *        2         2
	 * 
	 * @param   Point  $p1  The first point.
	 * @param   Point  $p2  The second point.
	 *
	 * @return  Point  The medium point between the 2 points.
	 */
	public static function getMediumBetweenPoints(Point $p1, Point $p2)
	{
		return new Point( ($p1->x + $p2->x) / 2, ($p1->y + $p2->y) / 2 );
	}
}
