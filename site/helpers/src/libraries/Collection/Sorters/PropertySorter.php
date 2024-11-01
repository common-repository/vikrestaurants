<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection\Sorters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Generic class to sort 2 items according to the comparison of the specified property.
 * 
 * @since 1.9
 */
abstract class PropertySorter extends Reversable
{
	/** @var string */
	protected $name;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $name       The name of the property to compare.
	 * @param  string  $direction  ASC or DESC (case-insensitive).
	 */
	public function __construct(string $name, string $direction = 'ASC')
	{
		$this->name = $name;

		parent::__construct($direction);
	}
}
