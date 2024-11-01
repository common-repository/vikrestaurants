<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DI\Container;

/**
 * Backups director class.
 * 
 * @since 1.9
 */
class Manager
{
	/**
	 * Indicates the minimum required version while creating a
	 * new backup on Joomla. This value should be changed everytime
	 * something in the database structure is altered.
	 * 
	 * @var string
	 */
	const MINIMUM_REQUIRED_VERSION_JOOMLA = '1.9';

	/**
	 * Indicates the minimum required version while creating a
	 * new backup on WordPress. This value should be changed everytime
	 * something in the database structure is altered.
	 * 
	 * @var string
	 */
	const MINIMUM_REQUIRED_VERSION_WORDPRESS = '1.3';

	/**
	 * The object holding all the supported export types.
	 * 
	 * @var Container
	 */
	private $container;

	/**
	 * Class constructor.
	 * 
	 * @param  Container  $container  The container holding the supported export types.
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Returns the container holding all the registered instances.
	 * @protected it is not possible to directly access the container
	 * from the outside.
	 * 
	 * @return  Container
	 */
	final protected function getContainer()
	{
		return $this->container;
	}

	/**
	 * Registers a new export type provider for lazy initialization.
	 * 
	 * @param   string    $type      The export type name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerExportTypeProvider(string $type, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set($type, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Creates a new backup.
	 * 
	 * @param   string  $type     The type of backup to execute.
	 * @param   array   $options  A configuration array.
	 *                            - folder     string  The path in which the archive should be saved.
	 *                                                 if not specified, the system temporary path will be used.
	 *                            - filename   string  An optional filename to use for the archive. If not specified
	 *                                                 the filename will be equals to the current time.
	 *                            - prefix     string  An optional prefix to prepend to the filename.
	 * 
	 * @return  string  The path of the backup (a ZIP archive).
	 * 
	 * @throws  \Exception
	 */
	public function create(string $type, array $options = [])
	{
		// ignore the maximum execution time
		set_time_limit(0);

		if (empty($options['folder']))
		{
			// before starting the export, make sure the temporary folder is supported
			$options['folder'] = \JFactory::getApplication()->get('tmp_path');

			if (!$options['folder'] || !\JFolder::exists($options['folder']))
			{
				throw new \Exception('The temporary folder seems to be not set', 500);
			}

			// remove trailing directory separator
			$options['folder'] = preg_replace("/[\/\\\\]$/", '', $options['folder']);
		}

		if (empty($options['filename']))
		{
			// use the current date and time as file name
			$options['filename'] = 'backup_' . $type . '_' . \JFactory::getDate()->format('Y-m-d H-i-s');
		}

		if (!empty($options['prefix']))
		{
			// include a prefix before the file name
			$options['filename'] = $options['prefix'] . $options['filename'];
		}

		// build archive path
		$path = $options['folder'] . DIRECTORY_SEPARATOR . $options['filename'];
		
		// create backup export director
		$director = new Export\Director($path);

		// set the manifest version equals to the minimum required one
		$director->setVersion(static::MINIMUM_REQUIRED_VERSION_JOOMLA, 'joomla');
		$director->setVersion(static::MINIMUM_REQUIRED_VERSION_WORDPRESS, 'wordpress');

		/** @var Export\Type */
		$handler = $this->getExportType($type);

		$error = null;

		try
		{
			// build the installers manifest
			$handler->build($director);

			/**
			 * Trigger event to allow third party plugins to extend the backup feature.
			 * This hook is useful to include third-party tables and files into the
			 * backup archive.
			 * 
			 * It is possible to attach a database table into the backup by using:
			 * $director->attachRule('sqlfile', '#__extensions');
			 * 
			 * @param   string 	         $type      The type of backup to execute.
			 * @param   Export\Director  $director  The instance used to manage the backup.
			 * @param   array            $options   An array of options.
			 * 
			 * @return  void
			 * 
			 * @since   1.9
			 */
			\VREFactory::getPlatform()->getDispatcher()->trigger('onBuildBackupVikRestaurants', [$type, $director, $options]);

			// compress the archive and obtain the full path
			$archivePath = $director->compress();
		}
		catch (\Exception $e)
		{
			// catch any error
			$error = $e;
		}

		// always delete archive folder
		\JFolder::delete($path);

		if ($error)
		{
			// in case of error, propagate it only after cleaning the dump
			throw $error;
		}

		return $archivePath;
	}

	/**
	 * Restores an existing backup.
	 * 
	 * @param   string  $path  The path of the backup to restore.
	 * 
	 * @return  void
	 * 
	 * @throws  \Exception
	 */
	public function restore(string $path)
	{
		// ignore the maximum execution time
		set_time_limit(0);

		// make sure the archive exists
		if (!\JFile::exists($path))
		{
			// unable to find the specified archive
			throw new \Exception(sprintf('Backup [%s] not found', $path), 404);
		}

		// create a unique extraction folder
		$extractdir = dirname($path) . DIRECTORY_SEPARATOR . uniqid();

		// extract the given backup
		$status = \E4J\VikRestaurants\Archive\Factory::extract($path, $extractdir);

		if (!$status)
		{
			// cannot extract the archive
			throw new \Exception(sprintf('Unable to extract [%s] into [%s]', $path, $extractdir), 500);
		}

		// create backup import director
		$director = new Import\Director($this->container, $extractdir);

		// set the manifest version equals to the minimum required one, according to the CMS in use
		if (\VersionListener::isJoomla())
		{
			$director->setVersion(static::MINIMUM_REQUIRED_VERSION_JOOMLA);
		}
		else
		{
			$director->setVersion(static::MINIMUM_REQUIRED_VERSION_WORDPRESS);
		}

		$error = null;

		try
		{
			// process the backup
			$director->process();
		}
		catch (\Exception $e)
		{
			$error = $e;
		}

		// always delete extracted folder
		\JFolder::delete($extractdir);

		if ($error)
		{
			// in case of error, propagate it only after cleaning the dump
			throw $error;
		}
	}

	/**
	 * Returns the export type registered with the specified ID.
	 * 
	 * @param   string       $id  The export type identifier.
	 * 
	 * @return  Export\Type  The export type instance.
	 * 
	 * @throws  \Exception
	 */
	public function getExportType(string $id)
	{
		if (!$this->container->has('export.' . $id))
		{
			// type not found
			throw new \DomainException('Export type [' . $id . '] not found', 404);
		}

		/** @var Export\Type */
		$exportType = $this->container->get('export.' . $id);

		// make sure we have a valid export type instance
		if (!$exportType instanceof Export\Type)
		{
			// invalid type
			throw new \UnexpectedValueException('The export type [' . $id . '] is not a valid instance.', 500);
		}

		return $exportType;
	}

	/**
	 * Returns a list containing all the supported export types.
	 *
	 * @return 	Export\Type[]  A list of types found.
	 */
	public function getExportTypes()
	{
		$types = [];
		
		foreach ($this->container->keys() as $id)
		{
			if (strpos($id, 'export.') !== 0)
			{
				// we are not observing an export type
				continue;
			}

			// get rid of the prefix
			$id = substr($id, 7);

			try
			{
				/** @var Export\Types */
				$types[$id] = $this->getExportType($id);
			}
			catch (\Exception $e)
			{
				// ignore type
			}
		}

		// sort plugins by name
		uasort($types, function($a, $b)
		{
			return strcmp($a->getName(), $b->getName());
		});

		return $types;
	}
}
