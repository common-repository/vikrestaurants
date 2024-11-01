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
 * This interface declares the basic mathods that all the 2D geometric shapes should implement.
 *
 * @since 1.7
 * @since 1.9 Renamed from \Shape
 */
interface Shape
{
	/**
	 * Returns the perimeter of the shape.
	 *
	 * @return  float  The shape perimeter.
	 */
	public function perimeter();

	/**
	 * Returns the area of the shape.
	 *
	 * @return  float  The shape area.
	 */
	public function area();

	/**
	 * Returns the center of the shape.
	 *
	 * @return  Point  The X and Y center of the shape.
	 */
	public function centroid();

	/**
	 * Checks whether the shape contains the specified point.
	 * 
	 * @param   Point  The point to check.
	 * 
	 * @return  bool   True if in, false if out.
	 * 
	 * @since   1.9
	 */
	public function containsPoint(Point $point);
}
