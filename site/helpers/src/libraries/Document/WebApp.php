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

use E4J\VikRestaurants\Document\WebApp\Manifest;

/**
 * This helper is used to support the web application feature provided by
 * the OS and the browser in use.
 * 
 * @since 1.9
 */
class WebApp
{
	/** @var Manifest */
	protected $manifest;

	/**
	 * Class constructor.
	 * 
	 * @param  Manifest  $manifest  The manifest interface.
	 */
	public function __construct(Manifest $manifest)
	{
		$this->manifest = $manifest;
	}

	/**
	 * Loads the manifest file.
	 * 
	 * @return  void
	 */
	public function load()
	{
		// fetch the path of the manifest
		$path = $this->manifest->getPath();

		// make sure the file exists
		if (!\JFile::exists($path))
		{
			// attempt to save the manifest file
			if (!$this->save())
			{
				// unable to save the file, abort
				return;
			}
		}

		// create manifest URI
		$uri = \VREFactory::getPlatform()->getUri()->getUrlFromPath($path);
		// append last-modify timestamp to refresh the cache whenever the file gets updated
		$uri .= (strpos($uri, '?') === false ? '?' : '&') . filemtime($path);

		// register link within the head of the document
		\JFactory::getDocument()->addHeadLink($uri, 'manifest');
	}

	/**
	 * Saves the manifest file.
	 * 
	 * @return  bool  True in case of success, false otherwise.
	 */
	public function save()
	{
		// get manifest body
		$data = $this->manifest->buildJson();

		try
		{
			/**
			 * Trigger event to allow third-party plugins to manipulate the manifest object
			 * that will be used to support the web application.
			 * 
			 * It is possible to throw an exception in case the manifest shouldn't be used.
			 * 
			 * @param   array|object  &$data  The manifest to encode in JSON format.
			 * 
			 * @return  void
			 * 
			 * @since   1.9
			 */
			\VREFactory::getPlatform()->getDispatcher()->trigger('onBuildWebAppManifest', [&$data]);
		}
		catch (\Exception $e)
		{
			// unset manifest in case of error
			$data = null;

			// attempt to delete the existing manifest file
			\JFile::delete($this->manifest->getPath());
		}

		if (!is_array($data) && !is_object($data))
		{
			// do not build manifest
			return false;
		}

		// save the manifest file
		return (bool) \JFile::write(
			// get manifest path
			$this->manifest->getPath(),
			// encode the manifest in JSON format
			json_encode($data, \JSON_PRETTY_PRINT)
		);
	}
}
