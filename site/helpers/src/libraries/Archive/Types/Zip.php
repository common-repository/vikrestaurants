<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Archive\Types;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Archive\Adapter;

/**
 * Implementor used to compress and extract ZIP archives.
 * 
 * @since 1.9
 */
class Zip extends Adapter
{
	/**
	 * @inheritDoc
	 */
	public function compress($source, $destination)
	{
		if (!class_exists('ZipArchive'))
		{
			// ZipArchive class is mandatory to create a package
			throw new \Exception('The ZipArchive class is not installed on this server.', 500);
		}

		// in case the destination path is already occupied, delete it first
		if (\JFile::exists($destination))
		{
			\JFile::delete($destination);
		}

		// scan all the files contained in the specified folder
		$files = \JFolder::files($source, $filter = '.', $recursive = true, $full = true);

		// sanitize source path
		$source = rtrim($source, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		// init package
		$zip = new \ZipArchive;
		$zip->open($destination, \ZipArchive::CREATE);

		foreach ($files as $file)
		{
			// get rid of base path from file
			$file = str_replace($source, '', $file);

			// extract directories from file path
			$chunks = preg_split("/[\/\\\\]+/", $file);
			// and ignore the file name
			array_pop($chunks);

			$folder = '';

			foreach ($chunks as $dir)
			{
				$folder .= $dir . '/';

				// check whether the folder exists
				if (!$zip->locateName($folder))
				{
					// nope, create it
					$zip->addEmptyDir($folder);
				}
			}

			// attach file to zip
			$zip->addFile($source . $file, $file);
		}

		// complete compression
		return $zip->close();
	}

	/**
	 * @inheritDoc
	 */
	public function download(string $source)
	{
		// set header content type for a correct download
		\JFactory::getApplication()->setHeader('Content-Type', 'application/zip');

		// invoke parent to start downloading the archive
		parent::download($source);
	}
}
