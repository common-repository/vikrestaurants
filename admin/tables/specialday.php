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
 * VikRestaurants special day table.
 *
 * @since 1.8
 */
class VRETableSpecialday extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_specialdays', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'group';
		$this->_requiredFields[] = 'name';
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

		if (isset($src['days_filter']) && is_array($src['days_filter']))
		{
			$src['days_filter'] = implode(',', $src['days_filter']);
		}

		if (!empty($src['working_shifts']))
		{	
			if (is_array($src['working_shifts']))
			{
				$shifts = $custom = [];

				foreach ($src['working_shifts'] as $sh)
				{
					// check if we are dealing with an ID (of the shift)
					if (is_numeric($sh))
					{
						$shifts[] = $sh;
					}
					// otherwise we probably have a custom JSON
					else
					{
						$custom[] = is_string($sh) ? json_decode($sh) : (object) $sh;
					}
				}

				$src['working_shifts'] = implode(',', $shifts);
				$src['custom_shifts']  = json_encode($custom);
			}
		}
		else
		{
			// clear both working shifts and custom shifts
			$src['working_shifts'] = '';

			if (!isset($src['custom_shifts']))
			{
				$src['custom_shifts'] = '';
			}
		}

		// convert start date to UNIX timestamp
		if (isset($src['start_ts']) && !is_numeric($src['start_ts']))
		{
			if (strlen((string) $src['start_ts']))
			{
				$src['start_ts'] = VikRestaurants::createTimestamp($src['start_ts'], 0, 0);
			}
			else
			{
				$src['start_ts'] = -1;
			}
		}

		// convert end date to UNIX timestamp
		if (isset($src['end_ts']) && !is_numeric($src['end_ts']))
		{
			if (strlen((string) $src['end_ts']))
			{
				$src['end_ts'] = VikRestaurants::createTimestamp($src['end_ts'], 23, 59);
			}
			else
			{
				$src['end_ts'] = -1;
			}
		}

		// compact images if specified
		if (isset($src['images']) && is_array($src['images']))
		{
			$src['images'] = implode(';;', $src['images']);
		}

		if (isset($src['depositcost']))
		{
			// cast deposit to float
			$src['depositcost'] = abs((float) $src['depositcost']);

			if (isset($src['askdeposit']) && $src['askdeposit'] == 0)
			{
				// unset deposit cost in case it is disabled
				$src['depositcost'] = 0;
			}
		}

		if (isset($src['delivery_areas']) && is_array($src['delivery_areas']))
		{
			// encode the list of accepted delivery areas in JSON
			$src['delivery_areas'] = json_encode($src['delivery_areas']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
