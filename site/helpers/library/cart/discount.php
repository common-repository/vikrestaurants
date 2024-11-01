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
 * Used to handle the take-away discount into the cart.
 *
 * @since 1.7
 * @deprecated 1.10  Use E4J\VikRestaurants\TakeAway\Cart\Deals\Discount instead.
 */
class TakeAwayDiscount extends E4J\VikRestaurants\TakeAway\Cart\Deals\Discount
{	
	/**
	 * Class constructor.
	 *
	 * @param 	integer  $id_deal 	 The ID of the deal.
	 * @param 	float 	 $amount 	 The amount of the deal.
	 * @param 	integer  $percentot  The amount type of the deal.
	 * @param 	integer  $quantity 	 The quantity of the deal.
	 * @param   string   $type       The deal type identifier.
	 */
	public function __construct($id_deal, $amount, $percentot, $quantity = 1, $type = null)
	{
		parent::__construct([
			'id'       => $id_deal,
			'amount'   => $amount,
			'percent'  => $percentot === static::PERCENTAGE_AMOUNT_TYPE,
			'quantity' => $quantity,
			'type'     => $type,
		]);
	}
	
	/**
	 * Get the amount type of the deal.
	 *
	 * @return 	integer  The deal amont type.
	 */
	public function getPercentOrTotal()
	{
		return $this->isPercent() ? static::PERCENTAGE_AMOUNT_TYPE : static::TOTAL_AMOUNT_TYPE;
	}
	
	/**
	 * Magic toString method to debug the discount contents.
	 *
	 * @return  string  The debug string of this object.
	 *
	 * @since   1.7
	 */
	public function __toString()
	{
		return '<pre>' . print_r($this, true) . '</pre>';
	}

	/**
	 * PERCENTAGE amount type identifier.
	 *
	 * @var integer
	 *
	 * @since 1.7
	 */
	const PERCENTAGE_AMOUNT_TYPE = 1;

	/**
	 * TOTAL amount type identifier.
	 *
	 * @var integer
	 *
	 * @since 1.7
	 */
	const TOTAL_AMOUNT_TYPE = 2;
}
