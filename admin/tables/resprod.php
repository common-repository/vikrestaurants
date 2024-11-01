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
 * VikRestaurants restaurant reservation product (bill cart items) table.
 *
 * @since 1.8
 */
class VRETableResprod extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_res_prod_assoc', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_product';
		$this->_requiredFields[] = 'id_reservation';
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

		if (isset($src['quantity']))
		{
			// make sure the quantity is 1 or higher
			$src['quantity'] = max(1, $src['quantity']);
		}

		if (!empty($src['price']))
		{
			// make sure the price is not lower than 0
			$src['price'] = max(0, $src['price']);
		}

		// JSON encode the tax breakdown, when specified as array
		if (isset($src['tax_breakdown']) && !is_string($src['tax_breakdown']))
		{
			$src['tax_breakdown'] = json_encode($src['tax_breakdown']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
