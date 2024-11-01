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
 * Javascript code handler.
 * 
 * @since 1.9
 */
class JsHandler implements CodeHandler, CodeHandlerDescriptor
{
	use IODocTrait { 
		save as saveJs;
		load as loadJs; 
	}

	/**
	 * @inheritDoc
	 */
	public function save(array $blocks)
	{
		$buffer = $this->saveJs($blocks);

		// indent each line by four spaces
		$buffer = "    " . preg_replace("/\R/", "\n    ", $buffer);

		// wrap code into a safe block
		$buffer = "(function($, w) {\n    \"use strict\"\n\n$buffer\n})(jQuery, window);";

		return $buffer;
	}

	/**
	 * @inheritDoc
	 */
	public function load(string $buffer)
	{
		// remove code wrapper
		$buffer = preg_replace("/^\(function\(\\$, w\)\s*{\R*\s*\"use strict\"\R*/", '', $buffer);
		$buffer = preg_replace("/}\)\(jQuery,\s*window\);?$/", '', $buffer);

		// remove the indentation
		$buffer = trim(preg_replace("/\R    /", "\n", $buffer));

		return $this->loadJs($buffer);
	}

	/**
	 * @inheritDoc
	 * 
	 * It is always assumed that the provided file exists.
	 */
	public function import(string $file)
	{
		// do not include the script if we are in WordPress and we are making an AJAX request
		if (\VersionListener::isWordpress() && wp_doing_ajax())
		{
			return;
		}
		
		// convert path into an URI
		$uri = \VREFactory::getPlatform()->getUri()->getUrlFromPath($file);

		// import script with versioning equals to the last modify date
		\JHtml::fetch('script', $uri, [
			'version' => filemtime($file),
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return 'JavaScript';
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fab fa-js-square';
	}

	/**
	 * @inheritDoc
	 */
	public function getHelp()
	{
		return \JText::translate('VRE_CODE_BLOCK_INSTR_JS');
	}
}
