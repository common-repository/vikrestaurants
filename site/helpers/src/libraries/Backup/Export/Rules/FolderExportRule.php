<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Export\Rules;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Backup\Export\Archive;
use E4J\VikRestaurants\Backup\Export\Rule;

/**
 * FOLDER Backup export rule.
 * 
 * @since 1.9
 */
class FolderExportRule extends Rule
{
	/**
	 * The target used by the system to restore the files in their position.
	 * It is possible to pass a PHP constant so that the backup receiver will
	 * be able to fetch the path at runtime.
	 * 
	 * It is possible to receive an array in order to support both constants
	 * and relative paths. In example, the first element of the array could be
	 * a constant and the second one the remaining path of the destination.
	 * 
	 * @var string|array
	 */
	protected $destination;

	/**
	 * The base (relative) path of the files. This is useful in case of
	 * recursive elements, because we need to strip the relative path
	 * used within the archive.
	 * 
	 * @var string
	 */
	protected $relativePath;

	/**
	 * An array containing the relative path of all the copied files.
	 * 
	 * @var string[]
	 */
	protected $files;

	/**
	 * Flag used to check whether during the import the path of the files to copy
	 * should be preserved or whether only the name should be used.
	 * 
	 * @var bool
	 */
	protected $recursive;

	/**
	 * Class constructor.
	 * 
	 * @param  Archive  $archive  The handler used to construct the archive.
	 * @param  array    $data     An associative array holding the instructions
	 *                            for the folder to copy.
	 */
	public function __construct(Archive $archive, array $data)
	{
		// make sure the source path is specified
		if (empty($data['source']))
		{
			throw new \Exception('Missing folder source', 500);
		}

		// make sure the destrination path is specified
		if (empty($data['destination']))
		{
			throw new \Exception('Missing folder destination', 500);
		}
		else
		{
			// remove trailing directory separator
			$data['destination'] = preg_replace("/[\/\\\\]+$/", '', $data['destination']);
		}

		// register relative path
		$this->relativePath = $data['destination'];

		if (empty($data['target']))
		{
			// final target equals to source path
			$data['target'] = $data['source'];
		}

		// check if we should preserve the path defined by the rule during the import
		$this->recursive = isset($data['recursive']) ? (bool) $data['recursive'] : false;

		// set destination path to use while restoring the backup
		$this->destination = $data['target'];

		if (\JFile::exists($data['source']))
		{
			// file given, use it directly
			$files = $data['source'];

			// go up by one level to properly copy the file
			$data['source'] = dirname($data['source']);
		}
		else
		{
			// ignore the following files while copying
			$exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'];

			// scan the given folder
			$files = \JFolder::files($data['source'], '.', $this->recursive, $fullPath = true, $exclude);

			if (!is_array($files))
			{
				// false received, the source folder does not exist
				$files = [];
			}
		}

		// iterate all files stored within the given folder
		foreach ((array) $files as $file)
		{
			// get rid of base path from file
			$rel = str_replace($data['source'], '', $file);
			$rel = preg_replace("/^[\/\\\\]+/", '', $rel);

			// build relative destination path
			$dest = $data['destination'] . DIRECTORY_SEPARATOR . $rel;

			// append file to archive
			if (!$archive->addFile($file, $dest))
			{
				throw new \Exception(sprintf('Cannot copy file [%s] into [%s]', $file, $dest), 500);
			}

			// register copied file
			$this->files[] = $dest;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getRule()
	{
		return 'folder';
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		// make sure there's at least a file to copy
		if ($this->files)
		{
			return [
				'destination'  => $this->destination,
				'relativePath' => $this->relativePath,
				'recursive'    => $this->recursive,
				'files'        => $this->files,
			];
		}

		// do not import empty files
		return null;
	}
}
