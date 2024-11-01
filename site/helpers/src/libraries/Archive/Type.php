<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Archive;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Declares the methods that a specific archives director should provide.
 * 
 * @since 1.9
 */
interface Type
{
	/**
	 * Extracts the file from a package.
	 * 
	 * @param   string  $source       The path of the package.
	 * @param   string  $destination  The folder in which the files should be extracted.
	 * 
	 * @return  bool    True on success, false otherwise.
	 */
	public function extract(string $source, string $destination);

	/**
	 * Compresses the specified folder into an archive.
	 * 
	 * @param   string  $source       The path of the folder to compress.
	 * @param   string  $destination  The path of the resulting archive.
	 * 
	 * @return  bool    True on success, false otherwise.
	 */
	public function compress(string $source, string $destination);

	/**
	 * Downloads the specified archive.
	 * 
	 * @param   string  $source  The path of the folder to compress.
	 * 
	 * @return  void
	 */
	public function download(string $source);
}
