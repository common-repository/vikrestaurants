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
 * VikRestaurants reservation menu relation table.
 *
 * @since 1.8
 */
class VRETableResmenu extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_res_menus_assoc', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_reservation';
		$this->_requiredFields[] = 'id_menu';

		// set relation columns
		$this->_tbl_assoc_pk = 'id_reservation';
		$this->_tbl_assoc_fk = 'id_menu';
	}
}
