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
 * VikRestaurants take-away deal table.
 *
 * @since 1.8
 */
class VRETableTkdeal extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_deal', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'name';
		$this->_requiredFields[] = 'type';
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

		// fetch ordering for new separators
		if ($src['id'] == 0)
		{
			$src['ordering'] = $this->getNextOrder();
		}

		// force a single usage for deals based on total cost
		if (isset($src['type']) && in_array($src['type'], ['freeitemtotal', 'coupon', 'discounttotal']))
		{
			$src['max_quantity'] = 1;
		}

		// JSON encode deal parameters
		if (isset($src['params']) && !is_string($src['params']))
		{
			$src['params'] = json_encode($src['params']);
		}

		// JSON encode shifts in case of array
		if (isset($src['shifts']) && !is_string($src['shifts']))
		{
			$src['shifts'] = json_encode($src['shifts']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
