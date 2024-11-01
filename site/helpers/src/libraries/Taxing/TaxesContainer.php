<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Taxing;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Database\DatabaseContainer;
use E4J\VikRestaurants\Database\Exception\DatabaseEntryNotFoundException;

/**
 * Container used to pull the taxes from the database.
 *
 * @since 1.9
 */
class TaxesContainer extends DatabaseContainer
{
	/**
	 * @inheritDoc
	 */
	public function get(string $id)
	{
		$query = $this->db->getQuery(true);

		// load tax details
		$query->select($this->db->qn('t.id'));
		$query->select($this->db->qn('t.name'));
		$query->select($this->db->qn('t.description'));
		$query->from($this->db->qn('#__vikrestaurants_tax', 't'));

		// load linked tax rules
		$query->select($this->db->qn('r.id', 'rule_id'));
		$query->select($this->db->qn('r.name', 'rule_name'));
		$query->select($this->db->qn('r.operator', 'rule_operator'));
		$query->select($this->db->qn('r.amount', 'rule_amount'));
		$query->select($this->db->qn('r.cap', 'rule_cap'));
		$query->select($this->db->qn('r.apply', 'rule_apply'));
		$query->select($this->db->qn('r.breakdown', 'rule_breakdown'));
		$query->from($this->db->qn('#__vikrestaurants_tax_rule', 'r'));

		// filter by given tax
		$query->where($this->db->qn('t.id') . ' = ' . (int) $id);
		// apply strict relation because taxes must specify at least
		// a rule, otherwise there wouldn't be anything to calculate
		$query->where($this->db->qn('t.id') . ' = ' . $this->db->qn('r.id_tax'));

		// sort rules by the specified ordering to properly
		// calculate the resulting taxes
		$query->order($this->db->qn('r.ordering') . ' ASC');

		/**
		 * Trigger hook to allow external plugins to manipulate the query used
		 * to load the tax details through this helper class.
		 *
		 * TIP: any column with an alias that starts with "rule_" will be
		 * automatically injected within the rule instance.
		 *
		 * @param   mixed  &$query  A query builder object.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onBeforeQueryTax', [&$query]);

		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();

		if (!$rows)
		{
			// no matching tax
			throw new DatabaseEntryNotFoundException(sprintf('Tax [%d] not found', (int) $id), 404);
		}

		$tax = [];

		// iterate properties of the first record to check
		// what should be injected within the tax object
		foreach (get_object_vars($rows[0]) as $k => $v)
		{
			// inject any property that DOES NOT start with "rule_"
			if (!preg_match("/^rule_/", $k))
			{
				$tax[$k] = $v;
			}
		}

		// create tax instance
		$tax = new Tax($tax);

		// iterate rules
		foreach ($rows as $row)
		{
			$rule = [];

			// iterate properties of the current record to check
			// what should be injected within the rule object
			foreach (get_object_vars($row) as $k => $v)
			{
				// inject any property that STARTS with "rule_"
				if (preg_match("/^rule_(.+?)$/", $k, $match))
				{
					// use property without "rule_"
					$rule[end($match)] = $v;
				}
			}

			// attach rule to parent tax
			$tax->attachRule($rule);
		}

		return $tax;
	}
}
