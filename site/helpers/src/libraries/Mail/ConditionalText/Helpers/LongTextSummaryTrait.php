<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper function used to create a summary from a given text.
 *
 * @since 1.9
 */
trait LongTextSummaryTrait
{
	/**
	 * Creates a summary for the provided text.
	 * 
	 * @param   string  $text     The text to summarize.
	 * @param   array   $options  An array of options:
	 *                            - plain    bool   Whether the text should not contain HTML tags.
	 *                            - length   int    The maximum number of characters to display.
	 *                                              Use 0 to display the whole text.
	 *                            - default  mixed  The default string to use in case the provided
	 *                                              text has no contents. Use false to avoid using
	 *                                              a default placeholder.
	 * 
	 * @return  string
	 */
	protected function createSummary(string $text, array $options = [])
	{
		$options = new \JRegistry($options);

		if ($options->get('plain', true))
		{
			// get rid of the HTML
			$text = strip_tags($text);
		}

		if ($length = $options->get('length', 0))
		{
			// do not take more than 128 characters
			if (mb_strlen($text, 'UTF-8') > $length)
			{
				// take only a part of the text
				$text = rtrim(mb_substr($text, 0, $length - 4, 'UTF-8'), '.,?!;:#\'"([{ ') . '...';
			}
		}

		if (!$text)
		{
			// get default value
			$text = (string) $options->get('default', '<em>' . \JText::translate('VRE_UISVG_NONE') . '</em>');
		}

		return $text;
	}
}
