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
 * Auto-generate a secret key for each existing table and a pin code for the restaurant reservations.
 * The secret key is used to grant a secure access to the ordering page through a QR code scan, while
 * the pin code is used to .
 *
 * @since 1.9
 */
class TablesSecretKeyGenerator extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$this->generateTableSecretKey();
		$this->generateReservationPinCode();

		return true;
	}

	/**
	 * Generates a new secret key for each table.
	 *
	 * @return  void
	 */
	private function generateTableSecretKey()
	{
		$db = \JFactory::getDbo();

		// fetch all the tables
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_table'));

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $table)
		{
			// generate a new secret key
			$table->secretkey = \VikRestaurants::generateSerialCode(16, 'table-secret');
			
			// finalise the update
			$db->updateObject('#__vikrestaurants_table', $table, 'id');
		}
	}

	/**
	 * Generates a new pin code for each reservation.
	 *
	 * @return  void
	 */
	private function generateReservationPinCode()
	{
		$db = \JFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			// generate a random PIN code between 0001 and 9999
			->set($db->qn('pin') . ' = LPAD(CAST(FLOOR(RAND() * 9999) + 1 AS CHAR), 4, \'0\');');

		$db->setQuery($query);
		$db->execute();
	}
}
