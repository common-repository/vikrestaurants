<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Used to handle the take-away item group topping into the cart.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\TakeAway\Cart\Item\Topping instead.
 */
class TakeAwayItemGroupTopping extends E4J\VikRestaurants\TakeAway\Cart\Item\Topping
{	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id_topping  The ID of the topping.
	 * @param 	integer  $id_assoc 	  The associative ID of the topping.
	 * @param 	string 	 $name 		  The name of the topping.
	 * @param 	float 	 $rate 		  The cost of the topping.
	 * @param 	integer  $units       An optional number of units.
	 */
	public function __construct($id_topping, $id_assoc, $name, $rate, $units = 1)
	{
		parent::__construct([
			'id'       => $id_topping,
			'id_assoc' => $id_assoc,
			'name'     => $name,
			'rate'     => $rate,
			'units'    => $units,
		]);
	}
	
	/**
	 * Magic toString method to debug the topping contents.
	 *
	 * @return  string  The debug string of this object.
	 *
	 * @since   1.7
	 */
	public function __toString()
	{
		return '<pre>' . print_r($this, true) . '</pre>';
	}
}
