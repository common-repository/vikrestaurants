<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Export\Types;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * MANAGEMENT backup export type.
 * 
 * @since 1.9
 */
class ManagementExportType extends FullExportType
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRE_BACKUP_EXPORT_TYPE_MANAGEMENT');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_BACKUP_EXPORT_TYPE_MANAGEMENT_DESCRIPTION');
	}

	/**
	 * @inheritDoc
	 */
	protected function getDatabaseTables()
	{
		// get database tables from parent
		$tables = parent::getDatabaseTables();

		// define list of database tables to exclude
		$exclude = [
			'#__vikrestaurants_reservation',
			'#__vikrestaurants_res_menus_assoc',
			'#__vikrestaurants_res_prod_assoc',
			'#__vikrestaurants_takeaway_reservation',
			'#__vikrestaurants_takeaway_res_prod_assoc',
			'#__vikrestaurants_takeaway_res_prod_topping_assoc',
			'#__vikrestaurants_takeaway_stock_override',
			'#__vikrestaurants_takeaway_avail_override',
			'#__vikrestaurants_reviews',
			'#__vikrestaurants_users',
			'#__vikrestaurants_user_delivery',
			'#__vikrestaurants_invoice',
			'#__vikrestaurants_order_status',
			'#__vikrestaurants_api_login_event_options',
			'#__vikrestaurants_api_login_logs',
			'#__vikrestaurants_api_ban',
		];

		// remove the specified tables from the list
		$tables = array_values(array_diff($tables, $exclude));

		return $tables;
	}

	/**
	 * @inheritDoc
	 */
	protected function getFolders()
	{
		// get folders from parent
		$folders = parent::getFolders();

		// unset some folders
		unset($folders['invoices']);
		unset($folders['avatar']);

		return $folders;
	}
}
