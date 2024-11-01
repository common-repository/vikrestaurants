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
 * Helper function used to create a summary from a list of items.
 *
 * @since 1.9
 */
trait CountableItemsSummaryTrait
{
	/**
	 * Creates a summary for the provided list.
	 * 
	 * @param   array  $items    The items list
	 * @param   array  $options  A configuration array:
	 *                           - display  int  The number of items to display.
	 *                                           The exceeding ones will be displayed as "and other n items".
	 * 
	 * @return  string
	 */
	protected function createSummary(array $items, array $options = [])
	{
		$options = new \JRegistry($options);

		// fetch the displayable items only
		$displayable = array_splice($items, 0, $options->get('display', 1));

		if (!$displayable)
		{
			// nothing to display, use a different text
			return \JText::plural('VRE_DEF_N_SELECTED', count($items));
		}

		// include the displayable items within the summary
		$summary = implode(', ', $displayable);

		if ($items)
		{
			// display the remaining items as "and other N items"
			$summary .= ' ' . \JText::plural('VRWIZARDOTHER_N_ITEMS', count($items));
		}

		return $summary;
	}
}
