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
 * VikRestaurants take-away availability override table.
 *
 * @since 1.8.3
 */
class VRETableTkavail extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_avail_override', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind($src, $ignore = array())
	{
		$src = (array) $src;

		if (empty($src['ts']))
		{
			// generate timestamp from given date and time
			list($hour, $min) = explode(':', $src['hourmin']);
			$src['ts'] = VikRestaurants::createTimestamp($src['date'], $hour, $min);
		}

		$dbo = JFactory::getDbo();

		// try to retrieve the override record if already exists
		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn($this->getTableName()));

		if (empty($src['id']))
		{
			// search by timestamp
			$q->where($dbo->qn('ts') . ' = ' . (int) $src['ts']);
		}
		else
		{
			// search by ID
			$q->where($dbo->qn('id') . ' = ' . (int) $src['id']);
		}

		$dbo->setQuery($q);
		$override = $dbo->loadObject();

		if ($override)
		{
			// Prepare for update.
			// Add/subtract the available units to the current value.
			$src['units'] += $override->units;
			// set ID to force the update
			$src['id'] = $override->id;
		}
		else
		{
			// set empty ID for insert
			$src['id'] = 0;
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
