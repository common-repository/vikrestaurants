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

use E4J\VikRestaurants\Currency\Currency;

/**
 * Currency class handler.
 *
 * @since 1.8
 * 
 * @deprecated 1.10  Use E4J\VikRestaurants\Currency\Currency instead.
 */
class VRECurrency extends Currency
{
	/**
	 * Class constructor.
	 *
	 * @param 	string 	 $code
	 * @param 	string 	 $symbol
	 * @param 	integer  $position
	 * @param 	mixed    $separator
	 * @param 	integer  $decimalDigits
	 */
	public function __construct($code, $symbol, $position = self::BEFORE_POSITION, $separator = self::PERIOD_SEPARATOR, $decimalDigits = 2)
	{
		parent::__construct([
			'code'      => $code,
			'symbol'    => $symbol,
			'position'  => $position,
			'separator' => $separator,
			'digits'    => $decimalDigits,
		]);	
	}
}
