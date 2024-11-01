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
 * Custom fields update adapter rule for 1.9 version.
 *
 * @since 1.9
 */
class CustomFieldsMapper extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$this->mapRules();
		$this->encodeSelectOptions();
		$this->addServiceSupport();

		return true;
	}

	/**
	 * Since the rules are now extendable, we had to refactor the column used to identify the rules, by
	 * switching the integer ID into the related file name.
	 *
	 * During the installation of the update, we need to map the existing custom fields.
	 *
	 * @return  void
	 */
	private function mapRules()
	{
		$db = \JFactory::getDbo();

		// create lookup to assign the correct rule
		$lookup = [
			0  => '',
			1  => 'nominative',
			2  => 'email',
			3  => 'phone',
			4  => 'address',
			5  => 'delivery',
			6  => 'zip',
			7  => 'city',
			8  => 'state',
			9  => 'pickup',
			10 => 'notes',
			11 => 'deliverynotes',
		];

		// fetch all the custom fields
		$q = $db->getQuery(true)
			->select($db->qn(['id', 'rule']))
			->from($db->qn('#__vikrestaurants_custfields'));

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $f)
		{
			// make sure the rule is supported
			if (!isset($lookup[$f->rule]))
			{
				continue;
			}

			// assign the new rule alias
			$f->rule = $lookup[$f->rule];
			// finalise the update
			$db->updateObject('#__vikrestaurants_custfields', $f, 'id');
		}
	}

	/**
	 * The options of a select are now encoded within a JSON string, so that we can track the relations
	 * between the options and their translations. For this reason, we must iterate all the existing custom
	 * fields, fetch the options, JSON-encode them and save the records.
	 *
	 * NOTE: the values of the custom fields stored within the database will lose the relation with the
	 * existing options.
	 *
	 * @return 	void
	 */
	private function encodeSelectOptions()
	{
		$db = \JFactory::getDbo();

		$ids = [];

		// fetch all the "select" custom fields
		$q = $db->getQuery(true)
			->select($db->qn(['id', 'choose']))
			->from($db->qn('#__vikrestaurants_custfields'))
			->where($db->qn('type') . ' = ' . $db->q('select'));

		$db->setQuery($q);

		foreach ($db->loadObjectList() as $f)
		{
			// JSON encode options
			$f->choose = json_encode(explode(';;__;;', $f->choose));
			
			// finalise the update
			$db->updateObject('#__vikrestaurants_custfields', $f, 'id');

			$ids[] = $f->id;
		}

		if ($ids)
		{
			// do the same for the translations of the custom fields
			$q = $db->getQuery(true)
				->select($db->qn(['id', 'choose']))
				->from($db->qn('#__vikrestaurants_lang_customf'))
				->where($db->qn('id_customf') . ' IN (' . implode(',', $ids) . ')');

			$db->setQuery($q);
			
			foreach ($db->loadObjectList() as $f)
			{
				// JSON encode options
				$f->choose = json_encode(explode(';;__;;', $f->choose));
				
				// finalise the update
				$db->updateObject('#__vikrestaurants_lang_customf', $f, 'id');
			}
		}
	}

	/**
	 * Before the 1.9 version, the custom fields required only for delivery or pick up service had
	 * the `required_delivery` column respectively set to 1 and 2. From now on, this kind of
	 * restriction is automatically applied depending on the selected service.
	 * 
	 * For this reaosn, we should automatically assign all the conditional fields to the related
	 * service ("delivery" when `required_delivery` = 1, "pickup" when `required_delivery` = 2).
	 * 
	 * @return  void
	 */
	private function addServiceSupport()
	{
		$db = \JFactory::getDbo();

		// set "delivery" service when `required_delivery` is equals to "1"
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_custfields'))
			->set($db->qn('service') . ' = ' . $db->q('delivery'))
			->where($db->qn('required_delivery') . ' = 1');

		$db->setQuery($q);
		$db->execute();

		// set "pickup" service when `required_delivery` is equals to "2"
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_custfields'))
			->set($db->qn('service') . ' = ' . $db->q('pickup'))
			->where($db->qn('required_delivery') . ' = 2');

		$db->setQuery($q);
		$db->execute();

		// set "delivery" service for fields with "delivery" rule
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_custfields'))
			->set($db->qn('service') . ' = ' . $db->q('delivery'))
			->set($db->qn('rule') . ' = ' . $db->q(''))
			->where($db->qn('rule') . ' = ' . $db->q('delivery'));

		$db->setQuery($q);
		$db->execute();

		// set "pickup" service for fields with "pickup" rule
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_custfields'))
			->set($db->qn('service') . ' = ' . $db->q('pickup'))
			->set($db->qn('rule') . ' = ' . $db->q(''))
			->where($db->qn('rule') . ' = ' . $db->q('pickup'));

		$db->setQuery($q);
		$db->execute();

		// set "delivery" service for fields with "deliverynotes" rule
		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_custfields'))
			->set($db->qn('service') . ' = ' . $db->q('delivery'))
			->where($db->qn('rule') . ' = ' . $db->q('deliverynotes'));

		$db->setQuery($q);
		$db->execute();
	}
}
