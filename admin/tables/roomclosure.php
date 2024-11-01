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
 * VikRestaurants room closure table.
 *
 * @since 1.8
 */
class VRETableRoomclosure extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_room_closure', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_room';
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to
	 * ensure they are safe to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		// check integrity using parent
		if (!parent::check())
		{
			return false;
		}

		// check start date
		if ($this->start_ts == -1)
		{
			// register error message
			$this->setError(JText::sprintf('VRE_INVALID_REQ_FIELD', JText::translate('VRMANAGEROOMCLOSURE2')));

			// invalid start date
			return false;
		}

		// check end date
		if ($this->end_ts == -1)
		{
			// register error message
			$this->setError(JText::sprintf('VRE_INVALID_REQ_FIELD', JText::translate('VRMANAGEROOMCLOSURE3')));

			// invalid end date
			return false;
		}

		// make sure start date is lower than end date
		if ($this->start_ts >= $this->end_ts)
		{
			// register error message
			$this->setError(JText::sprintf('VRE_INVALID_REQ_FIELD', JText::translate('VRMANAGEROOMCLOSURE2')));

			// invalid start date
			return false;
		}

		return true;
	}
}
