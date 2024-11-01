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

VRELoader::import('library.menu.custom');

/**
 * Extends the CustomShape class to display a line separator.
 *
 * @since 1.7
 * @since 1.8 Renamed from LeftBoardMenuLine to LeftboardCustomShapeLine.
 */
class LeftboardCustomShapeLine extends CustomShape
{
	/**
	 * @override
	 * Builds and returns the html structure of the custom menu item.
	 * This method must be implemented to define a specific graphic of the custom item.
	 *
	 * @return 	string 	The html of the custom item.
	 */
	public function buildHtml()
	{
		$layout = new JLayoutFile('menu.leftboard.custom.line');

		return $layout->render();
	}
}
