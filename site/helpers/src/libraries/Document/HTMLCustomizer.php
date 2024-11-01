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

/**
 * Helper class used to customize the nodes of an HTML document.
 * 
 * @since 1.9
 */
class HTMLCustomizer
{
	/** @var \DOMDocument */
	protected $dom;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $html  The HTML document to customize.
	 */
	public function __construct(string $html)
	{
		$this->dom = new \IvoPetkov\HTML5DOMDocument;
		$this->dom->loadHTML($html);
	}

	/**
	 * Applies the provided CSS code to the document.
	 * 
	 * @param   string  $css  The CSS code to apply.
	 * 
	 * @return  self    This object to support chaining.
	 */
	public function addCss(string $css)
	{
		// convert CSS code into an associative array
		$rules = (new Parsers\CSSCodeParser)->parse($css);

		// iterate all the selectors
		foreach ($rules as $selector => $properties)
		{
			/** @var \DOMNodeList */
			$nodes = $this->dom->querySelectorAll($selector);

			// iterate all the matching nodes
			foreach ($nodes as $node)
			{
				// get registered styles
				$style = (string) $node->getAttribute('style');

				// append the provided properties
				foreach ($properties as $key => $val)
				{
					$style .= " {$key}: {$val};";
				}

				// save the updated style attribute
				$node->setAttribute('style', trim($style));
			}
		}

		return $this;
	}

	/**
	 * Returns the customized HTML.
	 * 
	 * @return  string
	 */
	public function getHtml()
	{
		// obtain only the HTML contained within the body, because the
		// HTML5DOMDocument class always wraps the provided document
		// within both HTML and BODY tags
		return $this->dom->querySelector('body')->innerHTML ?? '';
	}

	/**
	 * @inheritDoc
	 */
	public function __toString()
	{
		return $this->getHtml();
	}
}
