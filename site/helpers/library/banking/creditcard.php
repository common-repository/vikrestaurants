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
 * This class is used to handle and dispatch the proper credit card brand.
 * It is helpful to use this class for seamless payment gateways.
 *
 * @abstract	It is needed to recognize the brand before to instantiate this class.
 *
 * Usage:
 * $card_number = '4242424242424242'; // VISA
 * $cc = CreditCard::getInstance($card_number);
 * echo $cc->formatCardNumber();
 *
 * @see 	VRELoader  	Used to load the files containing the classes of the brands.
 *
 * @since  	1.7
 */
abstract class CreditCard
{
	/**
	 * The number of the credit card.
	 *
	 * @var string
	 */
	private $card_number = '';

	/**
	 * The CVC, CVV or CV2 of the credit card.
	 *
	 * @var string
	 */
	private $cvc = '0';

	/**
	 * The expiring month of the credit card (1-12).
	 *
	 * @var integer
	 */
	private $exp_month = 0;

	/**
	 * The expiring year of the credit card.
	 *
	 * @var integer
	 */
	private $exp_year = 0;

	/**
	 * The cardholder name.
	 *
	 * @var string
	 */
	private $card_holder = '';

	/**
	 * Class constructor.
	 *
	 * @param 	string 		$card_number 	The card number.
	 * @param 	string  	$cvc 			The CVC, CVV or CV2.
	 * @param 	integer 	$month 			The expiring month.
	 * @param 	integer 	$year 			The expiring year.
	 * @param 	string 		$card_holder 	The cardholder name.
	 *
	 * @uses 	setCardNumber() 	Card number setter.
	 * @uses 	setCvc() 			Card CVC setter.
	 * @uses 	setExpiryDate() 	Card expiring month and year setter.
	 * @uses 	setCardholderName() Cardholder name setter.
	 */
	public function __construct($card_number, $cvc = '0', $month = 0, $year = 0, $card_holder = '')
	{
		$this->setCardNumber($card_number)
			->setCvc($cvc)
			->setExpiryDate($month, $year)
			->setCardholderName($card_holder);

	}

	/**
	 * Set the credit card number.
	 * Any character that it is not a digit will be ignored.
	 * @usedby 	CreditCard::__construct()
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	CreditCard	This object to support chaining.
	 */
	public function setCardNumber($card_number)
	{
		$this->card_number = preg_replace('/\D/', '', $card_number);

		return $this;
	}

	/**
	 * Get the credit card number.
	 *
	 * @return 	string 	The card number.
	 */
	public function getCardNumber()
	{
		return $this->card_number;
	}

	/**
	 * Set the credit card CVC, CVV or CV2.
	 * Any character that it is not a digit will be ignored.
	 * @usedby 	CreditCard::__construct()
	 *
	 * @param 	string 	$cvc 	The CVC, CVV or CV2.
	 *
	 * @return 	CreditCard	This object to support chaining.
	 */
	public function setCvc($cvc)
	{
		$this->cvc = preg_replace('/\D/', '', $cvc);

		return $this;
	}

	/**
	 * Get the credit card CVC, CVV or CV2.
	 *
	 * @return 	string 	The card CVC, CVV or CV2.
	 */
	public function getCvc()
	{
		return $this->cvc;
	}

	/**
	 * Set the credit card expiring month.
	 * @usedby 	CreditCard::setExpiryDate()
	 *
	 * @param 	integer 	$month 	The expiring month (1-12).
	 *
	 * @return 	CreditCard	This object to support chaining.
	 */
	public function setExpiryMonth($month)
	{
		$month = intval($month);

		if ($month >= 1 && $month <= 12) {
			$this->exp_month = $month;
		}

		return $this;
	}

	/**
	 * Get the expiring month.
	 *
	 * @return 	integer 	The expiring month.
	 */
	public function getExpiryMonth()
	{
		return $this->exp_month;
	}

	/**
	 * Set the credit card expiring year.
	 * @usedby 	CreditCard::setExpiryDate()
	 *
	 * @param 	integer 	$year 	The expiring year with 2 or 4 digits (e.g. 16 or 2016).
	 *
	 * @return 	CreditCard	This object to support chaining.
	 */
	public function setExpiryYear($year)
	{
		$year = intval($year);

		if ($year < 1000) {
			$now = getdate();
			$year = intval(substr($now['year'], 0, 2).$year);
		}

		$this->exp_year = $year;

		return $this;
	}

	/**
	 * Get the expiring year.
	 *
	 * @return 	integer 	The expiring year.
	 */
	public function getExpiryYear()
	{
		return $this->exp_year;
	}

	/**
	 * Set the credit card expiring date: month and year.
	 * @usedby 	CreditCard::__construct()
	 *
	 * @param 	integer 	$month 	The expiring month (1-12).
	 * @param 	integer 	$year 	The expiring year with 2 or 4 digits (e.g. 16 or 2016).
	 *
	 * @return 	CreditCard	This object to support chaining.
	 *
	 * @uses 	setExpiryMonth() 	Expiring month setter.
	 * @uses 	setExpiryYear() 	Expiring year setter.
	 */
	public function setExpiryDate($month, $year)
	{
		return $this->setExpiryMonth($month)->setExpiryYear($year);
	}

	/**
	 * Get the expiring date with the given format.
	 *
	 * @param 	string 	$format 	The date format.
	 *
	 * @return 	string 	The expiring date.
	 */
	public function getExpiryDate($format = 'm/y')
	{
		$date = getdate(mktime(0, 0, 0, $this->exp_month, 1, $this->exp_year));

		$last_mday = 31;
		if (in_array($date['mon'], array(4, 6, 9, 11))) {
			$last_mday = 30;
		} else if ($date['mon'] == 2) {
			$last_mday = 28;
		}
		
		return date($format, mktime(23, 59, 59, $date['mon'], $last_mday, $date['year']));
	}

	/**
	 * Set the cardholder name.
	 * @usedby 	CreditCard::__construct()
	 *
	 * @param 	string 	$card_holder 	The cardholder name.
	 *
	 * @return 	CreditCard	This object to support chaining.
	 */
	public function setCardholderName($card_holder)
	{
		$this->card_holder = trim($card_holder);

		return $this;
	}

	/**
	 * Get the cardholder name.
	 *
	 * @return 	string 	The cardholder name.
	 */
	public function getCardholderName()
	{
		return $this->card_holder;
	}

	/**
	 * Check if the credit card number is valid.
	 * The card number validation depends on the brand of the credit card.
	 * @abstract This method is handled from the child brand class.
	 * @usedby 	CreditCard::isChargeable()
	 *
	 * @return 	boolean 	True if the card number is valid.
	 */
	abstract public function isCardNumberValid();

	/**
	 * Check if the CVC is valid.
	 * The CVC is valid if it is a number in the range [100 - 9999]
	 * @usedby 	CrediCard::isChargeable()
	 *
	 * @return 	boolean 	True if the CVC is valid, otherwise false.
	 */
	public function isCardCvcValid()
	{
		return (($len = strlen($this->cvc)) >= 3 && $len <= 4);
	}

	/**
	 * Check if the credit card is expired.
	 * @usedby 	CrediCard::isChargeable()
	 *
	 * @return 	boolean 	True if the expiring date is in the past, otherwise false.
	 */
	public function isExpired()
	{
		$now = getdate();

		return ($this->exp_month < 1 || $this->exp_month > 12 || $this->exp_year < $now['year'] || ($this->exp_year == $now['year'] && $this->exp_month < $now['mon']));
	}

	/**
	 * Check if the cardholder name is valid.
	 * The cardholder name is valid only if it has more than 2 characters 
	 * and at least one of them is a white space (e.g John Smith).
	 * It is needed to have at least 3 chars and 1 space to receive a First name
	 * and a last name.
	 *
	 * It is not possible to have a white space at the beginning or at the end
	 * because the cardholder name is trimmed from the setter. 
	 * @usedby 	CrediCard::isChargeable()
	 *
	 * @return 	boolean 	True if the cardholder name is valid, otherwise false.
	 */
	public function isCardholderValid()
	{
		return (strlen($this->card_holder) > 2 && strpos($this->card_holder, ' ') !== false);
	}

	/**
	 * Check approximately if the credit card can be charged.
	 * A credit card can be charged only if all the conditions below are satisfied:
	 * - credit card number is valid
	 * - cvc is valid
	 * - expiring date is not in the past
	 * - cardholder name is valid
	 *
	 * @return 	boolean 	True if the credit card is chargable, otherwise false.
	 *
	 * @uses 	isCardNumberValid() 	Validate card number.
	 * @uses 	isCardCvcValid()	 	Validate CVC.
	 * @uses 	isExpired() 			Validate expiring date.
	 * @uses 	isCardholderValid() 	Validate cardholder name.
	 */
	public function isChargeable()
	{
		return ($this->isCardNumberValid() && $this->isCardCvcValid() && !$this->isExpired() && $this->isCardholderValid());
	}

	/**
	 * Get the credit card number digits count.
	 * @abstract This method is handled from the child brand class.
	 *
	 * @return 	integer 	The digits count of the credit card number.
	 */
	abstract public function getCardNumberDigits();

	/**
	 * Format the credit card number to be more human-readable.
	 * @abstract This method is handled from the child brand class.
	 *
	 * @return 	string 	The formatted card number. 
	 */
	abstract public function formatCardNumber();

	/**
	 * Get a masked version of the credit card for privacy.
	 * @abstract This method is handled from the child brand class.
	 *
	 * @return 	array 	A list containing 2 different masked versions of card number. 
	 */
	abstract public function getMaskedCardNumber();

	/**
	 * Get the alias of the credit card brand.
	 * The alias should be equal to the filename of the brand class.
	 * @abstract This method is handled from the child brand class.
	 *
	 * @return 	string 	The alias of the credit card brand.
	 */
	abstract public function getBrandAlias();

	/**
	 * Get the name of the credit card brand.
	 * @abstract This method is handled from the child brand class.
	 *
	 * @return 	string 	The name of the credit card brand.
	 */
	abstract public function getBrandName();

	/**
	 * Instantiate a new credit card brand class depending on the specified card number.
	 * Return NULL in case the method is not able to recognize the brand.
	 *
	 * @param 	string 		$card_number 	The card number.
	 * @param 	string  	$cvc 			The CVC, CVV or CV2.
	 * @param 	integer 	$month 			The expiring month.
	 * @param 	integer 	$year 			The expiring year.
	 * @param 	string 		$card_holder 	The cardholder name.
	 *
	 * @return 	CreditCard 	The proper CC brand handler, or NULL.
	 *
	 * @uses 	load() 					Import the brand handler file.
	 * @uses 	isVisa() 				True if the card number matches a Visa.
	 * @uses 	isMsterCard() 			True if the card number matches a MasterCard.
	 * @uses 	isAmericanExpress() 	True if the card number matches an American Express.
	 * @uses 	isDinersClub()		 	True if the card number matches a Diners Club.
	 * @uses 	isDiscover()		 	True if the card number matches a Discover.
	 * @uses 	isJcb() 				True if the card number matches a JCB.
	 */
	public static function getBrand($card_number, $cvc = '0', $month = 0, $year = 0, $card_holder = '')
	{
		if (CreditCard::isVisa($card_number)) {

			if (CreditCard::load(CreditCard::VISA)) {
				return new CCVisa($card_number, $cvc, $month, $year, $card_holder);
			}

		} else if (CreditCard::isMasterCard($card_number)) {

			if (CreditCard::load(CreditCard::MASTER_CARD)) {
				return new CCMasterCard($card_number, $cvc, $month, $year, $card_holder);
			}

		} else if (CreditCard::isAmericanExpress($card_number)) {

			if (CreditCard::load(CreditCard::AMERICAN_EXPRESS)) {
				return new CCAmericanExpress($card_number, $cvc, $month, $year, $card_holder);
			}

		} else if (CreditCard::isDinersClub($card_number)) {

			if (CreditCard::load(CreditCard::DINERS_CLUB)) {
				return new CCDinersClub($card_number, $cvc, $month, $year, $card_holder);
			}

		} else if (CreditCard::isDiscover($card_number)) {

			if (CreditCard::load(CreditCard::DISCOVER)) {
				return new CCDiscover($card_number, $cvc, $month, $year, $card_holder);
			}

		} else if (CreditCard::isJcb($card_number)) {

			if (CreditCard::load(CreditCard::JCB)) {
				return new CCJcb($card_number, $cvc, $month, $year, $card_holder);
			}

		}

		return null;
	}

	/**
	 * Check if the card number matches a Visa.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * How to identify VISA:
	 * A credit card is a VISA when the card number starts with 4.
	 *
	 * e.g. 4242 0000 000 0000
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	boolean 	True if the card number is a Visa, otherwise false.
	 *
	 * @uses 	matchBrandRanges() 	Verify matches of this brand.
	 */
	public static function isVisa($card_number)
	{
		$card_number = preg_replace('/\D/', '', $card_number);

		// starts with 4
		$ranges = array(
			array(4)
		);

		return self::matchBrandRanges($card_number, $ranges);
	}

	/**
	 * Check if the card number matches a MasterCard.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * How to identify MasterCard:
	 * A credit card is a MasterCard when the first 2 digits are in the range [51 - 55]
	 * or the first four digits are in the range [2221 - 2720].
	 *
	 * e.g. 5353 0000 0000 0000
	 * e.g. 2323 0000 0000 0000
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	boolean 	True if the card number is a MasterCard, otherwise false.
	 *
	 * @uses 	matchBrandRanges() 	Verify matches of this brand.
	 */
	public static function isMasterCard($card_number)
	{
		$card_number = preg_replace('/\D/', '', $card_number);

		// from 51 to 55 (included) or 2221 to 2720 (included)
		$ranges = array(
			array(51, 55),
			array(2221, 2720)
		);

		return self::matchBrandRanges($card_number, $ranges);
	}

	/**
	 * Check if the card number matches an American Express.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * How to identify American Express:
	 * A credit card is an American Express when the first 2 digits are 34 or 37.
	 *
	 * e.g. 3434 000000 00000
	 * e.g. 3737 000000 00000
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	boolean 	True if the card number is an American Express, otherwise false.
	 *
	 * @uses 	matchBrandRanges() 	Verify matches of this brand.
	 */
	public static function isAmericanExpress($card_number)
	{
		$card_number = preg_replace('/\D/', '', $card_number);

		// 34 or 37
		$ranges = array(
			array(34),
			array(37)
		);

		return self::matchBrandRanges($card_number, $ranges);
	}

	/**
	 * Check if the card number matches a Diners Club.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * How to identify Diners Club:
	 * A credit card is a Diners Club when the first 3 digits are in the range [300 - 305] or 309 
	 * or the first 2 digits are 36 or in the range [38 - 39].
	 *
	 * e.g. 3636 0000 0000 0000
	 * e.g. 3838 0000 0000 0000
	 * e.g. 3010 0000 0000 0000
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	boolean 	True if the card number is a Diners Club, otherwise false.
	 *
	 * @uses 	matchBrandRanges() 	Verify matches of this brand.
	 */
	public static function isDinersClub($card_number)
	{
		$card_number = preg_replace('/\D/', '', $card_number);

		// from 300 to 305 (included) or 309 or 36 or from 38 to 39 (included)
		$ranges = array(
			array(300, 305),
			array(309),
			array(36),
			array(38, 39)
		);

		// 3636 0000 0000 00
		// 3838 0000 0000 00
		// 3010 0000 0000 00
		return self::matchBrandRanges($card_number, $ranges);
	}

	/**
	 * Check if the card number matches a Discover.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * How to identify Discover:
	 * A credit card is a Discover when the first 4 digits are 6011 
	 * or the first 2 digits are 65 
	 * or the first 6 digits are in the range [622126 - 622925]
	 * or the first 3 digits are in the range [644 - 649].
	 *
	 * e.g. 6565 0000 0000 0000
	 * e.g. 6011 0000 0000 0000
	 * e.g. 6221 2900 0000 0000
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	boolean 	True if the card number is a Discover, otherwise false.
	 *
	 * @uses 	matchBrandRanges() 	Verify matches of this brand.
	 */
	public static function isDiscover($card_number)
	{
		$card_number = preg_replace('/\D/', '', $card_number);

		// 6011 or 65 or from 622126 to 622925 (included) or from 644 to 649 (included)
		$ranges = array(
			array(6011),
			array(65),
			array(622126, 622925),
			array(644, 649)
		);

		// 6565 0000 0000 0000
		// 6011 0000 0000 0000
		// 6221 2900 0000 0000
		return self::matchBrandRanges($card_number, $ranges);
	}

	/**
	 * Check if the card number matches a JCB.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * How to identify JCB:
	 * A credit card is a JCB when the first 4 digits are in the range [3528 - 3589].
	 *
	 * e.g. 3535 0000 0000 0000
	 *
	 * @param 	string 	$card_number 	The card number.
	 *
	 * @return 	boolean 	True if the card number is a JCB, otherwise false.
	 *
	 * @uses 	matchBrandRanges() 	Verify matches of this brand.
	 */
	public static function isJcb($card_number)
	{
		$card_number = preg_replace('/\D/', '', $card_number);

		// from 3528 to 3589 (included)
		$ranges = array(
			array(3528, 3589)
		);

		// 3535 0000 0000 0000
		return self::matchBrandRanges($card_number, $ranges);
	}

	/**
	 * Return a list containing the alias of all the accepted brands.
	 * 
	 * @return 	array 	A list with all the supported brand's aliases.
	 */
	public static function getAllBrands()
	{
		return array(
			CreditCard::VISA,
			CreditCard::MASTER_CARD,
			CreditCard::AMERICAN_EXPRESS,
			CreditCard::DINERS_CLUB,
			CreditCard::DISCOVER,
			CreditCard::JCB
		);
	}

	/**
	 * Load the file which declares the class handler of the specified brand.
	 * @usedby 	CreditCard::getBrand()
	 *
	 * @param 	string 	$brand 	The alias of the brand.
	 *
	 * @return 	boolean 	True if the brand file is loaded correctly, otherwise false.
	 *
	 * @uses 	VRELoader::import() 	to import brand files. 
	 */
	protected static function load($brand)
	{
		return VRELoader::import('library.banking.brands.'.$brand);
	}

	/**
	 * Match the card number with the provided brand card ranges.
	 * @usedby 	CreditCard::isVisa()
	 * @usedby 	CreditCard::isMasterCard()
	 * @usedby 	CreditCard::isAmericanExpress()
	 * @usedby 	CreditCard::isDinersClub()
	 * @usedby 	CreditCard::isDiscover()
	 * @usedby 	CreditCard::isJcb()
	 *
	 * @param 	string 	$card_number	The credit card number.
	 * @param 	array 	$ranges 		The list containing all the accepted brand ranges.
	 *
	 * @return 	boolean 	True if the credit card matches at least one range, otherwise false.
	 */
	protected static function matchBrandRanges($card_number, $ranges = array())
	{
		foreach ($ranges as $r) {

			if (count($r) == 1) {

				if (intval(substr($card_number, 0, strlen($r[0]))) == $r[0]) {
					return true;
				} 

			} else if (count($r) == 2) {

				$val = substr($card_number, 0, strlen($r[0]));

				if ($r[0] <= $val && $val <= $r[1]) {
					return true;
				}

			}

		}

		return false;
	}

	/**
	 * The VISA alias brand identifier.
	 *
	 * @var string 
	 */
	const VISA 	= 'visa';

	/**
	 * The MASTER CARD alias brand identifier.
	 *
	 * @var string 
	 */
	const MASTER_CARD = 'mastercard';

	/**
	 * The AMERICAN EXPRESS alias brand identifier.
	 *
	 * @var string 
	 */
	const AMERICAN_EXPRESS = 'amex';

	/**
	 * The DINERS CLUB alias brand identifier.
	 *
	 * @var string 
	 */
	const DINERS_CLUB = 'diners';

	/**
	 * The DISCOVER alias brand identifier.
	 *
	 * @var string 
	 */
	const DISCOVER = 'discover';

	/**
	 * The JCB alias brand identifier.
	 *
	 * @var string 
	 */
	const JCB = 'jcb';
}
