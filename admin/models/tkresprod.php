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
 * VikRestaurants take-away order product model.
 *
 * @since 1.9
 */
class VikRestaurantsModelTkresprod extends JModelVRE
{
	/**
	 * Loads all the toppings assigned to the specified order item.
	 * 
	 * @param   int  $idItem  The order item ID.
	 * 
	 * @return  object[]
	 */
	public function getToppings(int $idItem)
	{
		$db = JFactory::getDbo();

		$groups = [];

		// recover item toppings
		$query = $db->getQuery(true)
			->select('a.*')
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc', 'a'))
			->where($db->qn('a.id_assoc') . ' = ' . $idItem);

		$db->setQuery($query);
		
		foreach ($db->loadObjectList() as $t)
		{
			if (!isset($groups[$t->id_group]))
			{
				$group = new stdClass;
				$group->id       = $t->id_group;
				$group->toppings = [];
				$group->units    = [];

				$groups[$t->id_group] = $group;
			}

			if (!empty($t->id_topping))
			{
				$groups[$t->id_group]->toppings[] = (int) $t->id_topping;
				$groups[$t->id_group]->units[(int) $t->id_topping] = (int) $t->units;
			}
		}

		return array_values($groups);
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

		/** @var JModelLegacy */
		$prodModel = JModelVRE::getInstance('tkentry');

		if (!empty($data['id_product']))
		{
			/** @var stdClass|null */
			$product = $prodModel->getItem((int) $data['id_product']);

			if (!$product)
			{
				// item not found...
				$this->setError(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
				return false;
			}

			/** @var object[] */
			$product->options = $prodModel->getVariations($product->id);

			if (empty($data['id_product_option']) && $product->options)
			{
				// take the first available option
				$data['id_product_option'] = $product->options[0]->id;
			}
			else if (!$product->options)
			{
				// always unset option ID
				$data['id_product_option'] = 0;
			}
		}

		// attempt to save the record
		$id =  parent::save($data);

		if (!$id)
		{
			return false;
		}

		if (isset($data['groups']))
		{
			// register the specified toppings
			$this->setAttachedToppings($id, $data['groups']);
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

		// load any assigned toppings
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc'))
			->where($db->qn('id_assoc') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($toppings = $db->loadColumn())
		{
			// delete all the toppings that belong to the removed products
			JModelVRE::getInstance('tkresprodtopping')->delete($toppings);
		}

		return true;
	}

	/**
	 * Assigns all the specified toppings to the item.
	 * The toppings that were already assigned and are not reported
	 * within the list will be permanently deleted.
	 *
	 * @param   int      $id           The item primary key.
	 * @param   array    $groups       A list of group-topping relations.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	protected function setAttachedToppings(int $id, array $groups = [])
	{
		$db = JFactory::getDbo();

		// get existing records

		$existing = $saved = [];

		$q = $db->getQuery(true)
			->select($db->qn('id_group'))
			->select($db->qn('id_topping'))
			->from($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc'))
			->where($db->qn('id_assoc') . ' = ' . (int) $id);

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $r)
		{
			$existing[] = (int) $r->id_group . ':' . (int) $r->id_topping;
		}

		// insert new records

		$has = $aff = false;

		$insert = $db->getQuery(true)
			->insert($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc'))
			->columns($db->qn(['id_assoc', 'id_group', 'id_topping', 'units']));

		foreach ($groups as $group)
		{
			foreach ($group['toppings'] as $toppingId)
			{
				/**
				 * Fetch number of units.
				 *
				 * @since 1.8.2
				 */
				if (isset($group['units'][$toppingId]))
				{
					$units = (int) $group['units'][$toppingId];
				}
				else
				{
					$units = 1;
				}

				$needle = (int) $group['id'] . ':' . (int) $toppingId;

				// make sure the record to push doesn't exist yet
				if (!in_array($needle, $existing))
				{
					$insert->values((int) $id . ', ' . (int) $group['id'] . ', ' . (int) $toppingId . ', ' . $units);
					$has = true;
				}
				else
				{
					/**
					 * Otherwise try to update number of units.
					 *
					 * @since 1.8.2
					 */
					$update = $db->getQuery(true)
						->update($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc'))
						->set($db->qn('units') . ' = ' . $units)
						->where([
							$db->qn('id_assoc') . ' = ' . (int) $id,
							$db->qn('id_group') . ' = ' . (int) $group['id'],
							$db->qn('id_topping') . ' = ' . (int) $toppingId,
						]);

					$db->setQuery($update);
					$db->execute();
				}

				$saved[] = $needle;
			}
		}

		if ($has)
		{
			$db->setQuery($insert);
			$db->execute();

			$aff = (bool) $db->getAffectedRows();
		}

		// delete records

		$deleted = [];

		foreach ($existing as $r)
		{
			// make sure the records to delete is not contained in the saved records
			if (!in_array($r, $saved))
			{
				$deleted[] = $r;
			}
		}

		// detach previous elements, if any
		foreach ($deleted as $d)
		{
			list($groupId, $toppingId) = explode(':', $d);

			$delete = $db->getQuery(true)
				->delete($db->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc'))
				->where([
					$db->qn('id_assoc') . ' = ' . (int) $id,
					$db->qn('id_group') . ' = ' . (int) $groupId,
					$db->qn('id_topping') . ' = ' . (int) $toppingId,
				]);

			$db->setQuery($delete);
			$db->execute();

			$aff = $aff || $db->getAffectedRows();
		}	

		return $aff;
	}

	/**
	 * Changes the reservation code for the specified record.
	 * 
	 * @param   int    $id    The product ID.
	 * @param   mixed  $code  Either an array/object containing the code details
	 *                        or the primary key of the code.
	 * 
	 * @return  bool   True on success, false otherwise.
	 * 
	 * @since   1.9.1
	 */
	public function changeCode(int $id, $code)
	{
		if ((int) $id <= 0)
		{
			$this->setError(new Exception('Missing product ID', 400));
			return false;
		}

		if (is_numeric($code))
		{
			/** @var stdClass */
			$code = JModelVRE::getInstance('rescode')->getItem((int) $code, $blank = true);
		}

		$code = (array) $code;

		if (!isset($code['id']))
		{
			$this->setError(new Exception('Missing reservation code ID', 400));
			return false;
		}

		// save product first
		$saved = $this->save([
			'id'      => (int) $id,
			'rescode' => $code['id'],
		]);

		if (!$saved)
		{
			// unable to save the product
			return false;
		}

		if ($code['id'])
		{
			/** @var JModelLegacy */
			$resCodeModel = JModelVRE::getInstance('rescodeorder');

			// try to update the history of the product
			$saved = $resCodeModel->save([
				// use a different identifier (4) to avoid conflicts with the food of the restaurant (3)
				'group'      => 4,
				'id_order'   => (int) $id,
				'id_rescode' => (int) $code['id'],
				'notes'      => $code['notes'] ?? null,
			]);

			if (!$saved)
			{
				// propagate encountered error
				$this->setError($resCodeModel->getError());
				return false;
			}
		}

		return true;
	}
}
