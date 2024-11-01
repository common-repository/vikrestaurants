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
 * Handles 2D polygon shapes.
 *
 * @since 1.7
 * @since 1.9  Renamed from \Polygon
 */
class Polygon2D implements Shape
{
	/**
	 * The list containing all the polygon corners.
	 * It is not needed to push the first corner at the end to close the chain,
	 * as the system always close it with a segment from the last corner to the first corner.
	 *
	 * Do not repeat the first corner as last, otherwise there will be a segment with no length,
	 * even if this do not corrupt the functionality.
	 *
	 * @var array
	 */
	private $coordinates = [];

	/**
	 * Class constructor
	 *
	 * @param  array  $coordinates  The list containing all the corners.
	 */
	public function __construct($coordinates = [])
	{
		$this->setPoints($coordinates);
	}

	/**
	 * Sets the list of corners.
	 *
	 * @param   Point[]  $points  The list of corners.
	 * @param   bool     $clear   True to reset the list, false otherwise to append the new corners.
	 *
	 * @return 	self     This object to support chaining.
	 */
	public function setPoints(array $points, bool $clear = true)
	{
		if ($clear || !is_array($this->coordinates))
		{
			$this->coordinates = [];
		}

		foreach ($points as $point)
		{
			$this->addPoint($point);
		}

		return $this;
	}

	/**
	 * Adds a corner into the current list.
	 *
	 * @param   Point  $point  The corner to push.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function addPoint(Point $point)
	{
		$this->coordinates[] = $point;

		return $this;
	}

	/**
	 * Gets the corner at the specified position.
	 *
	 * @param   int    $index  The index of the corner.
	 *
	 * @return  Point  The corner found, otherwise NULL.
	 */
	public function getPoint($index)
	{
		if ($index >= 0 && $index < count($this->coordinates))
		{
			return $this->coordinates[$index];
		}

		return null;
	}

	/**
	 * Gets the list containing all the corners.
	 *
	 * @return  Point[]  The list of corners.
	 */
	public function getPoints()
	{
		return $this->coordinates;
	}

	/**
	 * Gets the index of the specified corner.
	 *
	 * @param   Point  $point  The point to search for.
	 *
	 * @return  int    The index found, otherwise -1.
	 */
	public function indexOf(Point $point)
	{
		foreach ($this->coordinates as $index => $corner)
		{
			if ($corner->equalsTo($point))
			{
				return $index;
			}
		}

		return -1;
	}

	/**
	 * Gets the total count of corners in the list.
	 *
	 * @return  int  The corners count.
	 */
	public function getNumPoints()
	{
		return count($this->coordinates);
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the perimeter of the polygon by summing all the distances
	 * found between the contiguous corners.
	 */
	public function perimeter()
	{
		$perimeter = 0;

		// exclude the last point because it have to be considered with the first point
		for ($i = 0; $i < count($this->coordinates) - 1; $i++)
		{
			// get the distance between contiguous points
			$perimeter += $this->coordinates[$i]->getDistance($this->coordinates[$i + 1]);
		}

		// close the chain from the last point to the first one
		$perimeter += $this->coordinates[0]->getDistance($this->coordinates[count($this->coordinates) - 1]);

		return $perimeter;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the area of the polygon with the algorithm below:
	 *
	 * V = [ (-3, -2), (-1, 4), (6, 1), (3, 10), (-4, 9) ] -> list of polygon corners
	 *
	 * --- STEP #1 ---
	 * List the x and y coordinates of each vertex of the polygon in counterclockwise order.
	 * Repeat the coordinates of the first point at the end of the list.
	 *
	 * Lx = [-3, -1, 6,  3, -4, -3] -> list with x coords (first x repeated at the end)
	 *
	 * Ly = [-2,  4, 1, 10,  9, -2] <- list with y coords (first y repeated at the end)
	 *
	 * --- STEP #2 ---
	 * Multiply the x coordinate of each vertex by the y coordinate of the next (index + 1) vertex.
	 *
	 * S1 = SUM i->1 to n-1 (Lx[i] * Ly[i+1]) -> -3*4 + -1*1 + 6*10 + 3 *9 + -4*-2 = 82
	 *
	 * --- STEP #3 ---
	 * Multiply the y coordinate of each vertex by the x coordinate of the next (index + 1) vertex.
	 *
	 * S2 = SUM i->1 to n-1 (Ly[i] * Lx[i+1]) -> -2*-1 + 4*6 + 1*3 + 10*-4 + 9*-3 = -38
	 *
	 * --- STEP #4 ---
	 * Subtract the sum of the second products from the sum of the first products and divide this difference by 2.
	 *
	 * A = (S1 - S2) / 2 -> (82 - (-38)) / 2 = (82 + 38) / 2 = 120 / 2 = 60 
	 */
	public function area()
	{
		$area_1 = 0;
		$area_2 = 0;

		for ($i = 0; $i < count($this->coordinates) - 1; $i++)
		{
			$area_1 += $this->coordinates[$i]->x * $this->coordinates[$i + 1]->y;
			$area_2 += $this->coordinates[$i]->y * $this->coordinates[$i + 1]->x;
		}

		$area_1 += $this->coordinates[count($this->coordinates) - 1]->x * $this->coordinates[0]->y;
		$area_2 += $this->coordinates[count($this->coordinates) - 1]->y * $this->coordinates[0]->x;
 
		// NOTE: if we apply the algorhitm in clockwise order, we will get the same value but with negative sign.
		// Therefore it is needed to get the absolute value to have always a positive amount.
		return abs($area_1 - $area_2) / 2;
	}

	/**
	 * @inheritDoc
	 * 
	 * Calculates the centroid of the polygon by getting the average x and y coordinates.
	 *
	 * V = [ (-3, -2), (-1, 4), (6, 1), (3, 10), (-4, 9) ] -> list of polygon corners
	 *
	 * --- STEP #1 ---
	 * List the x and y coordinates of each vertex of the polygon in counterclockwise order.
	 *
	 * Lx = [-3, -1, 6,  3, -4] -> list with x coords
	 *
	 * Ly = [-2,  4, 1, 10,  9] <- list with y coords
	 *
	 * --- STEP #2 ---
	 * Sum each value within the 2 lists.
	 *
	 * Sx = SUM i->1 to n (Lx[i]) -> -3 + (-1) + 6 + 3 + (-4) = 1
	 *
	 * Sy = SUM i->1 to n (Ly[i]) -> -2 + 4 + 1 + 10 + 9 = 22
	 *
	 * --- STEP #3 ---
	 *
	 * Divide the 2 sums by the count of the corners.
	 *
	 * Xavg = Sx / n -> 1 / 5 = 0.2
	 *
	 * Yavg = Sy / n -> 22 / 5 = 4.4
	 */
	public function centroid()
	{
		$p = new Point(0, 0);

		foreach ($this->coordinates as $coord)
		{
			$p->x += $coord->x;
			$p->y += $coord->y;
		}

		$p->x /= count($this->coordinates);
		$p->y /= count($this->coordinates);

		return $p;
	}

	/**
	 * @inheritDoc
	 * 
	 * @since 1.9
	 */
	public function containsPoint(Point $point)
	{
		// use the geometry helper to check whether a polygon contains the provided point
		return GeometryHelper::isPointInsidePolygon($this, $point, GeometryHelper::WINDING_NUMBER);
	}

	/**
	 * Gets the rectangle that bounds the polygon.
	 * The x position of the rectangle is the corner with lowest x.
	 * The y position of the rectangle is the corner with lowest y.
	 * The width of the rectangle is the corner with highest x minus lowest x.
	 * The height of the rectangle is the corner with highest y minus lowest y.
	 *
	 * @return  Rectangle2D  The bounds of the polygon.
	 */
	public function getBounds()
	{
		if (($numPoints = $this->getNumPoints()) === 0)
		{
			return new Rectangle2D();
		}

		$min = array($this->coordinates[0]->x, $this->coordinates[0]->y);
		$max = array($this->coordinates[0]->x, $this->coordinates[0]->y);

		for ($i = 1; $i < $numPoints; $i++)
		{
			$min[0] = min($min[0], $this->coordinates[$i]->x);
			$min[1] = min($min[1], $this->coordinates[$i]->y);

			$max[0] = max($max[0], $this->coordinates[$i]->x);
			$max[1] = max($max[1], $this->coordinates[$i]->y);
		}

		return new Rectangle2D($min[0], $min[1], $max[0] - $min[0], $max[1] - $min[1]);
	}
}
