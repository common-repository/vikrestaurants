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
 * VikRestaurants menus product table.
 *
 * @since 1.8
 */
class VRETableMenusproduct extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_section_product', 'id', $db);

		// register name as required field
		$this->_requiredFields[] = 'name';
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

		// fetch ordering for new products
		if (empty($src['id']))
		{
			if (empty($src['hidden']))
			{
				$dbo = JFactory::getDbo();

				$src['ordering'] = $this->getNextOrder($dbo->qn('hidden') . ' = 0');
			}
			else
			{
				$src['ordering'] = 0;
			}
		}
		else
		{
			$src['hidden'] = null;
		}

		if (isset($src['tags']) && is_array($src['tags']))
		{
			// join tags
			$src['tags'] = implode(',', $src['tags']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}

	/**
	 * Helper method used to store the user data within the session.
	 *
	 * @param 	mixed 	$data  The array data to store.
	 *
	 * @return 	self    This object to support chaining.
	 * 
	 * @since   1.9
	 */
	public function setUserStateData($data = null)
	{
		if ($data)
		{
			$data = (array) $data;

			if (isset($data['options']))
			{
				foreach ($data['options'] as &$opt)
				{
					if (is_string($opt))
					{
						$opt = json_decode($opt);
					}
				}
			}
		}

		return parent::setUserStateData($data);
	}
}
