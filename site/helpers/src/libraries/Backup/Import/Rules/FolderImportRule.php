<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Import\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Backup\Import\Rule;

/**
 * Backup Folder import rule.
 * 
 * @since 1.9
 */
class FolderImportRule extends Rule
{
	/**
	 * @inheritDoc
	 * 
	 * @param  object  $data  An object holding all the following information:
	 *                        - destination    the base destination pathe where the files should be imported to;
	 *                        - files          a list of files to import;
	 *                        - recursive      whether the copy should be recursive or not;
	 *                        - relativePath   The relative path where the files are currently stored (archive);
	 *                        - full           Whether the full file path should be used instead.
	 */
	public function execute($data)
	{
		if (empty($data->destination))
		{
			// destination path is missing
			throw new \Exception('Invalid Folder import rule, missing destination path', 404);
		}

		if (!isset($data->files))
		{
			// source files are missing
			throw new \Exception('Invalid Folder import rule, missing source files', 404);
		}

		// support the array notation for the destination
		if (is_array($data->destination))
		{
			// removed first element from destination
			$fixed = $data->destination;
			$data->destination = array_shift($fixed);
		}
		else
		{
			$fixed = null;
		}

		// check if we have a constant
		if (defined($data->destination))
		{
			// use the path defined by the plugin
			$destination = constant($data->destination);
		}
		else
		{
			// use a path relative to the system (according to the platform in use)
			$destination = \JPath::clean(\VREFactory::getPlatform()->getUri()->getAbsolutePath() . '/' . $data->destination);
		}

		if ($fixed)
		{
			// re-append fixed path to destination
			$destination = \JPath::clean($destination . '/' . implode('/', $fixed));
		}

		// iterate all source files
		foreach ((array) $data->files as $file)
		{
			// build source path
			$src = \JPath::clean($this->path . '/' . $file);

			// make sure the source file exists
			if (!\JFile::exists($src))
			{
				// source file is missing
				throw new \Exception(sprintf('File to copy [%s] not found', $src), 404);
			}

			if (!empty($data->full))
			{
				// use the full relative path
				$rel = $file;
			}
			else if (!empty($data->recursive) && !empty($data->relativePath))
			{
				// get rid of the relative path
				$rel = substr($file, strlen($data->relativePath . '/'));
			}
			else
			{
				// use only the file name
				$rel = basename($file);
			}

			// build full destination path
			$fd = \JPath::clean($destination . '/' . $rel);

			// get path of parent folder
			$parentDir = dirname($fd);

			// make sure the destination folder exists, otherwise create it first
			if (!\JFolder::exists($parentDir))
			{
				\JFolder::create($parentDir);
			}

			// try to copy the file
			if (!\JFile::copy($src, $fd))
			{
				// unable to perform file copy
				throw new \Exception(sprintf('Unable to copy [%s] into [%s]', $src, $fd), 500);
			}
		}
	}
}
