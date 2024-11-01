<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * This class is used to handle the extra query field for the extension updates.
 *
 * USAGE:
 *
 * $update = new \E4J\VikRestaurants\Update\UriHandler();
 * // or new \E4J\VikRestaurants\Update\UriHandler('com_example')
 *
 * $update->addExtraField('order_number', $order_number);
 * $update->addExtraField('domain', $domain);
 *
 * // OR
 *
 * $update->setExtraFields(array(
 * 		'order_number' 	=> $order_number,
 * 		'domain' 		=> $domain,
 * ));
 *
 * $update->register();
 *
 * In case the component didn't own the <schemapath> XML tag, it suggested to launch
 * also the function below to update correctly the schema version:
 *
 * $update->checkSchema($component_version);
 * 
 * @since 1.7
 * @since 1.9 Renamed from UriUpdateHandler
 */
class UriHandler
{
	/**
	 * The component (or plugin/module) instance that need to be updated.
	 *
	 * @var mixed
	 */
	private $component = null;

	/**
	 * The query string containing the extra fields to append to the update URI.
	 *
	 * @var string
	 */
	private $extraFields = '';

	/**
	 * Class constructor.
	 * 
	 * @param  mixed  $element  The element to load. Null to load the current component.
	 */
	public function __construct($element = null)
	{
		$this->getComponent($element);
	}

	/**
	 * Load the specified/current component.
	 *
	 * @param   mixed  $element  The element to load. Null to load the current component.
	 *
	 * @return  mixed  The loaded component.
	 */
	public function getComponent($element = null)
	{
		if ($element === null)
		{
			$element = \JFactory::getApplication()->input->get('option');
		}

		\JLoader::import('joomla.application.component.helper');
		$this->component = \JComponentHelper::getComponent($element);

		return $this->component;
	}

	/**
	 * Returns the registered extra fields.
	 * 
	 * @param   bool          $array  True to return an array of vars, false to obtain
	 *                                the full query string.
	 * 
	 * @return  string|array  The query string or an associative array of vars.
	 * 
	 * @since   1.9
	 */
	public function getExtraFields($array = false)
	{
		if (!$array)
		{
			// return query string
			return $this->extraFields;
		}

		// create a new URI instance and fetch registered vars
		$uri = new \JUri($this->extraFields);
		return $uri->getQuery($array);
	}

	/**
	 * Set the parameters into the additional query string.
	 *
	 * @param   array  $params  The associative array to push.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function setExtraFields(array $params = array())
	{
		$this->extraFields = '';

		foreach ($params as $key => $val )
		{
			$this->addExtraField($key, $val);
		}

		return $this;
	}

	/**
	 * Push a single value into the additional query string.
	 *
	 * @param   string  $key  The name of the query param.
	 * @param   mixed   $val  The value of the query param. If an array is specified, 
	 *                        the values will be added recursively.
	 *
	 * @return 	self    This object to support chaining.
	 */
	public function addExtraField($key, $val)
	{
		if (is_scalar($val))
		{
			$this->extraFields .= (empty($this->extraFields) ? '' : '&amp;') . $key . "=" . urlencode($val);
		}
		else
		{
			foreach ($val as $inner)
			{
				$this->addExtraField($key . '[]', $inner);
			}
		}

		return $this;
	}

	/**
	 * Commit the changes by updating the extra_fields column of the 
	 * `#__update_sites_extensions` database table.
	 *
	 * @return  bool  True on success, otherwise false.
	 */
	public function register()
	{
		if (!$this->component)
		{
			return false;
		}

		$db = \JFactory::getDbo();

		// load the update site record, if it exists
		$q = $db->getQuery(true);

		$q->select($db->qn('update_site_id'))
			->from($db->qn('#__update_sites_extensions'))
			->where($db->qn('extension_id') . ' = ' . $db->q($this->component->id));

		$db->setQuery($q);
		$updateSite = $db->loadResult();

		$success = false;

		if ($updateSite)
		{
			// update the update site record
			$q = $db->getQuery(true);

			$q->update($db->qn('#__update_sites'))
				->set($db->qn('extra_query') . ' = ' . $db->q($this->extraFields))
				->set($db->qn('enabled') . ' = 1')
				->set($db->qn('last_check_timestamp') . ' = 0')
				->where($db->qn('update_site_id') . ' = ' . $db->q($updateSite));

			$db->setQuery($q);
			$db->execute();

			$success = (bool) $db->getAffectedRows();

			// Delete any existing updates (essentially flushes the updates cache for this update site)
			$q = $db->getQuery(true);

			$q->delete('#__updates')
				->where($db->qn('update_site_id') . ' = ' . $db->q($updateSite));
			
			$db->setQuery($q);
			$db->execute();
		}

		return $success;
	}

	/**
	 * Check the schema of the extension to make sure the system will use
	 * the current version.
	 *
	 * @param   string 	$version  The current version of the component.
	 *
	 * @return  bool    True if the schema has been altered, otherwise false.
	 */
	public function checkSchema($version)
	{
		if (!$this->component)
		{
			return false;
		}

		$ok = false;

		$db = \JFactory::getDbo();

		$q = $db->getQuery(true);

		$q->select($db->qn('version_id'))
			->from($db->qn('#__schemas'))
			->where($db->qn('extension_id') . ' = ' . (int) $this->component->id);
		
		$db->setQuery($q, 0, 1);
		
		$schemaVersion = $db->loadResult();

		if ($schemaVersion)
		{
			if ($schemaVersion == $version)
			{
				$ok = true;
			}
			else
			{
				$q->clear()
					->delete($db->qn('#__schemas'))
					->where($db->qn('extension_id') . ' = ' . (int) $this->component->id);

				$db->setQuery($q);
				$db->execute();
			}
		}

		if (!$ok)
		{
			$q->clear()
				->insert($db->qn('#__schemas'))
				->columns(array($db->qn('extension_id'), $db->qn('version_id')))
				->values($this->component->id . ', ' . $db->q($version));

			$db->setQuery($q);
			$ok = (bool) $db->execute();
		}

		return $ok;
	}
}
