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
 * VikRestaurants status code model.
 *
 * @since 1.9
 */
class VikRestaurantsModelStatuscode extends JModelVRE
{
	/**
	 * Basic save implementation.
	 *
	 * @param 	mixed  $data  Either an array or an object of data to save.
	 *
	 * @return 	mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		$old = null;

		// in case of update of a status, we should make
		// sure whether the given code is changing
		if (!empty($data['code']) && !empty($data['id']))
		{
			$table = $this->getTable();

			// attempt to load the status details and look for any changes
			if ($table->load($data['id']) && $data['code'] != $table->code)
			{
				// the code seems to change, register old properties for later use
				$old = $table->getProperties();
			}
		}

		// attempt to save the relation
		$id = parent::save($data);

		if (!$id)
		{
			// an error occurred, do not go ahead
			return false;
		}

		if ($old)
		{
			// The code changed, we need to update all the records that
			// are currently assigned to that status code.
			// Mass update records without caring of triggering any events,
			// since we are doing a stability update.
			$db = JFactory::getDbo();

			if ($old['restaurant'])
			{
				$query = $db->getQuery(true)
					->update($db->qn('#__vikrestaurants_reservation'))
					->set($db->qn('status') . ' = ' . $db->q($data['code']))
					->where($db->qn('status') . ' = ' . $db->q($old['code']));

				$db->setQuery($query);
				$db->execute();
			}

			if ($old['takeaway'])
			{
				$query = $db->getQuery(true)
					->update($db->qn('#__vikrestaurants_takeaway_reservation'))
					->set($db->qn('status') . ' = ' . $db->q($data['code']))
					->where($db->qn('status') . ' = ' . $db->q($old['code']));

				$db->setQuery($query);
				$db->execute();
			}
		}
		
		return $id;
	}

	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 */
	public function delete($ids)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		// invoke parent first
		if (!parent::delete($ids))
		{
			// nothing to delete
			return false;
		}

		$db = JFactory::getDbo();

		// load any assigned translation
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_lang_status_code'))
			->where($db->qn('id_status_code') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($query);

		if ($languages = $db->loadColumn())
		{
			// delete assigned translations
			JModelVRE::getInstance('langstatuscode')->delete($languages);
		}

		return true;
	}

	/**
	 * Helper method used to ensure that all the required status codes have been
	 * properly configured for all the sections.
	 * 
	 * It is possible to use the getErrors() method to fetch the list of errors
	 * that have been registered while running the tests.
	 * 
	 * @return 	bool  True in case of success, false otherwise.
	 */
	public function runTests()
	{
		// build the array of tests
		$tests = [
			// define an array of status codes required to the restaurant group
			'restaurant' => [
				'confirmed',
				'paid',
				'pending',
				'removed',
				'cancelled',
			],
			// define an array of status codes required to the take-away group
			'takeaway' => [
				'confirmed',
				'paid',
				'pending',
				'removed',
				'cancelled',
			],
		];

		// ignore tests for the restaurant in case this section is unused
		if (!VikRestaurants::isRestaurantEnabled())
		{
			// restaurant disabled, ignore tests for this group
			unset($tests['restaurant']);
		}

		// ignore tests for the take-away in case this section is unused
		if (!VikRestaurants::isTakeAwayEnabled())
		{
			// take-away disabled, ignore tests for this group
			unset($tests['takeaway']);
		}

		$status = true;

		// iterate all groups
		foreach ($tests as $group => $roles)
		{
			// iterate all roles
			foreach ($roles as $role)
			{
				try
				{
					// try to fetch the status code
					JHtml::fetch('vrehtml.status.' . $role, $group, $column = 'code', $strict = true);
				}
				catch (Exception $e)
				{
					// status not found, register the error message (include the group alias)
					$this->setError($e->getMessage() . ' (' . $group . ')');
					$status = false;
				}
			}
		}

		return $status;
	}

	/**
	 * Restores the status codes to the factory settings.
	 * 
	 * @return  void
	 */
	public function restore()
	{
		$db = JFactory::getDbo();

		// delete all the existing status codes
		$db->setQuery("TRUNCATE TABLE `#__vikrestaurants_status_code`");
		$db->execute();

		// delete all the existing status codes translations
		$db->setQuery("TRUNCATE TABLE `#__vikrestaurants_lang_status_code`");
		$db->execute();

		// re-create all the default status codes
		$query = "INSERT INTO `#__vikrestaurants_status_code`
		(     `name`, `code`,  `color`, `ordering`, `approved`, `reserved`, `expired`, `cancelled`, `paid`, `restaurant`, `takeaway`) VALUES
		('Confirmed',    'C', '008000',          1,          1,          1,         0,           0,      0,            1,          1),
		(     'Paid',    'P', '339CCC',          2,          1,          1,         0,           0,      1,            1,          1),
		(  'Pending',    'W', 'FF7000',          3,          0,          1,         0,           0,      0,            1,          1),
		(  'Removed',    'E', '990000',          4,          0,          0,         1,           0,      0,            1,          1),
		('Cancelled',    'X', 'F01B17',          5,          0,          0,         0,           1,      0,            1,          1),
		( 'Refunded',    'R', '8116C9',          6,          0,          0,         0,           1,      1,            1,          1),
		(  'No-Show',    'N', '828282',          7,          1,          1,         0,           0,      0,            1,          1);";

		$db->setQuery($query);
		$db->execute();
	}
}
