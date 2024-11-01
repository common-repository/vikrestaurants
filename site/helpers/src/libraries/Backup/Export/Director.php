<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Export;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Wraps the instructions used to create a backup.
 * 
 * @since 1.9
 */
class Director
{
	/**
	 * An array of export rules.
	 * 
	 * @var Rule[]
	 */
	private $rules = [];

	/**
	 * The instance used to manage the archive.
	 * 
	 * @var Archive
	 */
	private $archive;

	/**
	 * The version to use for the backup manifest.
	 * 
	 * @var string
	 */
	private $version;

	/**
	 * A lookup used to register the compatible version for each CMS.
	 * 
	 * @var array
	 */
	private $platforms = [];

	/**
	 * Class constructor.
	 * 
	 * @param  string  $path  The archive path.
	 */
	public function __construct(string $path)
	{
		// init archive manager
		$this->archive = new Archive($path);
	}

	/**
	 * Returns the archive instance.
	 * 
	 * @return  Archive
	 */
	public function getArchive()
	{
		return $this->archive;
	}

	/**
	 * Sets the version of the manifest.
	 * 
	 * @param   string  $version
	 * 
	 * @return  self    This object to support chaining.
	 */
	public function setVersion(string $version, string $platform = null)
	{
		if (is_null($platform))
		{
			// register generic version
			$this->version = $version;
		}
		else
		{
			// register platform version
			$this->platforms[$platform] = $version;
		}

		return $this;
	}

	/**
	 * Attaches the specified rule as export instruction.
	 * 
	 * @param   Rule  $rule  The rule to attach.
	 * 
	 * @return  self  This object to support chaining.
	 */
	public function attachRule(Rule $rule)
	{
		// register rule only if there is some data to export
		if ($rule->getData())
		{
			$this->rules[] = $rule;
		}

		return $this;
	}

	/**
	 * Returns an array of registered installer rules.
	 * 
	 * @return  Rule[]
	 */
	public function getRules()
	{
		return $this->rules;
	}

	/**
	 * Compresses the archive.
	 * 
	 * @return  string  The archive path.
	 */
	public function compress()
	{
		// create manifest
		$manifest = $this->createManifest();

		if (defined('JSON_PRETTY_PRINT'))
		{
			$flag = JSON_PRETTY_PRINT;
		}
		else
		{
			$flag = 0;
		}

		// try to encode the manifest in JSON format
		$json = json_encode($manifest, $flag);

		if ($json === false)
		{
			// an error has occurred while trying to encode the manifest file in JSON format
			throw new \UnexpectedValueException('Failed to encode the manifest file. Error: ' . json_last_error() . '.', 500);
		}

		// add manifest file into the root of the archive
		$this->archive->addBuffer($json, 'manifest.json');

		// complete the backup process by creating the archive
		return $this->archive->compress();
	}

	/**
	 * Creates the backup manifest.
	 * 
	 * @return  object  The backup manifest to be encoded in JSON format.
	 */
	protected function createManifest()
	{
		// before to compress the archive, we need to create the installation manifest
		$manifest = new \stdClass;
		$manifest->title       = basename($this->archive->getPath());
		$manifest->version     = $this->version ?: VIKRESTAURANTS_SOFTWARE_VERSION;
		$manifest->application = 'Vik Restaurants';
		$manifest->signature   = md5($manifest->application . ' ' . $manifest->version);
		$manifest->langtag     = '*';
		$manifest->dateCreated = \JFactory::getDate()->toSql();
		$manifest->installers  = $this->getRules();

		if ($this->platforms)
		{
			$manifest->platforms = new \stdClass;

			foreach ($this->platforms as $id => $version)
			{
				$manifest->platforms->{$id} = new \stdClass;
				$manifest->platforms->{$id}->version   = $version;
				$manifest->platforms->{$id}->signature = md5($manifest->application . ' ' . $id . ' ' . $version);
			}
		}

		/**
		 * Trigger event to allow third party plugins to manipulate the backup manifest.
		 * Fires just before performing the compression of the archive.
		 * 
		 * @param   object   $manifest  The backup manifest.
		 * @param   Archive  $archive   The instance used to manage the archive.
		 * 
		 * @return  void
		 * 
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onCreateBackupManifestVikRestaurants', [$manifest, $this->archive]);

		return $manifest;
	}
}
