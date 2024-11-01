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
 * Handles geometric lines.
 *
 * @since 1.7
 * @since 1.9  Renamed from \Line
 */
class Line
{
	/**
	 * First point of the line.
	 *
	 * @var Point
	 */
	private $p1;

	/**
	 * Second point of the line.
	 *
	 * @var Point
	 */
	private $p2;

	/**
	 * Class constructor.
	 *
	 * @param  float  $x1  The first X of the line.
	 * @param  float  $y1  The first Y of the line.
	 * @param  float  $x2  The second X of the line.
	 * @param  float  $y2  The second Y of the line.
	 */
	public function __construct($x1, $y1, $x2, $y2)
	{
		$this->setPoints(
			new Point($x1, $y1), 
			new Point($x2, $y2)
		);
	}

	/**
	 * Sets the first point and the second point of the line.
	 *
	 * @param   Point  $p1  The first point of the line.
	 * @param   Point  $p2  The second point of the line.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setPoints(Point $p1 = null, Point $p2 = null)
	{
		if ($p1 === null)
		{
			$p1 = new Point();
		}

		if ($p2 === null)
		{
			$p2 = new Point();
		}

		$this->p1 = $p1;
		$this->p2 = $p2;

		return $this;
	}

	/**
	 * Gets a list containing the first point and second point of the line.
	 *
	 * @return  array  A list of points.
	 */
	public function getPoints()
	{
		return [$this->p1, $this->p2];
	}

	/**
	 * Gets the first point of the line.
	 *
	 * @return  Point  The first point.
	 */
	public function getFirstPoint()
	{
		return $this->p1;
	}

	/**
	 * Gets the second point of the line.
	 *
	 * @return  Point  The second point.
	 */
	public function getSecondPoint()
	{
		return $this->p2;
	}

	/**
	 * Gets the start X position of the line.
	 * The start X is the lowest value between the X coordinate of the 2 points.
	 *
	 * @return  float  The start X position.
	 */
	public function getStartX()
	{
		return min($this->p1->x, $this->p2->x);
	}

	/**
	 * Gets the end X position of the line.
	 * The end X is the highest value between the X coordinate of the 2 points.
	 *
	 * @return  float  The end X position.
	 */
	public function getEndX()
	{
		return max($this->p1->x, $this->p2->x);
	}

	/**
	 * Gets the start Y position of the line.
	 * The start Y is the lowest value between the Y coordinate of the 2 points.
	 *
	 * @return  float  The start Y position.
	 */
	public function getStartY()
	{
		return min($this->p1->y, $this->p2->y);
	}

	/**
	 * Gets the end Y position of the line.
	 * The end Y is the highest value between the Y coordinate of the 2 points.
	 *
	 * @return  float  The end Y position.
	 */
	public function getEndY()
	{
		return max($this->p1->y, $this->p2->y);
	}

	/**
	 * Checks if this line intersects with the specified line.
	 *
	 * @param   Line  $line  The line object to check for.
	 *
	 * @return 	bool  True if they intersect, otherwise false.
	 */
	public function intersect(Line $line)
	{
		return self::linesIntersection($this, $line);
	}

	/**
	 * Checks if the 2 given lines intersect each other.
	 *
	 * @param   Line  $l1  The first line object.
	 * @param   Line  $l2  The second line object.
	 *
	 * @return  bool  True if they intersect, otherwise false.
	 */
	public static function linesIntersection(Line $l1, Line $l2)
	{
		$p0_x = $l1->getFirstPoint()->x;
		$p0_y = $l1->getFirstPoint()->y;

		$p1_x = $l1->getSecondPoint()->x;
		$p1_y = $l1->getSecondPoint()->y;

		$p2_x = $l2->getFirstPoint()->x;
		$p2_y = $l2->getFirstPoint()->y;

		$p3_x = $l2->getSecondPoint()->x;
		$p3_y = $l2->getSecondPoint()->y; 
		
		$s1_x = $p1_x - $p0_x;
		$s1_y = $p1_y - $p0_y;

		$s2_x = $p3_x - $p2_x;
		$s2_y = $p3_y - $p2_y;

		if (-$s2_x * $s1_y + $s1_x * $s2_y == 0)
		{
			// collision detected -> one point of segments in common
			return true;
		}

		$s = (-$s1_y * ($p0_x - $p2_x) + $s1_x * ($p0_y - $p2_y)) / (-$s2_x * $s1_y + $s1_x * $s2_y);
		$t = ( $s2_x * ($p0_y - $p2_y) - $s2_y * ($p0_x - $p2_x)) / (-$s2_x * $s1_y + $s1_x * $s2_y);

		if ($s >= 0 && $s <= 1 && $t >= 0 && $t <= 1)
		{
			// collision detected
			return true;
		}

		// no collision
		return false;
	}
}
