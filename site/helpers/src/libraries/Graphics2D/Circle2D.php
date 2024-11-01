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
 * Handles 2D circle shapes (position and radius).
 *
 * @since 1.9
 */
class Circle2D extends Circle
{
	use PositionTrait;

	/**
	 * Class constructor.
	 *
	 * @param  float  $radius  The radius of the circle.
	 * @param  float  $x       The x center of the circle.
	 * @param  float  $y       The y center of the circle.
	 */
	public function __construct(float $radius, float $x = 0, float $y = 0)
	{
		parent::__construct($radius);
		$this->setCenter(new Point($x, $y));
	}

	/**
	 * Alias for `setPoint()` method.
	 *
	 * @param   Point  $center  The center of the circle.
	 *
	 * @return  self   This object to support chaining.
	 */
	final public function setCenter(Point $center)
	{
		return $this->setPoint($center);
	}

	/**
	 * Alias for `getPoint()` method.
	 *
	 * @return  Point  The circle center.
	 */
	final public function getCenter()
	{
		return $this->getPoint();
	}

	/**
	 * @inheritDoc
	 * 
	 * The centroid is always equals to the center of the circle.
	 */
	public function centroid()
	{
		return $this->getCenter();
	}
}
