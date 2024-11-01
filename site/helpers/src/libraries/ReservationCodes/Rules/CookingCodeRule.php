<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\ReservationCodes\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\ReservationCodes\CodeRule;

/**
 * Flags a food item as under preparation.
 *
 * @since 1.9
 */
class CookingCodeRule extends CodeRule
{
	/**
	 * The group where we are working on.
	 * 
	 * @var string
	 * @since 1.9.1
	 */
	protected $group;

	/**
	 * @inheritDoc
	 * 
	 * Available only for food items.
	 */
	public function isSupported(string $group)
	{
		$this->group = $group;

		return !strcasecmp($group, 'food')
			|| !strcasecmp($group, 'tkfood');
	}

	/**
	 * @inheritDoc
	 */
	public function execute($food)
	{
		if (is_object($food))
		{
			// extract ID from food object
			$food = $food->id;
		}
		else if (is_array($food))
		{
			// extract ID from foor array
			$food = $food['id'];
		}

		if ($this->group === 'tkfood')
		{
			$model = \JModelVRE::getInstance('tkresprod');
		}
		else
		{
			$model = \JModelVRE::getInstance('resprod');
		}

		// cooking the item
		$model->save([
			'id'     => $food,
			'status' => 0, // 0: cooking
		]);
	}
}
