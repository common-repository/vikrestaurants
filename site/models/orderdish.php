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
 * VikRestaurants order dishes view model.
 *
 * @since 1.9
 */
class VikRestaurantsModelOrderdish extends JModelVRE
{
	/**
	 * Resolves a QR code scan and returns the matching reservation, if any.
	 * 
	 * @param   string       $table  The table secret key.
	 * 
	 * @return  object|bool  The reservation details on success, false otherwise.
	 */
	public function resolveQR(string $table)
	{
		if (!$table)
		{
			// table key not provided
			$this->setError(new Exception('Missing table', 400));
			return false;
		}

		// fetch table starting from the secret key
		$table = JModelVRE::getInstance('table')->getItem([
			'secretkey' => $table,
		]);

		if (!$table)
		{
			// no table matching the provided secret key
			$this->setError(new Exception('Table not found', 404));
			return false;
		}

		/**
		 * We don't need to check whether the table has been published
		 * or not because the administrator might have disabled a table
		 * just to prevent direct online bookings.
		 */

		// fetch current date and time
		$now = VikRestaurants::now();

		// create search parameters
		$search = new VREAvailabilitySearch($now, date('G:i', $now));

		// fetch all the approved reservations for the current time
		$reservations = $search->getReservations();

		// filter the reservations to obtain only the ones assigned to the specified table
		$reservations = array_filter($reservations, function($reservation) use ($table)
		{
			return $reservation->id_table == $table->id;
		});

		if (!$reservations)
		{
			// could not find any reservation for the selected table
			$this->setError(new Exception(JText::translate('VRE_QR_RES_NOT_FOUND'), 404));
			return false;
		}

		if (count($reservations) > 1)
		{
			// there are multiple reservations assigned to the selected table
			$this->setError(new Exception(JText::translate('VRE_QR_RES_MULTI_ERR'), 500));
			return false;
		}

		// return the first element of the array
		return reset($reservations);
	}
}
