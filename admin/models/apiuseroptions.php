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
 * VikRestaurants API user-event config model.
 *
 * @since 1.9
 */
class VikRestaurantsModelApiuseroptions extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return 	mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		// load item through parent
		$item = parent::getItem($pk, $new);

		if ($item)
		{
			$item->options = $item->options ? (array) json_decode($item->options, true) : [];
		}

		return $item;
	}

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

		// in case of new record, look for an existing one
		if (empty($data['id']) && !empty($data['id_login']) && !empty($data['id_event']))
		{
			// load existing record, if any
			$existing = $this->getItem([
				'id_login' => $data['id_login'],
				'id_event' => $data['id_event'],
			]);

			if ($existing)
			{
				// override record ID
				$data['id'] = $existing->id;
			}
		}

		// attempt to save the record
		return parent::save($data);
	}

	/**
	 * Returns the record assigned to the specified login/event.
	 *
	 * @param 	integer  $id_login  The login primary key.
	 * @param 	string   $id_event  The event unique name.
	 *
	 * @return 	array|null
	 */
	public function getOptions(int $id_login, string $id_event)
	{
		/** @var stdClass|null */
		$item = $this->getItem([
			'id_login' => (int) $id_login,
			'id_event' => $id_event,
		]);

		return $item ? $item->options : [];
	}
}
