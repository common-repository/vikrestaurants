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
 * This class is used to handle American Express credit cards.
 *
 * @since  1.7
 */
class CCAmericanExpress extends CreditCard
{
	/**
	 * Check if the credit card number is valid.
	 * The card number is valid when its length is equals to 15.
	 *
	 * @return 	boolean 	True if the card number is valid.
	 */
	public function isCardNumberValid()
	{
		return ( strlen($this->getCardNumber()) == $this->getCardNumberDigits() );
	}

	/**
	 * Get the credit card number digits count.
	 *
	 * @return 	integer 	Return the digits count (15).
	 */
	public function getCardNumberDigits()
	{
		return 15;
	}

	/**
	 * Format the credit card number to be more human-readable.
	 * e.g. 3434 000000 00000
	 *
	 * @return 	string 	The formatted card number. 
	 */
	public function formatCardNumber()
	{
		$cc = $this->getCardNumber();

		return substr($cc, 0, 4).' '.substr($cc, 4, 6).' '.substr($cc, 10, 5);
	}

	/**
	 * Get a masked version of the credit card for privacy.
	 * e.g. **** ****** 00000
	 * e.g. 3434 000000 *****
	 *
	 * @return 	array 	A list containing 2 different masked versions of card number. 
	 */
	public function getMaskedCardNumber()
	{
		return array(
			'**** ****** '.substr($this->getCardNumber(), 10, 5),
			substr($this->getCardNumber(), 0, 4).' '.substr($this->getCardNumber(), 4, 6).' *****'
		);
	}

	/**
	 * Get the American Express alias.
	 *
	 * @return 	string 	The alias of the credit card brand (amex).
	 */
	public function getBrandAlias()
	{
		return CreditCard::AMERICAN_EXPRESS;
	}

	/**
	 * Get the name of the credit card brand.
	 *
	 * @return 	string 	The name of the credit card brand (American Express).
	 */
	public function getBrandName()
	{
		return 'American Express';
	}
}
