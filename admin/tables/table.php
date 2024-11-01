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
 * VikRestaurants roomtable table.
 *
 * @since 1.8
 */
class VRETableTable extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_table', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'name';
		$this->_requiredFields[] = 'id_room';
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

		if (empty($src['id']))
		{
			// generate secret key if not specified
			if (!isset($src['secretkey']))
			{
				// use a non-string to force the regeneration later
				$src['secretkey'] = true;
			}

			// create design data for new tables (if not specified)
			if (!isset($src['design_data']))
			{
				$dbo = JFactory::getDbo();

				$q = $dbo->getQuery(true)
					->select($dbo->qn('design_data'))
					->from($dbo->qn('#__vikrestaurants_table'))
					->order($dbo->qn('id') . ' DESC');

				if (isset($src['id_room']))
				{
					$q->where($dbo->qn('id_room') . ' = ' . (int) $src['id_room']);
				}

				$dbo->setQuery($q, 0, 1);
				$dd = $dbo->loadResult();

				if ($dd)
				{
					$dd = json_decode($dd, true);
				}

				// create table graphics properties
				$src['design_data'] = json_encode($this->createTableProperties($dd));
			}
		}
		
		// stringify design data
		if (isset($src['design_data']) && !is_scalar($src['design_data']))
		{
			$src['design_data'] = json_encode($src['design_data']);
		}

		// regenerate the secret key in case of invalid string
		if (isset($src['secretkey']) && !is_string($src['secretkey']))
		{
			$src['secretkey'] = VikRestaurants::generateSerialCode(16, 'table-secret');
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to
	 * ensure they are safe to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		// check integrity using parent
		if (!parent::check())
		{
			return false;
		}
		
		// check capacity
		if ((isset($this->min_capacity) || isset($this->max_capacity)) && $this->min_capacity > $this->max_capacity)
		{
			// register error message
			$this->setError(JText::sprintf('VRE_INVALID_REQ_FIELD', JText::translate('VRMANAGETABLE2')));

			// invalid capacity
			return false;
		}

		return true;
	}

	/**
	 * Creates table graphics properties.
	 *
	 * @param 	mixed  $data  The design data.
	 *
	 * @return  array  The graphics properties.
	 */
	protected function createTableProperties($data = null)
	{
		if (!$data)
		{
			$data = array();
			$data['posx'] 		= 40;
			$data['posy']		= 40;
			$data['width'] 		= 100;
			$data['height']		= 100;
			$data['rotate'] 	= 0;
			$data['bgColor'] 	= 'a3a3a3';
			$data['roundness']  = 0;
			$data['class']		= 'UIShapeRect';
		}
		else
		{
			if ($data['class'] == 'UIShapeCircle')
			{
				$w = $h = $data['radius'] * 2;
			}
			else
			{
				$w = $data['width'];
				$h = $data['height'];
			}

			$data = (array) $data;

			$data['posx'] += $w + 30;

			if ($data['posx'] > 800)
			{
				$data['posx'] = 40;
				$data['posy'] += $h + 30;
			}
		}

		return $data;
	}
}
