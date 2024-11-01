<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Document;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Interface used to parse strings of codes and return a more suitable
 * version for the program.
 * 
 * @since 1.9
 */
interface CodeParser
{
	/**
	 * Parses the provided code.
	 * 
	 * @param   string  $code  The code to parse.
	 * 
	 * @return  mixed   A suitable parsed version.
	 */
	public function parse(string $code);
}
