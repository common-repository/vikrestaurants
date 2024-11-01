<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\OrderDishes;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Used to handle the restaurant dish into the cart.
 *
 * @since 1.8
 * @since 1.9  Renamed from VREDishesItem.
 */
class Item
{
	/**
	 * Registry containing the item details.
	 *
	 * @var \JObject
	 */
	protected $data;

	/**
	 * The ID of the record stored in the database.
	 *
	 * @var int
	 */
	protected $id_record = 0;

	/**
	 * The ID of the selected variation.
	 *
	 * @var int
	 */
	protected $id_option = 0;

	/**
	 * A list of supported variations.
	 *
	 * @var object[]
	 */
	protected $options = [];

	/**
	 * The total number of units.
	 *
	 * @var int
	 */
	protected $quantity = 1;

	/**
	 * Any additional notes required for the purchase.
	 *
	 * @var string
	 */
	protected $notes = '';

	/**
	 * The serving number
	 *
	 * @var int
	 * @since 1.9.1
	 */
	protected $servingNumber = 0;

	/**
	 * Holds the totals information before any changes.
	 * 
	 * @var object
	 * @since 1.9
	 */
	protected $currentTotals;

	/**
	 * Whether the item has been modified or not.
	 * 
	 * @var bool
	 * @since 1.9
	 */
	protected $modified = false;

	/**
	 * Class constructor.
	 *
	 * @param  int    $id         The ID of the item to load.
	 * @param  mixed  $id_option  The optional ID of the variation.
	 */
	public function __construct(int $id, int $id_option = null)
	{
		$dbo = \JFactory::getDbo();

		// load product details from database
		$q = $dbo->getQuery(true)
			->select('p.*')
			->select($dbo->qn('a.id', 'id_assoc'))
			->select($dbo->qn('a.charge'))
			->select($dbo->qn('o.id', 'option_id'))
			->select($dbo->qn('o.name', 'option_name'))
			->select($dbo->qn('o.inc_price', 'option_price'))
			->from($dbo->qn('#__vikrestaurants_section_product_assoc', 'a'))
			->leftjoin($dbo->qn('#__vikrestaurants_section_product', 'p') . ' ON ' . $dbo->qn('a.id_product') . ' = ' . $dbo->qn('p.id'))
			->leftjoin($dbo->qn('#__vikrestaurants_section_product_option', 'o') . ' ON ' . $dbo->qn('o.id_product') . ' = ' . $dbo->qn('p.id'))
			->where($dbo->qn('a.id') . ' = ' . $id)
			->order($dbo->qn('o.ordering') . ' ASC');

		$dbo->setQuery($q);
		$rows = $dbo->loadObjectList();

		if (!$rows)
		{
			// item not found, throw exception
			throw new \Exception(\JText::translate('VRTKCARTROWNOTFOUND'), 404);
		}

		// set up internal details
		$this->data = new \JObject($rows[0]);

		// set up supported variations
		foreach ($rows as $opt)
		{
			if ($opt->option_id)
			{
				$tmp = new \stdClass;
				$tmp->id    = $opt->option_id;
				$tmp->name  = $opt->option_name;
				$tmp->price = (float) $opt->option_price;

				$this->options[$opt->option_id] = $tmp;
			}
		}

		// check if we have at least a variation
		if ($this->options)
		{
			if (!$id_option)
			{
				// use first variation available
				$this->id_option = $this->data->get('option_id');
			}
			else
			{
				// set specified variation
				$this->id_option = $id_option;
			}

			// validate selected variation
			if (!isset($this->options[$this->id_option]))
			{
				// variation not found, throw exception
				throw new \Exception(\JText::translate('VRTKCARTROWNOTFOUND'), 404);
			}
		}

		// register the current totals before any changes
		$this->currentTotals = $this->getTotals();

		// always reset changes during construction
		$this->modified(false);
	}

	/**
	 * Returns the ID used to keep the record stored.
	 *
	 * @return  int
	 */
	public function getRecordID()
	{
		return $this->id_record;
	}

	/**
	 * Sets the ID used to keep the record stored.
	 *
	 * @param   int   $id  The record ID.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setRecordID(int $id)
	{
		$this->id_record = $id;

		return $this;
	}

	/**
	 * Returns the item base name.
	 *
	 * @return  string  The item name.
	 */
	public function getName()
	{
		// get base name
		$name = $this->name;

		$translator = \VREFactory::getTranslator();

		// get item translation
		$tx = $translator->translate('menusproduct', $this->id);

		if ($tx)
		{
			// overwrite original name with translation
			$name = $tx->name;
		}

		return $name;
	}

	/**
	 * Returns the item description.
	 *
	 * @return  string  The item description.
	 */
	public function getDescription()
	{
		// get base description
		$description = $this->description;

		$translator = \VREFactory::getTranslator();

		// get item translation
		$tx = $translator->translate('menusproduct', $this->id);

		if ($tx)
		{
			// overwrite original description with translation
			$description = $tx->description;
		}

		return $description;
	}
	
	/**
	 * Returns the variation of the item, if any.
	 *
	 * @return  mixed  The item variation if exists, null otherwise.
	 */
	public function getVariation()
	{
		if ($this->id_option)
		{
			// get current variation
			$var = $this->options[$this->id_option];

			// variation set, translate name and return it
			$translator = \VREFactory::getTranslator();
			// get option translation
			$tx = $translator->translate('productoption', $this->id_option);

			if ($tx)
			{
				// overwrite original name with translation
				$var->name = $tx->name;
			}

			return $var;
		}

		// return null
		return null;
	}

	/**
	 * Selects the specified variation, if exists.
	 *
	 * @param   int   $id  The variation ID.
	 *
	 * @return  self  This object to supporting chaining.
	 */
	public function setVariation(int $id)
	{
		if (isset($this->options[$id]))
		{
			// existing option, select it
			$this->id_option = $id;

			// something has changed
			$this->modified();
		}

		return $this;
	}

	/**
	 * Returns a list of variations supported by the item.
	 *
	 * @return  object[]
	 */
	public function getVariations()
	{
		$translator = \VREFactory::getTranslator();

		// get current language tag
		$lang = \JFactory::getLanguage()->getTag();

		// preload translations
		$data = $translator->load('productoption', array_keys($this->options), $lang);

		// get variations
		$variations = $this->options;

		foreach ($variations as $var)
		{
			// check if we have a translation
			$tx = $data->getTranslation($var->id, $lang);

			if ($tx)
			{
				// overwrite original name with translation
				$var->name = $tx->name;
			}
		}

		return $variations;
	}

	/**
	 * Returns the full name of the item.
	 * Concatenates the item name and the variation name,
	 * separated by the given string.
	 *
	 * @param   string  $separator  The separator string between the names.
	 *
	 * @return  string  The item full name.
	 */
	public function getFullName(string $separator = null)
	{
		if (!$separator)
		{
			$separator = ' - ';
		}

		// get item base name
		$name = $this->getName();

		// get variation
		$var = $this->getVariation();

		if ($var)
		{
			// concatenate separator and variation
			$name .= $separator . $var->name;
		}

		return $name;
	}
	
	/**
	 * Returns the base price of the item.
	 * 
	 * @param   bool   $var  Whether the variation price should be included or not.
	 *
	 * @return  float  The item base price.
	 */
	public function getPrice($var = true)
	{
		$price = (float) $this->price + (float) $this->charge;

		if ($var && ($opt = $this->getVariation()))
		{
			// increase by the variation price
			$price += $opt->price;
		}

		return $price;
	}

	/**
	 * Returns the total cost of the item, variation included.
	 *
	 * @return  float  The real item total cost.
	 */
	public function getTotalCost()
	{
		// multiply total cost by the selected quantity
		return $this->getPrice() * $this->getQuantity();
	}

	/**
	 * Returns the total costs (net, tax, gross) of the item.
	 *
	 * @return  object
	 * 
	 * @since   1.9
	 */
	public function getTotals()
	{
		$total = $this->getTotalCost();

		// calculate taxes
		return \E4J\VikRestaurants\Taxing\TaxesFactory::calculate($this->id_tax, $total, [
			// Do not specify the subject, otherwise the provided TAX ID will be considered
			// as the ID of the product for which the taxes should be fetched.
			// 'subject' => 'restaurant.menusproduct',
			// The user ID should refer to the customers table.
			// 'id_user' => \JFactory::getUser()->id,
		]);
	}

	/**
	 * Returns the total costs (net, tax, gross) of the item before any changes.
	 *
	 * @return  object
	 * 
	 * @since   1.9
	 */
	public function getCurrentTotals()
	{
		return $this->currentTotals;
	}
	
	/**
	 * Get the quantity of the item.
	 *
	 * @return  int  The item quantity.
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}
	
	/**
	 * Sets the quantity of the item.
	 *
	 * @param   int   The item quantity.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setQuantity(int $units)
	{
		$this->quantity = max(0, abs($units));

		// something has changed
		$this->modified();

		return $this;
	}
	
	/**
	 * Increase the quantity of the item by the specified units.
	 *
	 * @param   int   $units  The units to add.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function add(int $units = 1)
	{
		$this->quantity += $units;

		// something has changed
		$this->modified();

		return $this;
	}
	
	/**
	 * Decrease the quantity of the item by the specified units.
	 *
	 * @param   int  $units  The units to remove.
	 *
	 * @return  int  The remaining units.
	 */
	public function remove(int $units = 1)
	{
		$this->quantity -= $units;

		if ($this->quantity < 0)
		{
			$this->quantity = 0;
		}

		// something has changed
		$this->modified();

		return $this->quantity;
	}

	/**
	 * Get the serving number of the item.
	 *
	 * @return  int  The serving number.
	 */
	public function getServingNumber()
	{
		return $this->servingNumber;
	}

	/**
	 * Sets the serving number for this item.
	 * 
	 * @param   int   $number A value between 0 and 2.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function setServingNumber(int $number)
	{
		$number = max(0, abs($number));
		$number = min(2, $number);

		$this->servingNumber = $number;

		return $this;
	}
	
	/**
	 * Get the additional notes of the item.
	 *
	 * @return  string  The item additional notes.
	 */
	public function getAdditionalNotes()
	{
		return $this->notes;
	}
	
	/**
	 * Set the additional notes of the item.
	 *
	 * @param   string  The item additional notes.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function setAdditionalNotes($notes)
	{
		$this->notes = $notes;

		// something has changed
		$this->modified();

		return $this;
	}

	/**
	 * Checks whether the specified record has been modified.
	 *
	 * @return  bool
	 */
	public function isModified()
	{
		return $this->modified;
	}

	/**
	 * Updates the modified status of the item.
	 *
	 * @param   bool  $status  The status to set (true by default).
	 *
	 * @return  self  This object to support chaining.
	 */
	public function modified(bool $status = true)
	{
		$this->modified = $status;

		return $this;
	}

	/**
	 * Checks whether the specified record can still be modified or deleted.
	 *
	 * @return  bool
	 */
	public function isWritable()
	{
		// check if we have a session item
		if (!$this->getRecordID())
		{
			// session items are always writable
			return true;
		}

		/**
		 * Then look for the global permissions.
		 *
		 * @since 1.8.3
		 */
		return \VREFactory::getConfig()->getBool('editfood');
	}
	
	/**
	 * Check if this object is equal to the specified item.
	 * Two items are equal if they have the same ID, the same variation ID,
	 * and the same additional notes.
	 *
	 * @param   Item  $item  The item to check.
	 *
	 * @return  bool  True if the 2 objects are equal, otherwise false.
	 */
	public function equalsTo(Item $item)
	{
		// compare items
		return $this->getRecordID() == $item->getRecordID()
			&& $this->id == $item->id 
			&& $this->id_option == $item->id_option
			&& $this->getAdditionalNotes() == $item->getAdditionalNotes()
			&& $this->getServingNumber() == $item->getServingNumber();
	}

	/**
	 * @inheritDoc
	 */
	public function __get($name)
	{
		if ($name === 'id_option')
		{
			// return selected variation
			return (int) $this->id_option;
		}

		return $this->data->get($name, null);
	}
	
	/**
	 * @inheritDoc
	 */
	public function __toString()
	{
		return '<pre>' . print_r($this, true) . '</pre>';
	}
}
