<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\DeliveryArea\Area;

/**
 * Filters the delivery areas to obtain only the ones that match the specified types.
 * 
 * @since 1.9
 */
class TypesFilter implements CollectionFilter
{
	/** @var string[] */
	protected $types;

	/**
	 * Class constructor.
	 */
	public function __construct($types)
	{
		$this->types = (array) $types;
	}

	/**
	 * @inheritDoc
	 * 
	 * @throws  \InvalidArgumentException  Only Area instances are accepted.
	 */
	public function match(Item $item)
	{
		if (!$item instanceof Area)
		{
			// can handle only objects that inherit the Area class
			throw new \InvalidArgumentException('Area item expected, ' . get_class($item) . ' given');
		}

		// make sure this delivery area type is accepted
		return in_array($item->get('type'), $this->types);
	}
}
