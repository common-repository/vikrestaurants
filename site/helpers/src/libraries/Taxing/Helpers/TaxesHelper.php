<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Taxing\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Taxes helper class.
 *
 * @since 1.9
 */
abstract class TaxesHelper
{
	/**
	 * Lookup used to store the TAX id of the specified subjects.
	 *
	 * @var array
	 */
	protected static $lookup = [];

	/**
	 * Returns the tax ID used by the specified subject.
	 *
	 * @param   integer  $id       The record id.
	 * @param   string   $subject  An identifier to detect the table of the record.
	 *
	 * @return  mixed    The tax ID on success, false otherwise.
	 */
	public static function getTaxOf($id, string $subject)
	{
		if (!isset(static::$lookup[$subject]))
		{
			// init subject pool
			static::$lookup[$subject] = [];
		}

		// look for a cached ID
		if (!isset(static::$lookup[$subject][$id]))
		{
			$db = \JFactory::getDbo();

			$query = $db->getQuery(true);

			$query->select($db->qn('id_tax'));
			$query->where($db->qn('id') . ' = ' . (int) $id);

			switch ($subject)
			{
				case 'restaurant.menusproduct':
					$query->from($db->qn('#__vikrestaurants_section_product'));
					break;

				case 'payment':
					$query->from($db->qn('#__vikrestaurants_gpayments'));
					break;

				case 'takeaway.item':
					$query->from($db->qn('#__vikrestaurants_takeaway_menus_entry'));
					break;

				/**
				 * @todo  Consider to implement an apposite field to choose
				 *        the taxes amount for the take-away services.
				 */
				// case 'takeaway.service':

				default:
					return false;
			}

			$db->setQuery($query, 0, 1);
			// cache tax ID
			static::$lookup[$subject][$id] = $db->loadResult() ?: false;
		}

		return static::$lookup[$subject][$id];
	}
}
