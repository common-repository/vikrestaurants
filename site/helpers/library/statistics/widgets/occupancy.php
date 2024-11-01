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
 * Widget class used to calculate the total restaurant occupancy for
 * a given date and time.
 *
 * Displays a "PIE" chart showing the percentage of the total occupancy.
 *
 * @since 1.8
 */
class VREStatisticsWidgetOccupancy extends VREStatisticsWidget
{
	/**
	 * @override
	 * Returns the form parameters required to the widget.
	 *
	 * @return 	array
	 */
	public function getForm()
	{
		return array(
			/**
			 * The date to use to check the total occupancy.
			 *
			 * The parameter is VOLATILE because, every time the session
			 * ends, we need to restore the field to an empty value, just
			 * to obtain the current date and time.
			 *
			 * @var calendar
			 */
			'date' => array(
				'type'     => 'calendar',
				'label'    => JText::translate('VRMANAGERESERVATION13'),
				'volatile' => true,
			),

			/**
			 * The hours to use to check the total occupancy.
			 *
			 * The parameter is VOLATILE because, every time the session
			 * ends, we need to restore the field to an empty value, just
			 * to obtain the current date and time.
			 *
			 * @var list
			 */
			'hours' => array(
				'type'     => 'select',
				'label'    => JText::translate('VRMANAGERESERVATION14'),
				'volatile' => true,
				'options'  => array_merge(
					[JHtml::fetch('select.option', '', '--')],
					JHtml::fetch('vikrestaurants.hours')
				),
			),

			/**
			 * The minutes to use to check the total occupancy.
			 *
			 * The parameter is VOLATILE because, every time the session
			 * ends, we need to restore the field to an empty value, just
			 * to obtain the current date and time.
			 *
			 * @var list
			 */
			'minutes' => array(
				'type'        => 'select',
				'volatile'    => true,
				'hiddenLabel' => true,
				'options'     => array_merge(
					[JHtml::fetch('select.option', '', '--')],
					JHtml::fetch('vikrestaurants.minutes', $group = 1)
				),
			),

			/**
			 * The color to be used when displaying the chart.
			 * By default, a blue color will be used.
			 *
			 * @var color
			 */
			'color' => array(
				'type'    => 'color',
				'label'   => JText::translate('VRE_UISVG_COLOR'),
				'default' => '307bbb',
			),
		);
	}

	/**
	 * @override
	 * Checks whether the specified group is supported
	 * by the widget. Children classes can override this
	 * method to drop the support for a specific group.
	 *
	 * This widget supports only the "restaurant" group.
	 *
	 * @param 	string 	 $group  The group to check.
	 *
	 * @return 	boolean  True if supported, false otherwise.
	 */
	public function isSupported($group)
	{
		return $group == 'restaurant' ? true : false;
	}

	/**
	 * @override
	 * Loads the dataset(s) that will be recovered asynchronously
	 * for being displayed within the widget.
	 *
	 * It is possible to return an array of records to be passed
	 * to a chart or directly the HTML to replace.
	 *
	 * @return 	mixed
	 */
	public function getData()
	{
		$dbo    = JFactory::getDbo();
		$config = VREFactory::getConfig();

		// get date from request
		$filters = array();
		$filters['date']     = $this->getOption('date');
		$filters['hours']    = $this->getOption(  'hours', JHtml::fetch('date', 'now', 'H'));
		$filters['minutes']  = $this->getOption('minutes', JHtml::fetch('date', 'now', 'i'));

		// use default date if invalid
		if (empty($filters['date']) || $filters['date'] == $dbo->getNullDate())
		{
			// use the current date
			$filters['date'] = JHtml::fetch('date', 'now', $config->get('dateformat'));
		}

		// create search parameters
		$search = new VREAvailabilitySearch($filters['date'], $filters['hours'] . ':' . $filters['minutes']);

		// count guests as admin, in order to include also
		// the customers assigned to unpublished tables/rooms
		$search->setAdmin(true);

		// get tables occurrences
		$tables = $search->getTablesOccurrence();

		// calculate total number of guests
		$guests = array_sum(array_values($tables));

		// DO NOT look as admin to exclude tables that
		// are currently unpublished
		$search->setAdmin(false);

		// calculate total number of seats
		$seats = $search->getSeatsCount();

		if (!$seats)
		{
			// in case of no available seats, just use the
			// number of guests in order to return an
			// occupancy of 100%
			$seats = $guests;
		}
	
		// calculate percentage occupancy (do not divide by 0)
		$occupancy = $seats ? $guests * 100 / $seats : 0;

		if ($occupancy > 99)
		{
			// do not risk to round 99.xx% to 100%
			$occupancy = floor($occupancy);
		}
		else
		{
			// always round to the next integer
			$occupancy = ceil($occupancy);
		}

		// make sure the occupancy didn't exceed the [0-100] range
		$occupancy = max(array(  0, $occupancy));
		$occupancy = min(array(100, $occupancy));

		$ts = E4J\VikRestaurants\Helpers\DateHelper::getTimestamp($search->get('date'), $search->get('hour'), $search->get('min'));

		// prepare return data
		$data = array(
			'occupancy' => $occupancy,
			'guests'    => $guests,
			'seats'     => $seats,
			'datetime'  => $ts,
			'date'      => JHtml::fetch('date', $ts, JText::translate('DATE_FORMAT_LC3'), date_default_timezone_get()),
			'time'      => date($config->get('timeformat'), $ts),
		);

		return $data;
	}
}
