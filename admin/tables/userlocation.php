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
 * VikRestaurants customer delivery location table.
 *
 * @since 1.8
 */
class VRETableUserlocation extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_user_delivery', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_user';
		$this->_requiredFields[] = 'address';
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

		// fetch ordering for new locations
		if (empty($src['id']) && empty($src['ordering']))
		{
			$dbo = JFactory::getDbo();

			$src['ordering'] = $this->getNextOrder($dbo->qn('id_user') . ' = ' . (int) $src['id_user']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
