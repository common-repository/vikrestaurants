<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CodeHub;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DI\Container;

/**
 * The code hub is used to easily support custom code.
 * 
 * @since 1.9
 */
class CodeHub
{
	/**
	 * The object holding all the supported code handlers.
	 * 
	 * @var Container
	 */
	protected $container;

	/**
	 * The path where the files to load should be stored.
	 * 
	 * @var string
	 */
	protected $path;

	/**
	 * Class constructor.
	 * 
	 * @param  Container  $container
	 * @param  string     $path
	 */
	public function __construct(Container $container, string $path)
	{
		$this->container = $container;
		$this->path      = $path;
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
	 * Registers a new code handler provider for lazy initialization.
	 * 
	 * @param   string    $handler   The code handler name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerCodeProvider(string $handler, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set($handler, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Returns the code handler registered with the specified ID.
	 * 
	 * @param   string       $extension  The code extension (e.g. php).
	 * 
	 * @return  CodeHandler  The code handler instance.
	 * 
	 * @throws  \Exception
	 */
	public function getCodeHandler(string $extension)
	{
		if (!$this->container->has($extension))
		{
			// handler not found
			throw new \DomainException('Code handler [' . $extension . '] not found', 404);
		}

		/** @var CodeHandler */
		$codeHandler = $this->container->get($extension);

		// make sure we have a valid code handler instance
		if (!$codeHandler instanceof CodeHandler)
		{
			// invalid handler
			throw new \UnexpectedValueException('The code handler [' . $extension . '] is not a valid instance.', 500);
		}

		return $codeHandler;
	}

	/**
	 * Returns a list containing all the supported code handlers.
	 *
	 * @return 	CodeHandler[]  A list of code handlers found.
	 */
	public function getCodeHandlers()
	{
		$handlers = [];
		
		foreach ($this->container->keys() as $extension)
		{
			try
			{
				/** @var CodeHandler */
				$handlers[$extension] = $this->getCodeHandler($extension);
			}
			catch (\Exception $e)
			{
				// ignore code handler
			}
		}

		return $handlers;
	}

	/**
	 * Converts an array of blocks into an executable code string
	 * and save it into the appropriate file.
	 * 
	 * @param   CodeBlock[]  $blocks  An array of code blocks.
	 * @param   string[]     $filter  An optional array to include only the specified handlers.
	 *                                Leave empty to take all the handlers.
	 * 
	 * @return  void
	 * 
	 * @throws  \Exception   In case of saving error.
	 */
	public function save(array $blocks, array $filter = [])
	{
		$lookup = [];

		if (!\JFolder::exists($this->path))
		{
			// create the folder in case it does not exist
			if (!\JFolder::create($this->path))
			{
				// cannot create the parent folder
				throw new \RuntimeException('Unable to create folder: ' . $this->path, 500);
			}
		}

		// categorize file blocks by extension
		foreach ($blocks as $block)
		{
			if (!$block instanceof CodeBlock)
			{
				// create code block at runtime starting from the data provided
				$block = new CodeBlock($block);
			}

			// get code extension
			$extension = $block->getExtension();
			
			if (!$extension)
			{
				continue;
			}

			if (!isset($lookup[$extension]))
			{
				$lookup[$extension] = [];
			}

			$lookup[$extension][] = $block;
		}

		// We should iterate all the supported code handlers in order
		// to properly update the files also in case of delete.
		foreach ($this->getCodeHandlers() as $extension => $handler)
		{
			if ($filter && !in_array($extension, $filter))
			{
				// handler not observed
				continue;
			}

			// create file path
			$filePath = \JPath::clean($this->path  . '/custom.' . $extension);

			$blocks = $lookup[$extension] ?? [];

			if ($blocks)
			{
				// create file buffer
				$buffer = $handler->save($blocks);

				// attempt to save file
				if (!\JFile::write($filePath, $buffer))
				{
					throw new \RuntimeException('Cannot save file: ' . $filePath, 500);
				}
			}
			else if (\JFile::exists($filePath))
			{
				// attempt to delete the file
				if (!\JFile::delete($filePath))
				{
					throw new \RuntimeException('Cannot delete file: ' . $filePath, 500);
				}
			}
		}
	}

	/**
	 * Loads all the supported code blocks, sorted by descending creation date.
	 * 
	 * @return  CodeBlock[]  An array of code blocks.
	 */
	public function load()
	{
		$blocks = [];

		// fetch all the supported code handlers
		foreach ($this->getCodeHandlers() as $extension => $handler)
		{
			// create file path
			$filePath = \JPath::clean($this->path  . '/custom.' . $extension);

			// make sure a custom file exists for the provided extension
			if (!\JFile::exists($filePath))
			{
				// extension not used
				continue;
			}

			$buffer = '';

			// open file pointer
			$fp = fopen($filePath, 'r');

			while (!feof($fp))
			{
				// read buffer
				$buffer .= fread($fp, 8192);
			}

			// close file pointer
			fclose($fp);

			// load blocks and merge them to the existing ones
			foreach ($handler->load($buffer) as $block)
			{
				// force file extension for each block
				$blocks[] = $block->setExtension($extension);
			}
		}

		usort($blocks, function($a, $b)
		{
			return strcasecmp($b->getVersion(), $a->getVersion());
		});

		return $blocks;
	}

	/**
	 * Prepares the custom files to be executed/imported.
	 * 
	 * @return  void
	 */
	public function import()
	{
		// fetch all the supported code handlers
		foreach ($this->getCodeHandlers() as $extension => $handler)
		{
			// create file path
			$filePath = \JPath::clean($this->path  . '/custom.' . $extension);

			// make sure a custom file exists for the provided extension
			if (!\JFile::exists($filePath))
			{
				// extension not used
				continue;
			}

			// attempt to import the custom file
			$handler->import($filePath);
		}
	}
}
