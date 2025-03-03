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
 * This class is used to handle JCB credit cards.
 *
 * @since  1.7
 */
class CCJcb extends CreditCard
{
	/**
	 * Check if the credit card number is valid.
	 * The card number is valid when its length is equals to 16.
	 *
	 * @return 	boolean 	True if the card number is valid.
	 */
	public function isCardNumberValid()
	{
		return (strlen($this->getCardNumber()) == $this->getCardNumberDigits());
	}

	/**
	 * Get the credit card number digits count.
	 *
	 * @return 	integer 	Return the digits count (16).
	 */
	public function getCardNumberDigits()
	{
		return 16;
	}

	/**
	 * Format the credit card number to be more human-readable.
	 * e.g. 3535 0000 0000 0000
	 *
	 * @return 	string 	The formatted card number. 
	 */
	public function formatCardNumber()
	{
		$cc = $this->getCardNumber();

		return substr($cc, 0, 4).' '.substr($cc, 4, 4).' '.substr($cc, 8, 4).' '.substr($cc, 12, 4);
	}

	/**
	 * Get a masked version of the credit card for privacy.
	 * e.g. **** **** **** 0000
	 * e.g. 3535 0000 0000 ****
	 *
	 * @return 	array 	A list containing 2 different masked versions of card number. 
	 */
	public function getMaskedCardNumber()
	{
		return array(
			'**** **** **** '.substr($this->getCardNumber(), 12, 4),
			substr($this->getCardNumber(), 0, 4).' '.substr($this->getCardNumber(), 4, 4).' '.substr($this->getCardNumber(), 8, 4).' ****'
		);
	}

	/**
	 * Get the JCb alias.
	 *
	 * @return 	string 	The alias of the credit card brand (jcb).
	 */
	public function getBrandAlias()
	{
		return CreditCard::JCB;
	}

	/**
	 * Get the name of the credit card brand.
	 *
	 * @return 	string 	The name of the credit card brand (JCB).
	 */
	public function getBrandName()
	{
		return 'JCB';
	}
	
}
