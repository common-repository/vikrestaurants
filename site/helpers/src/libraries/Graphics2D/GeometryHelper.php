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
 * Helper class to deal with geometric problems.
 *
 * @since 1.7
 * @since 1.9  Renamed from \Geom
 */
abstract class GeometryHelper
{
	/**
	 * Generates a polygon following the specified arguments.
	 *
	 * @param   int    $numCorners  The number of corners to generate.
	 * @param   float  $minX        The minimum x coordinate.
	 * @param   float  $maxX        The maximum x coordinate.
	 * @param   float  $minY        The minimum y coordinate.
	 * @param   float  $maxY        The maximum y coordinate.
	 *
	 * @return  Polygon2D
	 */
	public static function generateShape(int $numCorners, float $minX = 0, float $maxX = 100, float $minY = 0, float $maxY = 100)
	{
		$polygon = new Polygon2D();

		if (($maxX - $minX) * ($maxY - $minY) < abs($numCorners))
		{
			return $polygon;
		}

		for ($numCorners = abs($numCorners); $numCorners > 0; $numCorners--)
		{
			do
			{
				$p = new Point(rand($minX, $maxX), rand($minY, $maxY));
			} while ($polygon->indexOf($p) !== -1);


			$polygon->addPoint($p);
		}

		return $polygon;
	}
	
	/**
	 * Checks if a point lies on a line.
	 *
	 * @param   Line   $line   The line on which the point should lie.
	 * @param   Point  $point  The point to check.
	 *
	 * @return  bool   True if the point lies on the line, otherwise false.
	 */
	public static function isPointOnLine(Line $line, Point $point)
	{
		$dxc = $point->x - $line->getFirstPoint()->x;
		$dyc = $point->y - $line->getFirstPoint()->y;

		$dxl = $line->getSecondPoint()->x - $line->getFirstPoint()->x;
		$dyl = $line->getSecondPoint()->y - $line->getFirstPoint()->y;

		if ($dyc == 0 && $dyl == 0)
		{
			return $line->getStartX() <= $point->x && $point->x <= $line->getEndX();
		}

		if ($dxc == 0 && $dxl == 0)
		{
			return $line->getStartY() <= $point->y && $point->y <= $line->getEndY();
		}

		return !($dxc * $dyl - $dyc * $dxl);
	}

	/**
	 * Checks if a point is wrapped in a Polygon.
	 * The function uses always the even-odd method to understand if a point is inside a polygon.
	 *
	 * @param   Polygon2D  $shape  The polygon that should wrap the point.
	 * @param   Point      $point  The point to check.
	 * @param   int        $mode   The algorithm to use (1: CROSSING NUMBER, 2: WINDING NUMBER).
	 *
	 * @return  bool 	   True if the point is wrapped, false otherwise.
	 */
	public static function isPointInsidePolygon(Polygon2D $shape, Point $point, $mode = 1)
	{
		if ($mode == self::CROSSING_NUMBER)
		{
			return self::cn_Poly($shape, $point);
		}

		return self::wn_Poly($shape, $point);
	}

	/**
	 * Crossing Number method to check if a point is wrapped in a polygon.
	 * This methods may fail when the point is really close to at least one corner.
	 * Suggested use for low precision.
	 *
	 * @param   Polygon2D  $shape  The polygon that should wrap the point.
	 * @param   Point      $point  The point to check.
	 *
	 * @return  bool       True if the point is wrapped, otherwise false.
	 */
	protected static function cn_Poly(Polygon2D $shape, Point $point)
	{
		if ($shape->indexOf($point) !== -1)
		{
			// the point is a vertex of the polygon
			return true;
		}

		 // define the crossing number counter
		$cn = 0;

		// loop through all edges of the polygon (edge from V[i]  to V[i+1])
		for ($i = 0; $i < $shape->getNumPoints(); $i++)
		{
			$j = $i == $shape->getNumPoints() - 1 ? 0 : $i + 1;

			$v1 = $shape->getPoint($i);
			$v2 = $shape->getPoint($j);

			if( 
				// an upward crossing
				(($v1->y <= $point->y) && ($v2->y > $point->y))
				// a downward crossing
				|| (($v1->y > $point->y) && ($v2->y <=  $point->y))
			) { 
				// compute the actual edge-ray intersect x-coordinate
				$vt = ($point->y - $v1->y) / ($v2->y - $v1->y);
				if ($point->x < $v1->x + $vt * ($v2->x - $v1->x))
				{	
					// P.x < intersect
					$cn++; // a valid crossing of y=P.y right of P.x
				}
			}
		}

		return ($cn & 1); // 0 if even (out), and 1 if odd (in)
	}

	/**
	 * Winding Number method to check if a point is wrapped in a polygon.
	 * Suggested use for high precision.
	 *
	 * @param   Polygon2D  $shape  The polygon that should wrap the point.
	 * @param   Point      $point  The point to check.
	 *
	 * @return  bool       True if the point is wrapped, otherwise false.
	 */
	protected static function wn_Poly(Polygon2D $shape, Point $point)
	{
		if ($shape->indexOf($point) !== -1)
		{
			// the point is a vertex of the polygon
			return true;
		}

		// define the winding number counter
		$wn = 0;

		// loop through all edges of the polygon (edge from V[i] to V[i+1])
		for ($i = 0; $i < $shape->getNumPoints(); $i++)
		{
			$j = $i == $shape->getNumPoints() - 1 ? 0 : $i + 1;

			$v1 = $shape->getPoint($i);
			$v2 = $shape->getPoint($j);

			if ($v1->y <= $point->y)
			{
				// start y <= P.y
				if ($v2->y > $point->y)
				{
					// an upward crossing
					if (self::isLeft($v1, $v2, $point) > 0)
					{
						// P left of edge: we have a valid up intersection
						$wn++;
					}
				}
			}
			else
			{
				// start y > P.y (no test needed)
				if ($v2->y <= $point->y)
				{
					// a downward crossing 
					if (self::isLeft($v1, $v2, $point) < 0)
					{
						// P right of edge: we have a valid down intersection
						$wn--;
					}
				}
			}
		}
		
		return $wn;
	}

	/**
	 * Checks if a point is on the left side of a line.
	 *
	 * @param   Point  $p0  The first point of the line.
	 * @param   Point  $p1  The second point of the line.
	 * @param   Point  $p2  The point to check.
	 *
	 * @return 	bool   True if the point is on the left side of the line.
	 */
	protected static function isLeft(Point $p0, Point $p1, Point $p2)
	{
		return (($p1->x - $p0->x) * ($p2->y - $p0->y) - ($p2->x - $p0->x) * ($p1->y - $p0->y));
	}

	/**
	 * Checks if a point is inside a circle.
	 * A point is wrapped in a circle when the distance between the circle center 
	 * and the point is equals or higher than the radius of the circle.
	 *
	 * @param   Circle2D  $center  The circle that should wrap the point.
	 * @param   Point 	  $point   The point that should be wrapped.
	 *
	 * @return 	bool      True if the point is wrapped, otherwise false.
	 * 
	 * @deprecated 1.10   Use Circle::containsPoint() instead.
	 */
	public static function isPointInsideCircle(Circle2D $circle, Point $point)
	{
		return $circle->containsPoint($point);
	}

	/**
	 * Check if a point is inside a circle that lies on the Earth globe.
	 * A point is wrapped in a circle when the distance between the circle center 
	 * and the point is equals or higher than the radius of the circle.
	 *
	 * The distance between the circle center and the point need to be calculated differently
	 * as the globe is not a 2D geometric plane.
	 *
	 * @param   Circle2D  $circle  The circle that should wrap the point.
	 * @param   Point     $point   The point that should be wrapped.
	 *
	 * @return 	bool      True if the point is wrapped, otherwise false.
	 */
	public static function isPointInsideCircleOnEarth(Circle2D $circle, Point $point)
	{
		$lat_1 = $circle->getCenter()->y * pi() / 180.0;
		$lng_1 = $circle->getCenter()->x * pi() / 180.0;

		$lat_2 = $point->y * pi() / 180.0;
		$lng_2 = $point->x * pi() / 180.0;

		/**
		 * Distance between 2 coordinates:
		 * R = 6371 (Eart radius ~6371 km)
		 *
		 * coordinates in radians
		 * lat1, lng1, lat2, lng2
		 *
		 * Calculate the included angle fi
		 * fi = abs( lng1 - lng2 );
		 *
		 * Calculate the third side of the spherical triangle
		 * p = acos( 
		 *      sin(lat2) * sin(lat1) + 
		 *      cos(lat2) * cos(lat1) * 
		 *      cos( fi ) 
		 * )
		 * 
		 * Multiply the third side per the Earth radius (distance in km)
		 * D = p * R;
		 *
		 * MINIFIED EXPRESSION
		 *
		 * acos( 
		 *      sin(lat2) * sin(lat1) + 
		 *      cos(lat2) * cos(lat1) *
		 *      cos( abs(lng1-lng2) ) 
		 * ) * R
		 */

		return acos(
			sin($lat_2) * sin($lat_1) + 
			cos($lat_2) * cos($lat_1) *
			cos(abs($lng_1 - $lng_2))
		) * 6371 < $circle->getRadius();
	}

	/**
	 * Crossing Number method identifier.
	 *
	 * @var integer
	 */
	const CROSSING_NUMBER = 1;

	/**
	 * Winding Number method identifier.
	 *
	 * @var integer
	 */
	const WINDING_NUMBER = 2;
}
