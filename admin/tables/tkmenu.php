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
 * VikRestaurants take-away menu table.
 *
 * @since 1.8
 */
class VRETableTkmenu extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_takeaway_menus', 'id', $db);

		// register name as required field
		$this->_requiredFields[] = 'title';
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

		$dbo = JFactory::getDbo();

		if (isset($src['start_publishing']) && !is_numeric($src['start_publishing']))
		{
			// convert start publishing to UNIX timestamp
			if (!empty($src['start_publishing']) && $src['start_publishing'] != $dbo->getNullDate())
			{
				list($date, $time) = explode(' ', $src['start_publishing']);
				list($hour, $min)  = explode(':', $time);

				// calculate timestamp
				$src['start_publishing'] = VikRestaurants::createTimestamp($date, $hour, $min);
			}
			else
			{
				// unset start publishing
				$src['start_publishing'] = -1;
			}
		}

		if (isset($src['end_publishing']) && !is_numeric($src['end_publishing']))
		{
			// convert finish publishing to UNIX timestamp
			if (!empty($src['end_publishing']) && $src['end_publishing'] != $dbo->getNullDate())
			{
				list($date, $time) = explode(' ', $src['end_publishing']);
				list($hour, $min)  = explode(':', $time);

				// calculate timestamp
				$src['end_publishing'] = VikRestaurants::createTimestamp($date, $hour, $min);
			}
			else
			{
				// unset finish publishing
				$src['end_publishing'] = -1;
			}
		}

		/**
		 * Unset publishing dates in case the start publishing is
		 * after the finish publishing.
		 *
		 * @since 1.8.3
		 */
		if (!empty($src['start_publishing']) && !empty($src['end_publishing'])
			&& $src['start_publishing'] != -1 && $src['end_publishing'] != -1
			&& $src['start_publishing'] > $src['end_publishing'])
		{
			unset($src['start_publishing']);
			unset($src['end_publishing']);
		}

		// generate alias in case it is empty when creating or updating
		if (empty($src['alias']) && (empty($src['id']) || isset($src['alias'])))
		{
			// generate unique alias starting from title
			$src['alias'] = $src['title'];
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
			$src['alias'] = VRESefHelper::getUniqueAlias($src['alias'], 'tkmenu', $src['id']);
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

			if (isset($data['products']))
			{
				foreach ($data['products'] as &$prod)
				{
					if (is_string($prod))
					{
						$prod = json_decode($prod);
					}
				}
			}
		}

		return parent::setUserStateData($data);
	}
}
