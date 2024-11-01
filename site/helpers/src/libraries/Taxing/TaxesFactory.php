<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Taxing;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Taxing\Helpers\TaxesHelper;

/**
 * Taxes factory class.
 *
 * @since 1.9
 */
abstract class TaxesFactory
{
	/**
	 * Lookup used to store the TAX id of the specified subjects.
	 *
	 * @var array
	 */
	protected static $lookup = [];

	/**
	 * Calculates the taxes of the specified amount according
	 * to the rules of the given tax ID.
	 *
	 * @param   integer  $id       The tax ID.
	 * @param   float    $total    The total amount to check.
	 * @param   array    $options  An array of options.
	 *
	 * @return  mixed    An object containing the resulting taxes.
	 */
	public static function calculate($id, $total, array $options = [])
	{
		// create fake tax instance to handle prices without a
		// specified tax rule or to speed up the process in case
		// of free/negative amounts
		$tax = new Tax;
		
		$default = $tax->calculate($total, $options);

		if ($total <= 0)
		{
			// immediately return default object
			return $default;
		}

		// calculate costs
		$amount = static::_calculate($id, $total, $options);

		if ($amount === null)
		{
			// tax not found, return default object
			return $default;
		}

		return $amount;
	}

	/**
	 * Calculates the taxes of the specified amount according
	 * to the rules of the given tax ID.
	 *
	 * @param   integer  $id       The tax ID.
	 * @param   float    $total    The total amount to check.
	 * @param   array    $options  An array of options.
	 *
	 * @return  mixed    An object containing the resulting taxes on success,
	 *                   null in case the tax doesn't exist.
	 */
	protected static function _calculate($id, $total, array $options = [])
	{
		// obtain tax object
		$tax = static::getTaxObject($id, $options);

		if (!$tax)
		{
			// unable to detect tax handler
			return null;
		}

		// calculate taxes
		return $tax->calculate($total, $options);
	}

	/**
	 * Obtains the object able to calculate the taxes for the specified element.
	 * 
	 * @param   integer  $id       The tax ID.
	 * @param   array    $options  An array of options.
	 * 
	 * @return  Tax|null
	 */
	public static function getTaxObject($id, array $options = [])
	{
		// check whether the specified ID is referring to
		// a specific database table
		if (!empty($options['subject']))
		{
			// find tax ID of given subject
			$id = TaxesHelper::getTaxOf($id, $options['subject']);
		}

		/**
		 * Trigger hook to allow external plugins to switch tax ID at
		 * runtime, which may vary according to the specified options.
		 *
		 * @param   integer  $id       The current tax ID.
		 * @param   array    $options  An array of options.
		 *
		 * @return  mixed    The new ID of the tax to apply. Return false to ignore
		 *                   the taxes calculation.
		 *
		 * @since   1.9
		 */
		$result = \VREFactory::getPlatform()->getDispatcher()->filter('onBeforeUseTax', [$id, $options]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		if ($result->has())
		{
			if ($result->isFalse())
			{
				// do not calculate, as instructed by a third-party plugin
				return null;
			}

			// a plugin manipulated the ID, overwrite the default one
			$id = (int) $result[0];
		}

		if (!$id)
		{
			if (!\VikRestaurants::isRestaurantEnabled() || preg_match("/^takeaway\./", (string) ($options['subject'] ?? '')))
			{
				// get global tax (for the take-away)
				$id = \VREFactory::getConfig()->getUint('tkdeftax', 0);
			}
			else
			{
				// get global tax
				$id = \VREFactory::getConfig()->getUint('deftax', 0);
			}
		}

		if (!$id)
		{
			// no existing taxes
			return null;
		}

		try
		{
			// check whether the caller requested a specific language
			// for translating the details of the taxes
			$lang = isset($options['lang']) ? $options['lang'] : null;

			// create new container
			$container = new TaxesContainer(\JFactory::getDbo());
			// make sure we are caching the fetched taxes
			$container = new TaxesContainerCache($container);

			// obtain tax handler and translate it
			$tax = (new TaxesTranslator($container->get($id)))->translate($lang);
		}
		catch (\Exception $e)
		{
			// tax not found, abort
			return null;
		}

		return $tax;
	}

	/**
	 * Returns a list of supported math operators.
	 *
	 * @return  TaxRule[]
	 */
	public static function getMathOperators()
	{
		$op = [];

		// load default drivers
		$default = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Rules' . DIRECTORY_SEPARATOR . '*.php');

		foreach ($default as $file)
		{
			// get name of the file without suffix
			$name = preg_replace("/Rule\.php$/i", '', basename($file));

			// register operator
			$op[$name] = \JText::translate('VRETAXMATHOP_' . strtoupper($name));
		}

		/**
		 * Trigger hook to allow external plugins to support custom operations.
		 * New operations have to be appended to the given associative array.
		 * The key of the array is the unique ID of the operation, the value is
		 * a readable name to display.
		 *
		 * @param   array  &$operators  An array of operations.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadTaxOperators', [&$op]);

		// sort by ascending name and preserve keys
		asort($op);

		return $op;
	}
}
