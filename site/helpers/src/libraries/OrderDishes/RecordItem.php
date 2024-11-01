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
 * Used to handle the restaurant dish into the database.
 *
 * @since 1.8
 * @since 1.9  Renamed from VREDishesRecord.
 */
class RecordItem extends Item
{
	/**
	 * Overloaded item price.
	 *
	 * @var float
	 */
	protected $price;

	/**
	 * Class constructor.
	 *
	 * @param  int  $id  The database record ID.
	 */
	public function __construct(int $id)
	{
		$dbo = \JFactory::getDbo();

		// load product details from database
		$q = $dbo->getQuery(true)
			->select('i.*')
			->select($dbo->qn('a.id', 'id_assoc'))
			->from($dbo->qn('#__vikrestaurants_res_prod_assoc', 'i'))
			->leftjoin($dbo->qn('#__vikrestaurants_section_product_assoc', 'a') . ' ON ' . $dbo->qn('a.id_product') . ' = ' . $dbo->qn('i.id_product'))
			->where($dbo->qn('i.id') . ' = ' . $id);

		$dbo->setQuery($q, 0, 1);
		$dbo->execute();

		$item = $dbo->loadObject();

		if (!$item)
		{
			// item not found, throw exception
			throw new \Exception(\JText::translate('VRTKCARTROWNOTFOUND'), 404);
		}

		// instantiate product by using parent
		parent::__construct($item->id_assoc, $item->id_product_option);

		// set record ID
		$this->setRecordID($item->id);
		// set specified notes
		$this->setAdditionalNotes($item->notes);
		// set quantity
		$this->setQuantity($item->quantity);

		// register price stored in the database
		$this->price = $item->price;

		// register the current totals before any changes
		$this->currentTotals = $this->getTotals();

		// always reset changes during construction
		$this->modified(false);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPrice($var = true)
	{
		$price = (float) $this->price;

		if (!$var && ($opt = $this->getVariation()))
		{
			// increase by the variation price
			$price -= $opt->price;
		}

		return $price;
	}

	/**
	 * @inheritDoc
	 */
	public function isWritable()
	{
		/**
		 * Look for the global permissions first.
		 *
		 * @since 1.8.3
		 */
		if (!\VREFactory::getConfig()->getBool('editfood'))
		{
			// cannot edit dishes after transmit
			return false;
		}

		$dbo = \JFactory::getDbo();

		// do not recover "preparing" flag from internal
		// properties because it might have been cached
		// within the session
		$q = $dbo->getQuery(true)
			->select($dbo->qn('status'))
			->from($dbo->qn('#__vikrestaurants_res_prod_assoc'))
			->where($dbo->qn('id') . ' = ' . $this->getRecordID());

		$dbo->setQuery($q, 0, 1);

		// dish can be edited only if it is not yet under
		// preparation or it hasn't been yet transmitted
		return $dbo->loadResult() === null;
	}

	/**
	 * @inheritDoc
	 */
	public function remove(int $units = 1)
	{
		// invoke parent to remove specified units
		parent::remove($units);

		if ($this->quantity == 0)
		{
			// get reservation-product model
			$model = \JModelVRE::getInstance('resprod');

			// permanently delete record from database
			$model->delete($this->getRecordID());
		}

		return $this->quantity;
	}

	/**
	 * @inheritDoc
	 */
	public function setVariation(int $id)
	{
		// set price without variation cost
		$this->price = $this->getPrice(false);

		parent::setVariation($id);

		if ($opt = $this->getVariation())
		{
			// re-add variation price
			$this->price += $opt->price;
		}

		return $this;
	}
}
