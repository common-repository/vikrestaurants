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
 * VikRestaurants take-away item stock refill table.
 *
 * @since 1.8
 */
class VRETableTkstock extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_stock_override', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_takeaway_entry';
	}

	/**
	 * Method to bind an associative array or object to the Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind($src, $ignore = array())
	{
		$src = (array) $src;

		$db = JFactory::getDbo();

		// try to retrieve the stock record if already exists
		$q = $db->getQuery(true)
			->select($db->qn(['s.id', 's.items_available']))
			->from($db->qn($this->getTableName(), 's'))
			->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('s.id_takeaway_option') . ' = ' . $db->qn('o.id'));

		if (empty($src['id']))
		{
			// search by product
			$q->where($db->qn('s.id_takeaway_entry') . ' = ' . (int) $src['id_takeaway_entry']);

			// search by variation, if specified
			if (!empty($src['id_takeaway_option']))
			{
				$q->where($db->qn('s.id_takeaway_option') . ' = ' . (int) $src['id_takeaway_option']);
			}
			// otherwise apply "chained" search 
			else
			{
				$q->where($db->qn('s.id_takeaway_option') . ' IS NULL');
			}
		}
		else
		{
			$q->where($db->qn('s.id') . ' = ' . (int) $src['id']);
		}

		$db->setQuery($q);
		$stock = $db->loadObject();

		if ($stock)
		{
			// Prepare for update.
			// Add/subtract the available items to the current value.
			$src['items_available'] += $stock->items_available;
			// set ID to force the update
			$src['id'] = $stock->id;
		}
		else
		{
			// Prepare for insert.
			// Always start with the initial number of items in stock.
			$src['items_available'] += (int) @$src['items_in_stock'];
			// set empty ID
			$src['id'] = 0;
		}

		// unset variation to keep it NULL
		if (empty($src['id_takeaway_option']))
		{
			$src['id_takeaway_option'] = null;
		}

		// always update the timestamp
		$src['ts'] = VikRestaurants::now();

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
