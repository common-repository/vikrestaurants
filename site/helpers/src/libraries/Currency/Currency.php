<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Currency;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Currency class handler.
 *
 * @since 1.9
 */
class Currency
{
	/**
	 * The currency code (see ISO 4217).
	 *
	 * @var string
	 */
	private $code;

	/**
	 * The currency symbol.
	 *
	 * @var string
	 */
	private $symbol;

	/**
	 * The position of the currency symbol.
	 *
	 * @var int
	 */
	private $position;

	/**
	 * The type of separator to use (comma or period).
	 *
	 * @var int
	 */
	private $separator;

	/**
	 * The character to use as decimal separator.
	 *
	 * @var string
	 */
	private $decimalMark;

	/**
	 * The character to use as thousands separator.
	 *
	 * @var string
	 */
	private $thousandsMark;

	/**
	 * The number of decimal digits to use.
	 *
	 * @var int
	 */
	private $decimalDigits;

	/**
	 * True to include a space between the symbol and the amount.
	 *
	 * @var bool
	 */
	private $space;

	/**
	 * Class constructor.
	 *
	 * @param  string|array  $options  A configuration array or the currency code.
	 */
	public function __construct($options)
	{
		if (is_string($options))
		{
			// a currency code was provided
			$options = ['code' => $options];
		}

		if (!isset($options['code']))
		{
			throw new \InvalidArgumentException('Missing ISO 4217 currency code');
		}

		// set up currency code
		$this->setCode($options['code'])
			->setSymbol($options['symbol'] ?? $options['code'])
			->setPosition($options['position'] ?? self::BEFORE_POSITION)
			->setSeparator($options['separator'] ?? self::PERIOD_SEPARATOR)
			->setDecimalDigits($options['digits'] ?? 2)
			->setSpace($options['space'] ?? true);
	}

	/**
	 * Get the currency code.
	 *
	 * @return  string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Set the currency code (see ISO 4217).
	 *
	 * @param   string  $code  The code.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setCode(string $code)
	{
		$this->code = strtoupper(substr($code, 0, 3));

		return $this;
	}

	/**
	 * Get the currency symbol.
	 *
	 * @return  string
	 */
	public function getSymbol()
	{
		return $this->symbol;
	}

	/**
	 * Set the currency symbol.
	 *
	 * @param   string  $symbol  The symbol.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setSymbol($symbol)
	{
		$this->symbol = $symbol;

		return $this;
	}

	/**
	 * Check if the symbol should be displayed before the amount.
	 * 
	 * @param   int   $pos  Optionally override the default position.
	 *
	 * @return  bool
	 */
	public function isSymbolBefore(int $pos = 0)
	{
		$pos = $pos ?: $this->position;

		return abs($pos) == self::BEFORE_POSITION;
	}

	/**
	 * Check if the symbol should be displayed after the amount.
	 * 
	 * @param   int   $pos  Optionally override the default position.
	 *
	 * @return  bool
	 */
	public function isSymbolAfter(int $pos = 0)
	{
		return $this->isSymbolBefore($pos) === false;
	}

	/**
	 * Get the position of the currency symbol.
	 *
	 * @return  int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set the position of the currency symbol.
	 *
	 * @param   int   $position  The position.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setPosition(int $position)
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Get the decimal mark to use.
	 *
	 * @return  string
	 */
	public function getDecimalMark()
	{
		// check whether a custom mark was specified
		if ($this->decimalMark && preg_match("/[.,\s]/", $this->decimalMark))
		{
			return $this->decimalMark;
		}

		return $this->separator == self::COMMA_SEPARATOR ? ',' : '.';
	}

	/**
	 * Get the thousands mark to use.
	 *
	 * @return  string
	 */
	public function getThousandsMark()
	{
		$mark = $this->separator == self::COMMA_SEPARATOR ? '.' : ',';

		// check whether a custom mark was specified
		if (!is_null($this->thousandsMark) && (empty($this->thousandsMark) || preg_match("/[.,\s]/", $this->thousandsMark)))
		{
			// use given separator
			$mark = $this->thousandsMark;
		}

		$decimal = $this->getDecimalMark();

		// make sure the separators are not equal
		if ($this->thousandsMark == $decimal)
		{
			// use comma in case the decimal mark uses a dot and viceversa
			$mark = $decimal == ',' ? '.' : ',';
		}

		return $mark;
	}

	/**
	 * Set the type of separator to use (comma or period).
	 *
	 * @param   mixed  $separator  The separator.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setSeparator($separator)
	{
		if (is_array($separator) && count($separator) == 2)
		{
			// set custom separators
			$this->decimalMark   = substr($separator[0], 0, 1);
			$this->thousandsMark = substr($separator[1], 0, 1);
		}
		else
		{
			if (is_string($separator))
			{
				// get separator code
				$separator = $separator == ',' ? self::COMMA_SEPARATOR : self::PERIOD_SEPARATOR;
			}

			$this->separator = (int) $separator;
		}

		return $this;
	}

	/**
	 * Get the number of decimal digits.
	 *
	 * @return  int
	 */
	public function getDecimalDigits()
	{
		return $this->decimalDigits;
	}

	/**
	 * Set the number of decimal digits to use.
	 *
	 * @param   int   $decimalDigits  The decimal digits.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setDecimalDigits(int $decimalDigits)
	{
		$this->decimalDigits = $decimalDigits;

		return $this;
	}

	/**
	 * Check whether the currency should use a space between the
	 * currency symbol and the amount.
	 *
	 * @return  bool
	 */
	public function isSpace()
	{
		return $this->space;
	}

	/**
	 * Sets/unsets the space between the currency symbol and the amount.
	 *
	 * @param   bool  $space  True to include the space.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setSpace(bool $space)
	{
		$this->space = $space;

		return $this;
	}

	/**
	 * Convert the specified amount in a price string.
	 *
	 * @param   float   $amount   The amount to format.
	 * @param   array   $options  On-the-fly formatting options.
	 *
	 * @return  string  The formatted price.
	 */
	public function format(float $amount = 0.0, array $options = [])
	{
		$options['digits']          = $options['digits']         ??  $this->getDecimalDigits();
		$options['decimal_mark']    = $options['decimal_mark']   ??  $this->getDecimalMark();
		$options['thousands_mark']  = $options['thousands_mark'] ??  $this->getThousandsMark();
		$options['symbol']          = $options['symbol']         ??  $this->getSymbol();
		$options['position']        = $options['position']       ??  $this->getPosition();
		$options['space']           = $options['space']          ??  $this->isSpace();
		$options['no_decimal']      = $options['no_decimal']     ??  false;

		// if the decimals are optional and the amount is an integer, hide them
		if ($options['no_decimal'] && (int) $amount == $amount)
		{
			$options['digits'] = 0;
		}

		// format the number
		$amount = number_format(
			$amount,
			$options['digits'],
			$options['decimal_mark'],
			$options['thousands_mark']
		);

		if ($this->isSymbolBefore($options['position']))
		{
			return $options['symbol'] . ($options['space'] ? ' ' : '') . $amount;
		}

		return $amount . ($options['space'] ? ' ' : '') . $options['symbol'];
	}

	/**
	 * Constant to display the currency symbol after the price.
	 *
	 * @var int
	 */
	const AFTER_POSITION = 1;

	/**
	 * Constant to display the currency symbol before the price.
	 *
	 * @var int
	 */
	const BEFORE_POSITION = 2;

	/**
	 * Constant to use the comma (,) as decimal separator.
	 *
	 * @var int
	 */
	const COMMA_SEPARATOR = 1;

	/**
	 * Constant to use the period (.) as decimal separator.
	 *
	 * @var int
	 */
	const PERIOD_SEPARATOR = 2;
}
