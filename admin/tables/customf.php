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
 * VikRestaurants custom fields table.
 *
 * @since 1.8
 */
class VRETableCustomf extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_custfields', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'group';
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

		// fetch ordering for new custom fields
		if ($src['id'] == 0)
		{
			$src['ordering'] = $this->getNextOrder('`group` = ' . (int) $src['group']);
		}

		if (isset($src['choose']) && !is_string($src['choose']))
		{
			// stringify configuration
			$src['choose'] = json_encode($src['choose']);
		}

		if (!empty($src['type']) && $src['type'] == 'separator')
		{
			// do not use rule for separator
			$src['rule'] = 0;
			// separators are always optional
			$src['required'] = 0;

			if (!empty($src['choose']))
			{
				// make class suffix safe
				$src['choose'] = preg_replace("/[^a-zA-Z0-9_\-\s]+/", '', $src['choose']);
			}
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
