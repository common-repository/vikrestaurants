<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  wizard
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Implement the wizard step used to download and
 * install a sample data package.
 *
 * @since 1.1
 */
class VREWizardStepSampleData extends VREWizardStep
{
	/**
	 * Returns the step title.
	 * Used as a very-short description.
	 *
	 * @return 	string  The step title.
	 */
	public function getTitle()
	{
		return __('Sample Data');
	}

	/**
	 * Returns the step description.
	 *
	 * @return 	string  The step description.
	 */
	public function getDescription()
	{
		return __('<p>It is possible to download here a set of sample data, which will auto-populate VikRestaurants to be immediately ready to use.</p>', 'vikrestaurants');
	}

	/**
	 * Returns an optional step icon.
	 *
	 * @return 	string  The step icon.
	 */
	public function getIcon()
	{
		return '<i class="fas fa-download"></i>';
	}

	/**
	 * Return the group to which the step belongs.
	 *
	 * @return 	string  The group name.
	 */
	public function getGroup()
	{
		// belongs to GLOBAL group
		return JText::translate('VRMENUTITLEHEADER4');
	}

	/**
	 * Returns the HTML to display description and actions
	 * needed to complete the step.
	 *
	 * @return 	string  The HTML of the step.
	 */
	public function display()
	{
		// always try to search for a layout related to this step
		return JLayoutHelper::render('html.wizard.sampledata', array('step' => $this));
	}

	/**
	 * Checks whether the specified step can be skipped.
	 * By default, all the steps are mandatory.
	 * 
	 * @return 	boolean  True if skippable, false otherwise.
	 */
	public function canIgnore()
	{
		return true;
	}

	/**
	 * Implements the step execution.
	 *
	 * @param 	JRegistry  $data  The request data.
	 *
	 * @return 	boolean
	 */
	protected function doExecute($data)
	{
		// get selected sample data
		$id = $data->get('sampledata');

		JLoader::import('adapter.filesystem.folder');

		// get temporary dir
		$tmp = get_temp_dir();

		// clean temporary path
		$tmp = rtrim(JPath::clean($tmp), DIRECTORY_SEPARATOR);

		// make sure the folder exists
		if (!is_dir($tmp))
		{
			throw new Exception(sprintf('Temporary folder [%s] does not exist', $tmp), 404);
		}

		// make sure the temporary folder is writable
		if (!wp_is_writable($tmp))
		{
			throw new Exception(sprintf('Temporary folder [%s] is not writable', $tmp), 403);
		}

		// download end-point
		$url = 'https://vikwp.com/api/?task=sampledata.download';

		// init HTTP transport
		$http = new JHttp();

		// build smaple data file name
		$packname = 'sampledata-' . uniqid();

		// build request headers
		$headers = array(
			// turn on stream to push body within a file
			'stream'   => true,
			// define the filepath in which the data will be pushed
			'filename' => $tmp . DIRECTORY_SEPARATOR . $packname . '.zip',
			// make sure the request is non blocking
			'blocking' => true,
			// force timeout to 60 seconds
			'timeout'  => 60,
		);

		// build post data
		$data = array(
			'id' => $id,
		);

		// make connection with VikWP server
		$response = $http->post($url, $data, $headers);

		if ($response->code != 200)
		{
			// raise error returned by VikWP
			throw new Exception($response->body, $response->code);
		}

		// make sure the file has been saved
		if (!JFile::exists($headers['filename']))
		{
			throw new Exception('ZIP package could not be saved on disk', 404);
		}

		// create destination folder for extracted elements
		$dest = $tmp . DIRECTORY_SEPARATOR . $packname;

		// make sure the destination folder doesn't exist
		if (JFolder::exists($dest))
		{
			// remote it before proceeding with the extraction
			JFolder::delete($dest);
		}

		// import archive class handler
		JLoader::import('adapter.filesystem.archive');

		// the package was downloaded successfully, let's extract it (onto TMP folder)
		$extracted = JArchive::extract($headers['filename'], $dest);

		// we no longer need the archive
		JFile::delete($headers['filename']);

		if (!$extracted)
		{
			// an error occurred while extracting the files
			throw new Exception(sprintf('Cannot extract files to [%s]', $tmp), 500);
		}

		// make sure the folder is intact
		if (!JFolder::exists($dest))
		{
			// impossible to access the extracted elements
			throw new Exception(sprintf('Cannot access extracted elements from [%s] folder', $dest), 404);
		}

		$error = null;

		try
		{
			// run sample data installation
			$this->installSampleData($dest);
		}
		catch (Exception $e)
		{
			// safely catch error to finalize process
			$error = $e;
		}

		// process complete, clean up the temporary folder before exiting
		JFolder::delete($dest);

		// in case of error, propagate it after deleting the extracted folder
		if ($error)
		{
			throw $error;
		}

		return true;
	}

	/**
	 * Returns a list of supported sample data.
	 *
	 * @return 	array  A list of sample data.
	 */
	public function getSampleData()
	{
		// build transient key
		$transient = 'vikrestaurants_sampledata_' . md5(VIKRESTAURANTS_SOFTWARE_VERSION);

		// get cached sample data list
		$data = get_transient($transient);

		if ($data)
		{
			// return cached transient
			return json_decode($data);
		}

		// instantiate HTTP transport
		$http = new JHttp();

		// build end-point URI
		$uri = 'https://vikwp.com/api/?task=sampledata.list';

		// build post data
		$post = array(
			'sku'     => 'vre',
			'version' => VIKRESTAURANTS_SOFTWARE_VERSION,
		);

		// load sample data from server
		$response = $http->post($uri, $post);

		if ($response->code == 200)
		{
			// decode response
			$data = json_decode($response->body);

			// cache sample data list for an hour
			set_transient($transient, json_encode($data), HOUR_IN_SECONDS);
		}
		else
		{
			$data = array();
		}

		return $data;
	}

	/**
	 * Interpretes the manifest contained within the folder
	 * to install the sample data.
	 *
	 * @param 	string 	$folder  The sample data folder.
	 *
	 * @return 	void
	 *
	 * @throws 	Exception
	 */
	protected function installSampleData($folder)
	{
		// load manifest.json file
		if (!is_file($folder . DIRECTORY_SEPARATOR . 'manifest.json'))
		{
			// missing JSON manifest
			throw new Exception('Manifest file not found', 404);
		}

		// open manifest file in read mode
		$manifestFile = fopen($folder . DIRECTORY_SEPARATOR . 'manifest.json', 'r');

		$manifest = '';

		// load manifest content
		while (!feof($manifestFile))
		{
			$manifest .= fread($manifestFile, 8192);
		}

		// close file
		fclose($manifestFile);

		// decode JSON
		$manifest = json_decode($manifest);

		if (!$manifest)
		{
			// unable to JSON decode manifest
			throw new Exception('Manifest file contains an invalid JSON', 500);
		}

		$dbo = JFactory::getDbo();

		// look for uninstall queries
		if (isset($manifest->uninstall))
		{
			// iterate queries to clean any existing records
			foreach ($manifest->uninstall as $q)
			{
				try
				{
					// check if we are uninstalling the shortcodes
					if (preg_match("/vikrestaurants_wpshortcodes/i", $q))
					{
						// uninstall the existing shortcodes
						$this->uninstallShortcodes();
					}

					// launch query
					$dbo->setQuery($q);
					$dbo->execute();
				}
				catch (Exception $e)
				{
					// malformed query, suppress error and go ahead

					if (VIKRESTAURANTS_DEBUG)
					{
						// propagate error in case of debug
						throw $e;
					}
				}
			}
		}

		// look for installers
		if (isset($manifest->installers))
		{
			// iterate installers
			foreach ($manifest->installers as $install)
			{
				try
				{
					// switch case role to invoke the proper installation method
					switch ($install->role)
					{
						case 'sql':
						case 'insert':
							$this->installSqlRole($install->data);
							break;

						case 'media':
						case 'folder':
							$this->installFilesRole($install->data->destination, $install->data->files, $folder);
							break;
					}
				}
				catch (Exception $e)
				{
					// malformed role, suppress error and go ahead

					if (VIKRESTAURANTS_DEBUG)
					{
						// propagate error in case of debug
						throw $e;
					}
				}
			}

			// install shortcodes after completing the set up
			$this->installShortcodes();
		}
	}

	/**
	 * Executes the specified queries.
	 *
	 * @param 	mixed 	$queries  Either a query string or an array.
	 *
	 * @return 	void
	 */
	protected function installSqlRole($queries)
	{
		if (!is_array($queries))
		{
			$queries = array($queries);
		}

		$dbo = JFactory::getDbo();

		// iterate queries one by one
		foreach ($queries as $q)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}
	}

	/**
	 * Moves the files into the related folders.
	 *
	 * @param 	string 	$dest   The destination folder.
	 * @param 	mixed 	$files  Either a file or an array.
	 * @param 	string 	$dir    The current directory.
	 *
	 * @return 	void
	 */
	protected function installFilesRole($dest, $files, $dir)
	{
		if (!is_array($files))
		{
			$files = array($files);
		}

		foreach ($files as $file)
		{
			// fetch file path
			$path = JPath::clean($dir . DIRECTORY_SEPARATOR . $file);

			if (preg_match("/\/media\/?$/i", $dest))
			{
				// use media folder instead
				$dest = VREMEDIA;
			}
			else if (preg_match("/\/media@small\/?$/i", $dest))
			{
				// use media@small folder instead
				$dest = VREMEDIA_SMALL;
			}
			else if (preg_match("/\/customers\/?$/i", $dest))
			{
				// use customers avatar folder instead
				$dest = VRECUSTOMERS_AVATAR;
			}
			else if (preg_match("/\/pdf\/archive\/?$/i", $dest))
			{
				// use invoices folder instead
				$dest = VREINVOICE;
			}
			else
			{
				// fallback to plugin base folder
				$dest = VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . $dest;
			}

			// fetch destination file path
			$destFile = JPath::clean($dest . DIRECTORY_SEPARATOR . basename($file));

			// copy file in its destination
			$res = JFile::copy($path, $destFile);
		}
	}

	/**
	 * Uninstalls all the pages that have been assigned
	 * to the existing shortcodes.
	 *
	 * @return 	void
	 */
	protected function uninstallShortcodes()
	{
		// get shortcode admin model
		$model = JModel::getInstance('vikrestaurants', 'shortcodes', 'admin');

		// get all existing shortcodes
		$shortcodes = $model->all(array('createdon', 'post_id'));

		// iterate all shortcodes found
		foreach ($shortcodes as $shortcode)
		{
			// make sure the shortcode has been assigned to a post
			if ($shortcode->post_id)
			{
				// get post details
				$post = get_post((int) $shortcode->post_id);

				// convert shortcode creation date
				$shortcode->createdon = new JDate($shortcode->createdon);
				// convert post creation date
				$post->post_date_gmt = new JDate($post->post_date_gmt);

				// compare ephocs and make sure the post was not created before the shortcode
				if ((int) $shortcode->createdon->format('U') <= (int) $post->post_date_gmt->format('U'))
				{
					// permanently delete post
					wp_delete_post($post->ID, $force_delete = true);
				}
			}
		}
	}

	/**
	 * Assigns all the newly created shortcodes to new pages.
	 *
	 * @return 	void
	 */
	protected function installShortcodes()
	{
		// get shortcode admin model
		$model = JModel::getInstance('vikrestaurants', 'shortcodes', 'admin');

		// get all existing shortcodes
		$shortcodes = $model->all();

		// iterate all shortcodes found
		foreach ($shortcodes as $shortcode)
		{
			if ($shortcode->post_id == 0)
			{
				// Add a new page (we allow a WP_ERROR to be thrown in case of failure).
				// This should automatically trigger the hook that we use to link the shortcode 
				// to the new page/post ID, and so there's no need to update the item.
				wp_insert_post(array(
					'post_title'   => (!empty($shortcode->name) ? $shortcode->name : JText::translate($shortcode->title)),
					'post_content' => $shortcode->shortcode,
					'post_status'  => 'publish',
					'post_type'    => 'page',
				), true);
			}
		}
	}
}
