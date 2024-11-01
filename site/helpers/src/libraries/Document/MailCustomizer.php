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
 * Helper class used to customize the nodes of an HTML document suitable for an e-mail.
 * 
 * The mail clients may force a default color for the contents inside a table.
 * Since all our e-mail templates are based on a tables-structure, we need to force
 * the color specified from the body (or the background) to all the tables, just to
 * make sure that the e-mail will be sent with correct colors.
 * 
 * @since 1.9
 */
class MailCustomizer extends HTMLCustomizer
{
	/**
	 * @inheritDoc
	 */
	public function addCss(string $css)
	{
		parent::addCss($css);

		// recover the "body" used by VikRestaurants for the e-mail
		$color = $this->getTextColor('.vreBody');

		if (!$color)
		{
			// the body doesn't specify a color, let's try with the background
			$color = $this->getTextColor('.vreBackground');
		}

		if (!$color)
		{
			// no default text provided, we don't need to proceed
			return $this;
		}

		/** @var \DOMNodeList */
		$tables = $this->dom->querySelectorAll('table');

		// iterate all the existing tables
		foreach ($tables as $table)
		{
			// check whether the table already applies a text color
			$tableColor = $this->getTextColor($table);

			if ($tableColor)
			{
				// do not overwrite the text color of the table
				continue;
			}

			// get registered styles
			$style = (string) $table->getAttribute('style');

			// concat default color to the style attribute
			$style .= ' color: ' . $color . ';';

			// save the updated style attribute
			$table->setAttribute('style', trim($style));
		}

		return $this;
	}

	/**
	 * Returns the text color of the provided node.
	 * 
	 * @param   DOMElement|string  $node  Either a DOM node or a selector.
	 * 
	 * @return  string|null  The color if specified, null otherwise.
	 */
	protected function getTextColor($node)
	{
		if (is_string($node))
		{
			// selector provided, fetch the matching node
			$node = $this->dom->querySelector($node);
		}

		if (!$node)
		{
			// node not found
			return null;
		}

		// get node style
		$style = (string) $node->getAttribute('style');

		// check whether the style specifies a color for the text
		if (!preg_match_all("/(?:^|[;\s])color:\s*(.*?);/", $style, $matches))
		{
			// no color provided
			return null;
		}

		// at least a text color found, return the last available one
		return end($matches[1]);
	}
}
