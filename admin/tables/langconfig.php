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
 * VikRestaurants language configuration table.
 *
 * @since 1.8
 */
class VRETableLangconfig extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_lang_config', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'param';
		$this->_requiredFields[] = 'tag';
	}
}
