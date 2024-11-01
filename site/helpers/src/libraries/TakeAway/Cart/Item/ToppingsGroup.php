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
 * Used to handle the take-away item group into the cart.
 * This class wraps a list of toppings.
 *
 * @since 1.7
 * @since 1.9  Renamed from TakeAwayItemGroup.
 */
class ToppingsGroup implements \IteratorAggregate
{	
	/**
	 * The ID of the group.
	 *
	 * @var int
	 */
	protected $id;
	
	/**
	 * The title of the group.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * If has multiple choises or only one.
	 *
	 * @var bool
	 */
	protected $multiple;

	/**
	 * Flag used to check whether the toppings can be
	 * picked more than once.
	 *
	 * @var bool
	 * @since 1.8.2
	 */
	protected $useQuantity;
	
	/**
	 * The list of topping chosen.
	 *
	 * @var Topping[]
	 */
	protected $toppings = [];
	
	/**
	 * Class constructor.
	 *
	 * @param  array|object  $data  The toppings group data.
	 */
	public function __construct($data)
	{
		$data = (array) $data;

		$this->id          = (int) ($data['id'] ?? 0);
		$this->title       = (string) ($data['title'] ?? '');
		$this->multiple    = (bool) ($data['multiple'] ?? false);
		$this->useQuantity = (bool) ($data['quantity'] ?? false);
	}
	
	/**
	 * Get the ID of the group.
	 *
	 * @return  int  The group ID.
	 */
	public function getGroupID()
	{
		return $this->id;
	}
	
	/**
	 * Get the title of the group.
	 *
	 * @return  string  The group title.
	 */
	public function getTitle()
	{
		/**
		 * Try to translate the toppings group.
		 *
		 * @since 1.8.2
		 */
		$translator = \VREFactory::getTranslator();

		// translate topping group
		$tx = $translator->translate('tkentrygroup', $this->getGroupID());

		if ($tx)
		{
			// return the translation found
			return $tx->title;
		}

		// use default title
		return $this->title;
	}
	
	/**
	 * Check if the group is multiple: allow the selection of multiple toppings.
	 *
	 * @return  bool  True if multiple, otherwise false.
	 */
	public function isMultiple()
	{
		return $this->multiple;
	}

	/**
	 * Check if the group is single: allow the selection of only one topping.
	 *
	 * @return  bool  True if single, otherwise false.
	 */
	public function isSingle()
	{
		return !$this->isMultiple();
	}

	/**
	 * Checks whether the toppings can be picked multiple times.
	 *
	 * @return  bool  True in case the group allows the selection of the units
	 *                for the same topping (eg. salmon x2).
	 */
	public function useQuantity()
	{
		return $this->useQuantity;
	}
	
	/**
	 * Calculates the total cost of the group by summing the cost of each topping in the list.
	 *
	 * @return  float  The group total cost.
	 */
	public function getTotalCost()
	{
		$total = 0;

		foreach ($this->toppings as $topping)
		{
			$total += $topping->getRate();
		}

		return $total;
	}

	/**
	 * Returns the index of the specified topping.
	 *
	 * @param   Topping   $topping  The topping to search for.
	 *
	 * @return  int|bool  The index of the topping on success, otherwise false.
	 */
	public function indexOf(Topping $topping)
	{
		foreach ($this->toppings as $index => $t)
		{
			if ($t->equalsTo($topping))
			{
				return $index;
			}
		}

		return false;
	}

	/**
	 * Pushes the specified topping into the list.
	 * It is possible to push a topping only if it is not yet contained in the list.
	 *
	 * @param   Topping  $topping  The topping to insert.
	 *
	 * @return  bool     True on success, otherwise false.
	 */
	public function addTopping(Topping $topping)
	{
		$index = $this->indexOf($topping);

		if ($index === false || $index < 0)
		{
			$this->toppings[] = $topping;

			return true;
		}

		return false;
	}

	/**
	 * Resets the list by removing all the toppings.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function clear()
	{
		$this->toppings = [];

		return $this;
	}
	
	/**
	 * Returns the list containing all the toppings.
	 *
	 * @return  Topping[]  The list of toppings.
	 */
	public function getToppings()
	{
		return $this->toppings;
	}
	
	/**
	 * Check if this object is equal to the specified group.
	 * Two groups are equal if they have the same ID and the 
	 * toppings contained in both the lists are the same.
	 *
	 * @param   ToppingsGroup  $group  The group to check.
	 *
	 * @return  bool  True if the 2 objects are equal, otherwise false.
	 */
	public function equalsTo(ToppingsGroup $group)
	{
		if ($this->getGroupID() != $group->getGroupID())
		{
			// group ID is different
			return false;
		}

		$l1 = $this->getToppings();
		$l2 = $group->getToppings();

		if (count($l1) != count($l2))
		{
			// not the same number of toppings
			return false;
		}

		// repeat until the count is reached or there is a different topping.
		for ($i = 0; $i < count($l1); $i++)
		{
			$found = false;

			// repeat until the topping is found.
			for ($j = 0; $j < count($l2) && !$found; $j++)
			{
				// if true, break the statement
				$found = $l1[$i]->equalsTo($l2[$j]);
			}

			if (!$found)
			{
				// topping not found
				return false;
			}
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		return new \ArrayIterator($this->getToppings());
	}
}
