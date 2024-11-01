<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\TakeAway\Cart\Item;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Used to handle the take-away item topping into the cart.
 *
 * @since 1.7
 * @since 1.9  Renamed from TakeAwayItemGroupTopping.
 */
class Topping
{	
	/**
	 * The ID of the topping.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * The Associative ID of the topping.
	 * This ID is needed to know the parent group.
	 *
	 * @var int
	 */
	protected $idAssoc;

	/**
	 * The name of the topping.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The cost of the topping.
	 *
	 * @var float
	 */
	protected $rate;

	/**
	 * The number of units of the topping.
	 *
	 * @var int
	 * @since 1.8.2
	 */
	protected $units;
	
	/**
	 * Class constructor.
	 *
	 * @param  array|object  $data  The topping data.
	 */
	public function __construct($data)
	{
		$data = (array) $data;

		$this->id      = (int) ($data['id'] ?? 0);
		$this->idAssoc = (int) ($data['id_assoc'] ?? 0);
		$this->name    = (string) ($data['name'] ?? '');
		$this->rate    = (float) ($data['rate'] ?? 0);
		$this->units   = (int) ($data['units'] ?? 1);
	}
	
	/**
	 * Get the ID of the topping.
	 *
	 * @return  int  The topping ID.
	 */
	public function getToppingID()
	{
		return $this->id;
	}

	/**
	 * Get the associative ID of the topping.
	 * This ID chain the topping to its parent group.
	 *
	 * @return  int  The topping assoc ID.
	 */
	public function getAssocID()
	{
		return $this->idAssoc;
	}
	
	/**
	 * Get the name of the topping.
	 *
	 * @return  string  The topping name.
	 */
	public function getName()
	{
		/**
		 * Try to translate the topping.
		 *
		 * @since 1.8.2
		 */
		$translator = \VREFactory::getTranslator();

		// translate topping
		$tx = $translator->translate('tktopping', $this->getToppingID());

		if ($tx)
		{
			// return the translation found
			return $tx->name;
		}

		// use default name
		return $this->name;
	}
	
	/**
	 * Get the cost of the topping.
	 *
	 * @return  float  The topping cost.
	 */
	public function getRate()
	{
		/**
		 * Multiply the cost of the topping by the
		 * number of selected units.
		 *
		 * @since 1.8.2
		 */
		return $this->rate * $this->getUnits();
	}

	/**
	 * Returns the number of picked units.
	 *
	 * @return  int  The topping units.
	 */
	public function getUnits()
	{
		return $this->units;
	}

	/**
	 * Sets the number of picked units.
	 *
	 * @param   int   $units  The units to set.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setUnits(int $units)
	{
		$this->units = max(1, $units);

		return $this;
	}

	/**
	 * Increases the number of picked units.
	 *
	 * @param   int   $units  The units to increase (1 by default).
	 *
	 * @return  self  This object to support chaining.
	 */
	public function addUnits(int $units = 1)
	{
		return $this->setUnits($this->units + $units);
	}

	/**
	 * Decreases the number of picked units.
	 *
	 * @param   int   $units  The units to decrease (1 by default).
	 *
	 * @return  self  This object to support chaining.
	 */
	public function removeUnits(int $units = 1)
	{
		return $this->setUnits($this->units - $units);
	}
	
	/**
	 * Checks if this object is equal to the specified topping.
	 * Two toppings are equal if they have the same ID.
	 *
	 * @param   Topping  $topping  The topping to check.
	 *
	 * @return  bool     True if the 2 objects are equal, otherwise false.
	 */
	public function equalsTo(Topping $topping)
	{
		return $this->getToppingID() == $topping->getToppingID();
	} 
}
