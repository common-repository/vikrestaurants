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
 * VikRestaurants take-away delivery area table.
 *
 * @since 1.8
 */
class VRETableTkarea extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_delivery_area', 'id', $db);

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

		// fetch ordering for new delivery area
		if ($src['id'] == 0)
		{
			$src['ordering'] = $this->getNextOrder();
		}

		if (isset($src['min_cost']))
		{
			$src['min_cost'] = abs((float) $src['min_cost']);
		}

		if (isset($src['attributes']) && is_array($src['attributes']))
		{
			$src['attributes'] = json_encode($src['attributes']);
		}

		if (isset($src['content']) && !is_string($src['content']))
		{
			$src['content'] = json_encode($src['content']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
