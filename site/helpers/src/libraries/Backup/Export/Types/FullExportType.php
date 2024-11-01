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

use E4J\VikRestaurants\Backup\Export\Director;
use E4J\VikRestaurants\Backup\Export\Type;

/**
 * FULL Backup export type.
 * 
 * @since 1.9
 */
class FullExportType implements Type
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		return \JText::translate('VRE_BACKUP_EXPORT_TYPE_FULL');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_BACKUP_EXPORT_TYPE_FULL_DESCRIPTION');
	}

	/**
	 * @inheritDoc
	 */
	public function build(Director $director)
	{
		// fetch database tables to export
		$tables = $this->getDatabaseTables();

		// iterate all database tables
		foreach ($tables as $table)
		{
			// create a SQL dump and save it into a file
			$director->attachRule(
				new \E4J\VikRestaurants\Backup\Export\Rules\SQLInsertExportRule(
					// this is the adapter that will be used to register the queries
					new \E4J\VikRestaurants\Backup\Export\Rules\SQLFileExportRule($director->getArchive(), $table),
					// this is the name of the database table to dump
					$table
				)
			);
		}

		// register the UPDATE queries for the configuration table
		$director->attachRule(
			// this is the adapter that will be used to register the queries
			new \E4J\VikRestaurants\Backup\Export\Rules\SQLFileExportRule(
				// this is the archive manager
				$director->getArchive(),
				// this is the name of the file
				'#__vikrestaurants_config',
				// these are the update queries
				$this->getConfigSQL()
			)
		);

		// fetch folders to export
		$folders = $this->getFolders();

		// iterate all folders to copy
		foreach ($folders as $folder)
		{
			// create FOLDER export rule
			$director->attachRule(
				new \E4J\VikRestaurants\Backup\Export\Rules\FolderExportRule($director->getArchive(), $folder)
			);
		}
	}

	/**
	 * Returns an array of database tables to export.
	 * 
	 * @return  array
	 */
	protected function getDatabaseTables()
	{
		$db = \JFactory::getDbo();

		// load all the installed database tables
		$tables = $db->getTableList();

		// get current database prefix
		$prefix = $db->getPrefix();

		// replace prefix with placeholder
		$tables = array_map(function($table) use ($prefix)
		{
			return preg_replace("/^{$prefix}/", '#__', $table);
		}, $tables);

		// remove all the tables that do not belong to VikRestaurants
		$tables = array_values(array_filter($tables, function($table)
		{
			if ($table === '#__vikrestaurants_config')
			{
				// exclude the configuration table, which will be handled in a different way
				return false;
			}

			return preg_match("/^#__vikrestaurants_/", $table);
		}));

		return $tables;
	}

	/**
	 * Returns an associative array of folders to export, where the key is equals
	 * to the path to copy and the value is the relative destination path.
	 * 
	 * @return 	array
	 */
	protected function getFolders()
	{
		return [
			'media' => [
				'source'      => VREMEDIA,
				'destination' => 'media/normal',
				'target'      => 'VREMEDIA',
			],
			'media@small' => [
				'source'      => VREMEDIA_SMALL,
				'destination' => 'media/small',
				'target'      => 'VREMEDIA_SMALL',
			],
			'mailtmpl' => [
				'source'      => VREMAIL_TEMPLATES_RESTAURANT,
				'destination' => 'mail/tmpl/restaurant',
				'target'      => 'VREMAIL_TEMPLATES_RESTAURANT',
			],
			'tkmailtmpl' => [
				'source'      => VREMAIL_TEMPLATES_TAKEAWAY,
				'destination' => 'mail/tmpl/takeaway',
				'target'      => 'VREMAIL_TEMPLATES_TAKEAWAY',
			],
			'invoices' => [
				'source'      => VREINVOICE,
				'destination' => 'invoices',
				'target'      => 'VREINVOICE',
			],
			'avatar' => [
				'source'      => VRECUSTOMERS_AVATAR,
				'destination' => 'customers/avatar',
				'target'      => 'VRECUSTOMERS_AVATAR',
			],
			'customcss' => [
				'source'      => \JPath::clean(VREBASE . '/assets/css/vre-custom.css'),
				'destination' => 'css',
				'target'      => ['VREBASE', 'assets/css'],
			],
			'envcss' => [
				'source'      => VRE_CSS_CUSTOMIZER,
				'destination' => 'css/customizer',
				'target'      => 'VRE_CSS_CUSTOMIZER',
			],
			'codehub' => [
				'source'      => VRE_CUSTOM_CODE_FOLDER,
				'destination' => 'codehub',
				'target'      => 'VRE_CUSTOM_CODE_FOLDER',
			],
		];
	}

	/**
	 * Returns an array of queries used to keep the configuration up-to-date.
	 * 
	 * @return 	array
	 */
	protected function getConfigSQL()
	{
		$db = \JFactory::getDbo();

		$sql = [];

		// prepare update statement
		$update = $db->getQuery(true)->update($db->qn('#__vikrestaurants_config'));

		// define list of parameters to ignore
		$exclude = [
			'version',
			'bcv',
			'update_extra_fields',
			'backupfolder',
		];

		// fetch all configuration settings
		$q = $db->getQuery(true)
			->select($db->qn(['param', 'setting']))
			->from($db->qn('#__vikrestaurants_config'))
			->where($db->qn('param') . ' NOT IN (' . implode(',', array_map([$db, 'q'], $exclude)) . ')');

		$db->setQuery($q);
		
		// iterate all settings
		foreach ($db->loadObjectList() as $row)
		{
			// clear update
			$update->clear('set')->clear('where');
			// define value to set
			$update->set($db->qn('setting') . ' = ' . $db->q($row->setting));
			// define parameter to update
			$update->where($db->qn('param') . ' = ' . $db->q($row->param));

			$sql[] = (string) $update;
		}

		return $sql;
	}
}
