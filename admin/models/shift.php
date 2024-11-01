<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants working shift model.
 *
 * @since 1.9
 */
class VikRestaurantsModelShift extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return  mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		$shift = parent::getItem($pk, $new);

		if (!$shift)
		{
			return null;
		}

		if (!$shift->id)
		{
			// set default values for the new shift
			$shift->from = 720;
			$shift->to   = 1380;
		}

		if (strlen((string) $shift->days))
		{
			$shift->days = preg_split("/\s*,\s*/", $shift->days);
		}
		else
		{
			$shift->days = range(0, 6);
		}

		return $shift;
	}
}
