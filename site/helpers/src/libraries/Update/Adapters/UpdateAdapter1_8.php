<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Update adapter for com_vikrestaurants 1.8 version.
 *
 * NOTE. do not call exit() or die() because the update won't be finalised correctly.
 * Return false instead to stop in anytime the flow without errors.
 *
 * @since 1.8
 * @since 1.9  Renamed from VikRestaurantsUpdateAdapter1_8
 */
abstract class UpdateAdapter1_8
{
	/**
	 * Method run during update process.
	 *
	 * @param   object   $parent  The parent that calls this method.
	 *
	 * @return  boolean  True on success, otherwise false to stop the flow.
	 */
	public static function update($parent)
	{
		self::adjustReservationClosure();

		return true;
	}

	/**
	 * Method run during postflight process.
	 *
	 * @param   object 	 $parent  The parent that calls this method.
	 *
	 * @return 	boolean  True on success, otherwise false to stop the flow.
	 */
	public static function finalise($parent)
	{
		return true;
	}

	/**
	 * Method run before executing VikRestaurants for the first time
	 * after the update completion.
	 *
	 * @param   object  $parent  The parent that calls this method.
	 *
	 * @return  bool    True on success, otherwise false to stop the flow.
	 */
	public static function afterupdate($parent)
	{
		// update BC version to the current one before executing the process,
		// so that in case of errors it won't be executed anymore
		\VREFactory::getConfig()->set('bcv', '1.8');

		self::setupRecordsAlias();

		return true;
	}

	/**
	 * Marks the closure column for all the reservations that
	 * own a purchaser nominative equals to CLOSURE.
	 *
	 * @return  bool
	 */
	protected static function adjustReservationClosure()
	{
		$db = \JFactory::getDbo();

		$q = $db->getQuery(true)
			->update($db->qn('#__vikrestaurants_reservation'))
			->set($db->qn('closure') . ' = 1')
			->where('BINARY ' . $db->qn('purchaser_nominative') . ' = ' . $db->q('CLOSURE'));

		$db->setQuery($q);
		$db->execute();

		return true;
	}

	/**
	 * Creates a default alias for all the tables that
	 * require it for routing purposes.
	 *
	 * @return  bool
	 */
	protected static function setupRecordsAlias()
	{
		$db = \JFactory::getDbo();

		// create alias for menus
		$model = \JModelVRE::getInstance('menu');

		$q = $db->getQuery(true)
			->select($db->qn(['id', 'name']))
			->from($db->qn('#__vikrestaurants_menus'))
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($q);

		foreach ($db->loadAssocList() as $menu)
		{
			$menu['alias'] = '';
			$model->save($menu);
		}

		// create alias for take-away menus
		$model = \JModelVRE::getInstance('tkmenu');

		$q = $db->getQuery(true)
			->select($db->qn(['id', 'title']))
			->from($db->qn('#__vikrestaurants_takeaway_menus'))
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($q);
		
		foreach ($db->loadAssocList() as $menu)
		{
			$menu['alias'] = '';
			$model->save($menu);
		}

		// create alias for products
		$model = \JModelVRE::getInstance('tkentry');

		$q = $db->getQuery(true)
			->select($db->qn(['id', 'name', 'id_takeaway_menu']))
			->from($db->qn('#__vikrestaurants_takeaway_menus_entry'))
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($q);

		foreach ($db->loadAssocList() as $prod)
		{
			$prod['alias'] = '';
			$model->save($prod);
		}

		// create alias for options	
		$model = \JModelVRE::getInstance('tkentryoption');

		$q = $db->getQuery(true)
			->select($db->qn(['id', 'name', 'id_takeaway_menu_entry']))
			->from($db->qn('#__vikrestaurants_takeaway_menus_entry_option'))
			->order($db->qn('ordering') . ' ASC');

		$db->setQuery($q);
		
		foreach ($db->loadAssocList() as $opt)
		{
			$opt['alias'] = '';
			$model->save($opt);
		}

		return true;
	}
}
