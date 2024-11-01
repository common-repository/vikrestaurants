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
 * VikRestaurants API log table.
 *
 * @since 1.8
 */
class VRETableApilog extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_api_login_logs', 'id', $db);
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
			if (empty($src['ip']))
			{
				// use current IP address in case it hasn't been specified
				$src['ip'] = JFactory::getApplication()->input->server->get('REMOTE_ADDR');
			}

			if (empty($src['createdon']) || $src['createdon'] == -1)
			{
				// register current time in case it hasn't been specified
				$src['createdon'] = VikRestaurants::now();
			}

			// get logging modality:
			// - 2 always
			// - 1 only errors
			// - 0 never
			$mode = VREFactory::getConfig()->getUint('apilogmode');

			$id_login = isset($src['id_login']) ? $src['id_login'] : 0;
			$status   = isset($src['status'])   ? $src['status']   : 0;

			// check if we should insert a new record or if we should
			// update an existing one according to the loggin mode
			if ($mode == 0 || ($mode == 1 && !empty($src['status'])))
			{
				$db = JFactory::getDbo();
				
				$q = $db->getQuery(true)
					->select($db->qn('id'))
					->from($db->qn($this->getTableName()))
					->where($db->qn('id_login') . ' = ' . (int) $id_login)
					->where($db->qn('status') . ' = ' . (int) $status);

				$db->setQuery($q, 0, 1);
				$id = (int) $db->loadResult();

				if ($id)
				{
					// force update
					$src['id'] = $id;
				}
			}
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}
}
