<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Trait used to easily format the currencies.
 * 
 * @since 1.9
 */
trait CurrencyFormatter
{
	/**
	 * Converts the provided number into a formatted price.
	 * 
	 * @param   float   $number   The number to format.
	 * @param   array   $options  An array of formatting options.
	 * 
	 * @return  string  The formatted price.
	 */
	public function toCurrency(float $number, array $options = [])
	{
		return \VREFactory::getCurrency()->format($number, $options);
	}

	/**
	 * Converts the provided price into a plain decimal.
	 * 
	 * @param   string  $price  The formatted price.
	 * 
	 * @return  float   The plain decimal.
	 */
	public function toNumber(string $currency)
	{
		if (!preg_match("/[0-9,.]+(?:[.,][0-9]+)?/", $currency, $match))
		{
			// unable to find a match, try to cast it into a number
			return (float) $currency;
		}

		// extract the number from the regex
		$number = $match[0];
		
		if (\VREFactory::getCurrency()->getDecimalMark() === '.')
		{
			// remove any commas from the number
			$number = str_replace(',', '', $number);
		}
		else
		{
			// remove any dots from the number
			$number = str_replace('.', '', $number);
			// replace the decimal separator with a dot
			$number = str_replace(',', '.', $number);
		}

		return (float) $number;
	}
}
