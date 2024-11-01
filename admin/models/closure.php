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
 * VikRestaurants restaurant closure model.
 *
 * @since 1.9
 */
class VikRestaurantsModelClosure extends JModelVRE
{
	/**
	 * Basic save implementation.
	 *
	 * @param   mixed  $data  Either an array or an object of data to save.
	 *
	 * @return  mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		if (empty($data['id_table']))
		{
			// the table ID is mandatory
			$this->setError(new InvalidArgumentException('Missing table ID.', 400));
			return false;
		}

		$data['people'] = 0;

		if (isset($data['id_table']))
		{
			if (is_string($data['id_table']) && !is_numeric($data['id_table']))
			{
				// probably a JSON string was provided, convert it to an array of integers
				$tables = (array) json_decode($data['id_table']);
			}
			else if (!is_array($data['id_table']))
			{
				// treat number as an array
				$tables = [(int) $data['id_table']];
			}

			/** @var JModelLegacy */
			$tableModel = JModelVRE::getInstance('table');

			// Always use the maximum capacity supported by the selected tables.
			// This avoids to receive other reservations in case a table is shared.
			foreach ($tables as $tableId)
			{
				$data['people'] += (int) $tableModel->getItem($tableId, $blank = true)->max_capacity;
			}
		}

		if (!$data['people'])
		{
			// could not find the requested table
			$this->setError(new RuntimeException('Table ID [' . $data['id_table'] . '] not found', 404));
			return false;
		}

		if (empty($data['stay_time']))
		{
			// use default amount if time of stay was not specified
			$data['stay_time'] = VREFactory::getConfig()->getUint('averagetimestay');
		}

		// flag reservation as closed
		$data['closure'] = 1;

		// always auto-approve the closure
		$data['status'] = JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code');

		if (empty($data['purchaser_nominative']))
		{
			// add a default nominative
			$data['purchaser_nominative'] = 'CLOSURE';
		}

		// attempt to save the closure as a reservation
		return JModelVRE::getInstance('reservation')->save($data);
	}

	/**
	 * Reopens a closed tables by deleting the registered record.
	 * 
	 * @param   int   $id  The closure ID.
	 * 
	 * @return  bool  True on success, false otherwise.
	 */
	public function reopen(int $id)
	{
		// permanently delete the specified closure
		return JModelVRE::getInstance('reservation')->delete([$id]);
	}
}
