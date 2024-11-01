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
 * Defines an abstract adapter able to extract the archives by using the
 * native tools provided by the CMS.
 * 
 * @since 1.9
 */
abstract class Adapter implements Type
{
	/**
	 * @inheritDoc
	 */
	public function extract(string $source, string $destination)
	{
		if (!class_exists('JArchive'))
		{
			// get temporary path
			$tmp_path = \JFactory::getApplication()->get('tmp_path');

			// instantiate archive class
			$archive = new \Joomla\Archive\Archive(['tmp_path' => $tmp_path]);

			// extract the archive
			return $archive->extract($source, $destination);
		}

		// backward compatibility
		return \JArchive::extract($source, $destination);
	}

	/**
	 * @inheritDoc
	 */
	public function download(string $source)
	{
		if (!\JFile::exists($source))
		{
			// the selected archive doesn't exist
			throw new \Exception(sprintf('Archive [%s] not found', $source), 404);
		}

		$app = \JFactory::getApplication();

		// prepare headers
		$app->setHeader('Content-Disposition', 'attachment; filename=' . basename($source));
		$app->setHeader('Content-Length', filesize($source));

		// send headers
		$app->sendHeaders();

		// use fopen to properly download large files
		$handle = fopen($source, 'rb');

		// read 1MB per cycle
		$chunk_size = 1024 * 1024;

		while (!feof($handle))
		{
			echo fread($handle, $chunk_size);
			ob_flush();
			flush();
		}

		fclose($handle);
	}
}
