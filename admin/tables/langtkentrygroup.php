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
 * VikRestaurants language take-away menu entry topping group table.
 *
 * @since 1.8
 */
class VRETableLangtkentrygroup extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_lang_takeaway_menus_entry_topping_group', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_group';
		$this->_requiredFields[] = 'id_parent';
		$this->_requiredFields[] = 'tag';
	}
}
