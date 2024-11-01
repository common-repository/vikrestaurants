<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CodeHub;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * This interface defines the tasks that a specific handler (e.g. php) should be
 * able to implement.
 * 
 * @since 1.9
 */
interface CodeHandler
{
	/**
	 * Converts an array of blocks into an executable code string.
	 * 
	 * @param   CodeBlock[]  $blocks  An array of code blocks.
	 * 
	 * @return  string       The resulting code snippet.
	 */
	public function save(array $blocks);

	/**
	 * Loads the code blocks from the provided buffer.
	 * 
	 * @param   string       $buffer  The code snippet to parse.
	 * 
	 * @return  CodeBlock[]  An array of code blocks.
	 */
	public function load(string $buffer);

	/**
	 * Prepares the file to be executed/imported.
	 * 
	 * @param   string  $file  The path of the file to import.
	 * 
	 * @return  void
	 */
	public function import(string $file);
}
