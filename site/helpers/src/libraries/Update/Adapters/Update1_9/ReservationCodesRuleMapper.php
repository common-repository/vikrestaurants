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
 * Reservation codes rule update adapter for 1.9 version.
 *
 * @since 1.9
 */
class ReservationCodesRuleMapper extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$this->mapRules();

		return true;
	}

	/**
	 * Before the 1.9 version of VikRestaurants, the rules were saved with lowercase letters. From now on, since
	 * the PHP classes are under a namespace, it is better to have them with capitalized letters.
	 *
	 * @return  void
	 */
	private function mapRules()
	{
		$db = \JFactory::getDbo();

		// fetch all the reservation codes
		$q = $db->getQuery(true)
			->select($db->qn(['id', 'rule']))
			->from($db->qn('#__vikrestaurants_res_code'))
			->where([
				$db->qn('rule') . ' <> ' . $db->q(''),
				$db->qn('rule') . ' IS NOT NULL',
			]);

		$db->setQuery($q);
		
		foreach ($db->loadObjectList() as $code)
		{
			switch ($code->rule)
			{
				case 'closebill':
					$code->rule = 'CloseBill';
					break;
				
				case 'orderdishes':
					$code->rule = 'OrderDishes';
					break;
				
				default:
					$code->rule = ucfirst($code->rule);
			}
			
			// finalise the update
			$db->updateObject('#__vikrestaurants_res_code', $code, 'id');
		}
	}
}
