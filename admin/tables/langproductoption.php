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
 * VikRestaurants language menu product option table.
 *
 * @since 1.8
 */
class VRETableLangproductoption extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_lang_section_product_option', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_option';
		$this->_requiredFields[] = 'id_parent';
		$this->_requiredFields[] = 'tag';
	}
}
