<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * Started the conversion from timestamp integers to DATE columns for higher consistency. Since integers
 * cannot be directly converted into dates, we need to drop the column and then re-add it with the same
 * name and a different type.
 *
 * In order to avoid losing all our stored data, instead of dropping the column, we should simply rename them.
 * This way, after the update, we are able to compile all the new columns with a mass SQL update.
 *
 * Here's the list of tables and columns that implemented a similar change:
 * `#__vikrestaurants_coupon`
 * 
 * @since 1.9
 */
class TimestampDatetimeConverter extends UpdateRule
{
	/**
	 * The timezone used for the timestamps.
	 *
	 * @var string
	 */
	private $timezone;

	/**
	 * The string used to save NULL dates within the database.
	 *
	 * @var string
	 */
	private $nullDate;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $version)
	{
		parent::__construct($version);

		// init the currently set timezone
		$this->timezone = date_default_timezone_get();

		if ($this->timezone === 'UTC')
		{
			// get system timezone
			$sys_tz = \JFactory::getApplication()->get('offset', 'UTC');

			if ($sys_tz !== 'UTC')
			{
				// auto-adjust the times to the timezone set from the CMS configuration
				$this->timezone = $sys_tz;
			}
		}

		// register a NULL date
		$this->nullDate = \JFactory::getDbo()->getNullDate();
	}

	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$this->convertCoupons();

		return true;
	}

	/**
	 * Converts the coupons timestamps into UTC datetimes.
	 * Here's a list of columns that need to be adjusted:
	 * - `datevalid`
	 *
	 * @return  void
	 */
	private function convertCoupons()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn(['id', 'datevalid']))
			->from($db->qn('#__vikrestaurants_coupons'));

		$db->setQuery($query);
		
		foreach ($db->loadObjectList() as $c)
		{
			if (strpos((string) $c->datevalid, '-') === false)
			{
				continue;
			}

			// extract start and end dates from column
			list($start, $end) = explode('-', $c->datevalid);

			// do conversion
			$c->start_publishing = $this->ts2date($start, $this->timezone);
			$c->end_publishing   = $this->ts2date($end  , $this->timezone);

			// commit changes
			$db->updateObject('#__vikrestaurants_coupons', $c, 'id');
		}
	}

	/**
	 * Helper function used to convert a timestamp into a datetime string.
	 *
	 * @param   mixed  $timestamp  The UNIX timestamp to convert.
	 * @param   mixed  $tz         The timezone of the timestamp.
	 *
	 * @return  string
	 */
	protected function ts2date($timestamp, $tz = null)
	{
		if ($timestamp === '' || is_null($timestamp) || $timestamp == -1)
		{
			// return a NULL date
			return $this->nullDate;
		}

		// convert the timestamp by using the native date function so
		// that it will be able to properly use the current timezone
		$date = date('Y-m-d H:i:s', $timestamp);

		// return the SQL date time string
		return \JFactory::getDate($date, $tz)->toSql();
	}
}
