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
 * VikRestaurants media files table.
 *
 * @since 1.8
 */
class VRETableMedia extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_media', 'id', $db);
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

		if (empty($src['id']) && !empty($src['image']))
		{
			$db = JFactory::getDbo();

			// search a media record with the specified image
			$q = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn($this->getTableName()))
				->where($db->qn('image') . ' = ' . $db->q($src['image']));

			$db->setQuery($q, 0, 1);
			$id = $db->loadResult();

			if ($id)
			{
				// media found, register PK
				$src['id'] = (int) $id;
			}
		}

		// invoke parent
		return parent::bind($src, $ignore);
	}
}
