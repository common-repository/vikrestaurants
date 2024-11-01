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

use E4J\VikRestaurants\Helpers\DateHelper;

/**
 * VikRestaurants coupon table.
 *
 * @since 1.8
 */
class VRETableCoupon extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_coupons', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'code';
		$this->_requiredFields[] = 'group';
	}

	/**
	 * Method to bind an associative array or object to the Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind($src, $ignore = array())
	{
		$src = (array) $src;

		if (isset($src['code']) && strlen($src['code']) == 0)
		{
			// generate coupon code in case it was specified as an empty string
			$src['code'] = VikRestaurants::generateSerialCode(12, 'coupon');
		}

		if (!empty($src['code']))
		{
			/**
			 * Sanitize coupon code. Only the following characters are accepted:
			 * - letters (a-z and A-Z)
			 * - numbers (0-9)
			 * - hyphens (-) and underscores (_)
			 *
			 * @since 1.9
			 */
			$src['code'] = preg_replace("/[^a-zA-Z0-9\-_]+/", '', $src['code']);
		}

		// only positive amount
		if (isset($src['value']))
		{
			$src['value'] = abs($src['value']);
		}

		// only positive amount
		if (isset($src['mincost']))
		{
			$src['mincost'] = abs($src['mincost']);
		}

		// only positive amount
		if (isset($src['minpeople']))
		{
			$src['minpeople'] = abs($src['minpeople']);
		}

		// make sure the start date is not higher than the end date
		if (isset($src['start_publishing']) && isset($src['end_publishing']) && $src['start_publishing'] > $src['end_publishing'] && !DateHelper::isNull($src['end_publishing']))
		{
			// swap the specified dates
			$tmp                     = $src['start_publishing'];
			$src['start_publishing'] = $src['end_publishing'];
			$src['end_publishing']   = $tmp;
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
