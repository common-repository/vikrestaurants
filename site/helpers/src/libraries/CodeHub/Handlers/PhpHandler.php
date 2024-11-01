<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CodeHub\Handlers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CodeHub\CodeHandler;
use E4J\VikRestaurants\CodeHub\CodeHandlerDescriptor;
use E4J\VikRestaurants\CodeHub\IODocTrait;

/**
 * PHP code handler.
 * 
 * @since 1.9
 */
class PhpHandler implements CodeHandler, CodeHandlerDescriptor
{
	use IODocTrait {
		save as savePhp;
		load as loadPhp;
	}

	/**
	 * @inheritDoc
	 */
	public function save(array $blocks)
	{
		foreach ($blocks as $block)
		{
			// remove PHP openings and closures
			$code = $block->getCode();

			$code = preg_replace("/^<\?php\R*/i", '', $code);
			$code = preg_replace("/\?>\R*$/", '', $code);

			$block->setCode($code);
		}

		$buffer = $this->savePhp($blocks);

		// add a security measure at the beginning of the file
		$buffer = "<?php\n\ndefined('_JEXEC') or die;\n\n" . $buffer;

		return $buffer;
	}

	/**
	 * @inheritDoc
	 */
	public function load(string $buffer)
	{
		$blocks = $this->loadPhp($buffer);

		foreach ($blocks as $block)
		{
			// prepend "<?php" to the code for a correct syntax highlighting
			$block->setCode("<?php\n" . $block->getCode());
		}

		return $blocks;
	}

	/**
	 * @inheritDoc
	 * 
	 * It is always assumed that the provided file exists.
	 */
	public function import(string $file)
	{
		try
		{
			// try to load the file
			include_once $file;
		}
		catch (\Throwable $error)
		{
			// do not break the code in case of coding error
			$errorMessage = $error->getMessage() . ' in ' . $error->getFile() . ' on line ' . $error->getLine();
			
			// inform the user about the error message faced
			\JFactory::getApplication()->enqueueMessage($errorMessage, 'error');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return 'PHP';
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fab fa-php';
	}

	/**
	 * @inheritDoc
	 */
	public function getHelp()
	{	
		return \JText::translate('VRE_CODE_BLOCK_INSTR_PHP');
	}
}
