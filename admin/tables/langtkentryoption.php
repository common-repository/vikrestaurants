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
 * VikRestaurants language take-away menu entry option table.
 *
 * @since 1.8
 */
class VRETableLangtkentryoption extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_lang_takeaway_menus_entry_option', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'id_option';
		$this->_requiredFields[] = 'id_parent';
		$this->_requiredFields[] = 'tag';
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

		// check alias only if not empty
		if (!empty($src['alias']))
		{
			$dbo = JFactory::getDbo();
			
			// try to recover real parent ID
			$q = $dbo->getQuery(true)
				->select($dbo->qn('id_entry'))
				->from($dbo->qn('#__vikrestaurants_lang_takeaway_menus_entry'))
				->where($dbo->qn('id') . ' = ' . (int) $src['id_parent']);

			$dbo->setQuery($q);
			$id_parent = (int) $dbo->loadResult();

			VRELoader::import('library.sef.helper');
			// make sure the alias is unique
			$src['alias'] = VRESefHelper::getUniqueAlias($src['alias'], 'tkentryoption', $src['id_option'], $id_parent);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
