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
 * VikRestaurants take-away menu entry table.
 *
 * @since 1.8
 */
class VRETableTkentry extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_menus_entry', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'name';
		$this->_requiredFields[] = 'id_takeaway_menu';
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

		// fetch ordering for new products
		if ($src['id'] == 0)
		{
			$src['ordering'] = $this->getNextOrder();
		}

		// generate alias in case it is empty when creating or updating
		if (empty($src['alias']) && (empty($src['id']) || isset($src['alias'])))
		{
			// generate unique alias starting from name
			$src['alias'] = $src['name'];
		}
		
		// check if we are going to update an empty alias
		if (isset($src['alias']) && strlen($src['alias']) == 0)
		{
			// avoid to update an empty alias by using a uniq ID
			$src['alias'] = uniqid();
		}

		if (!empty($src['alias']))
		{
			VRELoader::import('library.sef.helper');
			// make sure the alias is unique
			$src['alias'] = VRESefHelper::getUniqueAlias($src['alias'], 'tkentry', $src['id'], $src['id_takeaway_menu']);
		}

		// check if the image attribute is an array
		if (isset($src['img_path']) && is_array($src['img_path']))
		{
			$images = $src['img_path'];

			// take the first element as main image
			$src['img_path'] = (string) array_shift($images);
			// assign the remaining elements to the extra images
			$src['img_extra'] = $images;
		}

		// check if the extra images attribute is an array
		if (isset($src['img_extra']) && is_array($src['img_extra']))
		{
			// JSON encode the extra images
			$src['img_extra'] = json_encode($src['img_extra']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}

	/**
	 * Helper method used to store the user data within the session.
	 *
	 * @param 	mixed 	$data  The array data to store.
	 *
	 * @return 	self    This object to support chaining.
	 * 
	 * @since   1.9
	 */
	public function setUserStateData($data = null)
	{
		if ($data)
		{
			$data = (array) $data;

			if (isset($data['options']))
			{
				foreach ($data['options'] as &$opt)
				{
					if (is_string($opt))
					{
						$opt = json_decode($opt);
					}
				}
			}

			if (isset($data['groups']))
			{
				foreach ($data['groups'] as &$group)
				{
					if (is_string($group))
					{
						$group = json_decode($group);
					}
				}
			}

			$data['images'] = [];

			if (!empty($data['img_path']))
			{
				$data['images'] = $data['img_path'];
			}
		}

		return parent::setUserStateData($data);
	}
}
