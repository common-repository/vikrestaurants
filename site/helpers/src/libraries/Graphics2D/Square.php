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
 * Handles square shapes.
 *
 * @since 1.7
 * @since 1.9  Renamed from \Square
 */
class Square extends Rectangle
{
	/**
	 * Class constructor.
	 *
	 * @param  float  $side  The side of the square.
	 */
	public function __construct(float $side = 0)
	{
		parent::__construct($side, $side);
	}

	/**
	 * @inheritDoc
	 */
	final public function setWidth(float $width)
	{
		$this->setSide($width);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	final public function setHeight(float $height)
	{
		$this->setSide($height);

		return $this;
	}

	/**
	 * Sets the side of the square. This method will affect both the width the height.
	 *
	 * @param   float  $side  The side of the square.
	 *
	 * @return 	self   This object to support chaining.
	 */
	public function setSide(float $side)
	{
		parent::setWidth($side);
		parent::setHeight($side);

		return $this;
	}

	/**
	 * Gets the side of the square.
	 *
	 * @return  float  The square side.
	 */
	public function getSide()
	{
		// getWidth() or getHeight() will return always the same value.
		return $this->getWidth();
	}
}
