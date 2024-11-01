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
 * VikRestaurants invoice table.
 *
 * @since 1.8
 */
class VRETableInvoice extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_invoice', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_order';
		$this->_requiredFields[] = 'inv_number';
		$this->_requiredFields[] = 'file';
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

		// invoice creation
		if (empty($src['id']))
		{
			if (empty($src['createdon']))
			{
				$src['createdon'] = VikRestaurants::now();
			}
		}

		if (empty($src['file']) && !empty($src['path']))
		{
			// save only the file name
			$src['file'] = basename($src['path']);
		}

		if (isset($src['inv_date']) && !is_numeric($src['inv_date']))
		{
			// convert date into a timestamp
			$src['inv_date'] = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($src['inv_date']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
