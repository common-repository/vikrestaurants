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
 * VikRestaurants e-mail conditional texts table.
 *
 * @since 1.9
 */
class VRETableMailtext extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_mail_text', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'name';
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
			if (E4J\VikRestaurants\Helpers\DateHelper::isNull($src['created'] ?? null))
			{
				$src['created'] = JFactory::getDate()->toSql();
			}

			$src['ordering'] = $this->getNextOrder();
		}

		// stringify conditional text filters
		if (isset($src['filters']) && !is_string($src['filters']))
		{
			$src['filters'] = json_encode($src['filters']);
		}

		// stringify conditional text actions
		if (isset($src['actions']) && !is_string($src['actions']))
		{
			$src['actions'] = json_encode($src['actions']);
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
