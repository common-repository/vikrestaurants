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
 * VikRestaurants special day-menu relational table.
 *
 * @since 1.9
 */
class VRETableSpecialdaymenu extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_sd_menus', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_spday';
		$this->_requiredFields[] = 'id_menu';

		// set relation columns
		$this->_tbl_assoc_pk = 'id_spday';
		$this->_tbl_assoc_fk = 'id_menu';
	}
}
