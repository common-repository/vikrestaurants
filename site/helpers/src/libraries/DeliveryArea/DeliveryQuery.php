<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\DeliveryArea;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Composes a query with the address information.
 * 
 * @since 1.9
 */
class DeliveryQuery implements \JsonSerializable
{
	/** @var array */
	protected $components = [];

	/**
	 * Class constructor.
	 * 
	 * @param  array|object  $components  Either an array or an object containing the information details.
	 */
	public function __construct($components = [])
	{
		if (!is_array($components) && !is_object($components))
		{
			// cannot iterate the provided argument
			throw new \InvalidArgumentException('Expected an array or an object, ' . gettype($components) . 'given');
		}

		foreach ($components as $k => $v)
		{
			// attach delivery information to components property
			$this->components[$k] = $v;
		}
	}

	/**
	 * Obtains the latitude and longitude coordinates.
	 * 
	 * @return  object|null
	 */
	public function getCoordinates()
	{
		$lat = $this->getComponent([ 'latitude', 'lat']);
		$lng = $this->getComponent(['longitude', 'lng']);

		if ($lat !== null && $lng !== null)
		{
			$coordinates = new \stdClass;
			$coordinates->latitude  = (float) $lat;
			$coordinates->longitude = (float) $lng;

			return $coordinates;
		}

		return null;
	}

	/**
	 * Obtains the street name and number.
	 * 
	 * @return  string|null
	 */
	public function getAddress()
	{
		// try to look for a full address
		$address = $this->getComponent('address');

		if ($address)
		{
			// full address found
			return $address;
		}

		// check if we have a street name by looking at different slots
		$street = $this->getComponent(['route', 'street', 'street_name']);

		if (!$street)
		{
			// unable to detect an address
			return null;
		}

		// check if we have a street number by looking at different slots
		$number = $this->getComponent(['number', 'street_number', 'premise']);

		if ($number)
		{
			$street .= ' ' . $number; 
		}

		return $street;
	}

	/**
	 * Obtains the city name.
	 * 
	 * @return  string|null
	 */
	public function getCity()
	{
		return $this->getComponent(['city', 'locality']);
	}

	/**
	 * Obtains the ZIP/POSTAL Code.
	 * 
	 * @return  string|null
	 */
	public function getZipCode()
	{
		return $this->getComponent(['zip', 'zipcode', 'zip_code', 'postcode', 'post_code', 'postal_code']);
	}

	/**
	 * Obtains a specified component from the search query.
	 * 
	 * @param   array|string  $components  Either a component name or a list.
	 * 
	 * @return  mixed
	 */
	public function getComponent($components)
	{
		// scan all the possible slots
		foreach ((array) $components as $component)
		{
			// check whether the component exists
			if (isset($this->components[$component]))
			{
				// component found
				return $this->components[$component];
			}
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return $this->components;
	}
}
