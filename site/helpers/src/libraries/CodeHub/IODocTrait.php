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

/**
 * Default implementation for all the code handlers that support doc blocks.
 * 
 * @since 1.9
 */
trait IODocTrait
{
	/**
	 * @see CodeHandler::save()
	 */
	public function save(array $blocks)
	{
		$buffer = [];

		// scan all the provided blocks
		foreach ($blocks as $block)
		{
			// make sure the code exists
			if (!$block->getCode())
			{
				continue;
			}

			// convert code block into an executable snippet
			$buffer[] = "/**\n * ### " . $block->getTitle() . " ###\n *\n"
				. " * " . $block->getDescription() . "\n * \n"
				. " * @author " . $block->getAuthor() . "\n"
				. " * @version " . $block->getVersion() . "\n"
				. " * {@internal " . $block->getID() . "}\n"
				. " */\n\n" . $block->getCode(); 
		}

		return implode("\n\n", $buffer);
	}

	/**
	 * @see CodeHandler::load()
	 */
	public function load(string $buffer)
	{
		$blocks = [];
		$self   = $this;

		// extract every snippet declaration from the code
		$buffer = preg_replace_callback("/\/\*\*\s*\*\s*###\s*(.*?)\s*###(.*?)@author\s*(.*?)@version\s*(.*?)(?:{@internal\s*([a-z0-9]+)}\s*)?\*\//si", function($match) use (&$blocks, $self)
		{
			// create a new block with the meta data found
			$blocks[] = new CodeBlock([
				'title'       => $self->clean($match[1]),
		        'description' => $self->clean($match[2]),
		        'author'      => $self->clean($match[3]),
		        'version'     => $self->clean($match[4]),
		        'id'          => $self->clean($match[5] ?? ''),
			]);

			// replace each code block with a delimiter
		    return "\n{vikrestaurants_code_snippet_delimiter}\n";
		}, $buffer);

		// split any delimiter in order to find the matching snippets
		$chunks = preg_split("/{vikrestaurants_code_snippet_delimiter}/", $buffer);

		// Assign each snippet to the related block.
		// Starts from 1 because the first line is always the heading of the file.
		for ($i = 1; $i < count($chunks); $i++)
		{
		    $blocks[$i - 1]->setCode(trim($chunks[$i]));
		}

		return $blocks;
	}

	/**
	 * Cleans the provided string by removing unexpected characters.
	 * 
	 * @param   string  $str
	 * 
	 * @return  string
	 */
	protected function clean(string $str)
	{
		// remove every "*" at the beginning of a new line
		return trim(preg_replace("/\R\s*\*/", '', $str));
	}
}
