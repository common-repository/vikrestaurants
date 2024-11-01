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
 * Trait used to easily format the details of the taxes.
 * 
 * @since 1.9
 */
trait TaxFormatter
{
	/**
	 * Internal property used to cache the fetched details.
	 * 
	 * @var E4J\VikRestaurants\Taxing\TaxesContainer
	 */
	private $taxesContainer;

	/**
	 * Fetches the name of the tax with matching ID.
	 * 
	 * @param   int     $taxId  The ID of the tax to search.
	 * 
	 * @return  string  The name of the tax.
	 */
	public function toTaxName(int $taxId)
	{
		if ($taxId <= 0)
		{
			return '';
		}

		try
		{
			// fetch Tax name from ID
			return $this->getTaxesContainer()->get($taxId)->name;
		}
		catch (\Exception $e)
		{
			// entry not found
		}

		return '';
	}

	/**
	 * Returns the taxes container by creating it during the first access.
	 * 
	 * @return  E4J\VikRestaurants\Taxing\TaxesContainer
	 */
	private function getTaxesContainer()
	{
		if ($this->taxesContainer === null)
		{
			// create new container
			$this->taxesContainer = new \E4J\VikRestaurants\Taxing\TaxesContainer(\JFactory::getDbo());
			// make sure we are caching the fetched taxes
			$this->taxesContainer = new \E4J\VikRestaurants\Taxing\TaxesContainerCache($this->taxesContainer);
		}

		return $this->taxesContainer;
	}
}
