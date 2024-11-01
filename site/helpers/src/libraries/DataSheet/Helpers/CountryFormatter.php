<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DataSheet\Helpers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Trait used to easily format the details of the countries.
 * 
 * @since 1.9
 */
trait CountryFormatter
{
	/**
	 * Internal property used to cache the fetched details.
	 * 
	 * @var array
	 */
	private $countriesLookup = [];

	/**
	 * Fetches the name of the matching ISO 3166 country code.
	 * 
	 * @param   string  $code  The ISO 3166 country code (alpha-2).
	 * 
	 * @return  string  The matching country name.
	 */
	public function toCountryName(string $code)
	{
		// Fetch country name.
		// We don't need to cache the result as this job is already applied by the used method.
		$country = \JHtml::fetch('vrehtml.countries.withcode', $code);

		return $country ? $country->name : '';
	}

	/**
	 * Fetches the ISO 3166 code of the matching country name.
	 * 
	 * @param   string  $name  The country name.
	 * 
	 * @return  string  The ISO 3166 country code (alpha-2).
	 */
	public function toISO3166(string $name)
	{
		// check whether the code of this country has been already fetched
		if (!isset($this->countriesLookup[$name]))
		{
			// access database instance if available, otherwise access the global one
			if (property_exists($this, 'db'))
			{
				$db = $this->db;
			}
			else
			{
				$db = \JFactory::getDbo();
			}

			// search country code by name
			$query = $db->getQuery(true)
				->select($db->qn('country_2_code'))
				->from($db->qn('#__vikrestaurants_countries'))
				->where($db->qn('country_name') . ' = ' . $db->q($name));

			$db->setQuery($query, 0, 1);

			// cache country code for later use
			$this->countriesLookup[$name] = (string) $db->loadResult();
		}

		return $this->countriesLookup[$name];
	}
}
