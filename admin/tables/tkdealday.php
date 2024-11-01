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
 * VikRestaurants take-away deal day relation table.
 *
 * @since 1.9
 */
class VRETableTkdealday extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_deal_day_assoc', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_deal';
		$this->_requiredFields[] = 'id_weekday';

		// set relation columns
		$this->_tbl_assoc_pk = 'id_deal';
		$this->_tbl_assoc_fk = 'id_weekday';
	}
}
