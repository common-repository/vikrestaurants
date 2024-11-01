<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DI\Container;

/**
 * Factory class used to access the supported datasheets.
 * 
 * @since 1.9
 */
class DataSheetFactory
{
	/**
	 * The object holding all the supported datasheet types.
	 * 
	 * @var Container
	 */
	private $container;

	/**
	 * Class constructor.
	 * 
	 * @param  Container  $container  The container holding the supported datasheet types.
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
	 * Registers a new datasheet type provider for lazy initialization.
	 * 
	 * @param   string    $type      The datasheet type name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerDataSheetProvider(string $type, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set('datasheet.' . $type, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Returns the datasheet type registered with the specified ID.
	 * 
	 * @param   string     $id       The datasheet type identifier.
	 * @param   array      $options  A configuration array.
	 * 
	 * @return  DataSheet  The datasheet type instance.
	 * 
	 * @throws  \Exception
	 */
	public function getDataSheet(string $id, array $options = [])
	{
		if (!$this->container->has('datasheet.' . $id))
		{
			// type not found
			throw new \DomainException('Datasheet type [' . $id . '] not found', 404);
		}

		/** @var DataSheet */
		$datasheet = $this->container->get('datasheet.' . $id, $options);

		// make sure we have a valid datasheet instance
		if (!$datasheet instanceof DataSheet)
		{
			// invalid type
			throw new \UnexpectedValueException('The datasheet type [' . $id . '] is not a valid instance.', 500);
		}

		return $datasheet;
	}

	/**
	 * Registers a new datasheet export driver provider for lazy initialization.
	 * 
	 * @param   string    $driver    The datasheet export driver name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerExportDriverProvider(string $driver, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set('export.' . $driver, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Returns the datasheet export driver registered with the specified ID.
	 * 
	 * @param   string     $id       The datasheet export driver identifier.
	 * @param   array      $options  A configuration array.
	 * 
	 * @return  DataSheet  The datasheet type instance.
	 * 
	 * @throws  \Exception
	 */
	public function getExportDriver(string $id, array $options = [])
	{
		if (!$this->container->has('export.' . $id))
		{
			// driver not found
			throw new \DomainException('Datasheet export driver [' . $id . '] not found', 404);
		}

		/** @var Export\ExportDriver */
		$exportDriver = $this->container->get('export.' . $id, $options);

		// make sure we have a valid datasheet export driver instance
		if (!$exportDriver instanceof Export\ExportDriver)
		{
			// invalid driver
			throw new \UnexpectedValueException('The datasheet export driver [' . $id . '] is not a valid instance.', 500);
		}

		return $exportDriver;
	}

	/**
	 * Returns a list containing all the supported datasheet export drivers.
	 *
	 * @return 	Export\ExportDriver[]  A list of drivers found.
	 */
	public function getExportDrivers()
	{
		$drivers = [];
		
		foreach ($this->container->keys() as $id)
		{
			if (strpos($id, 'export.') !== 0)
			{
				// we are not observing an export driver
				continue;
			}

			// get rid of the prefix
			$id = substr($id, 7);

			try
			{
				/** @var Export\ExportDriver */
				$drivers[$id] = $this->getExportDriver($id);
			}
			catch (\Exception $e)
			{
				// ignore type
			}
		}

		return $drivers;
	}
}
