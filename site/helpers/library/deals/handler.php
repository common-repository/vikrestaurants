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

VRELoader::import('library.deals.rule');

/**
 * Used to handle the deals stored in the database.
 *
 * @since 1.6
 * @since 1.7  Renamed from VRDealsHandler
 * @deprecated 1.10  Use the classes provided by E4J\VikRestaurants\Deals instead.
 */
class DealsHandler
{
	/**
	 * Get all the available deals between the selected date.
	 * Target products and Gift products are not included.
	 * @usedby 	DealsHandler::getAvailableFullDeals()
	 *
	 * @param 	mixed    $cart 	Either the take-away cart instance or the check-in date.
	 * @param 	integer  $type 	The value of the type to filter deals. 
	 * 							Use 0 to skip type filtering.
	 *
	 * @return 	array 	 The list containing all the deals found.
	 * 
	 * @deprecated 1.10  Use E4J\VikRestaurants\Deals\DealHandler::getAvailableDeals() instead.
	 */
	public static function getAvailableDeals($cart, $type = 0)
	{
		if (is_scalar($cart) || !$cart)
		{
			// use specified timestamp
			$ts = (int) $cart;

			// recover cart instance
			VikRestaurants::loadCartLibrary();
			$cart = \TakeAwayCart::getInstance();
		}
		else
		{
			// obtain check-in date from cart
			$ts = $cart->getCheckinTimestamp();
		}

		if (is_numeric($type))
		{
			// adjust type for backward compatibility
			$lookup = [
				0 => '',
				1 => 'aboveall',
				2 => 'discountitem',
				3 => 'freecombination',
				4 => 'freetotal',
				5 => 'coupon',
				6 => 'discounttotal',
			];

			$type = $lookup[$type];
		}

		// fetch the available deals
		$deals = E4J\VikRestaurants\Deals\DealsCollection::getInstance()
		    ->filter(new E4J\VikRestaurants\Deals\Filters\PublishedFilter)
		    ->filter(new E4J\VikRestaurants\Deals\Filters\DateFilter($ts));

		if ($type)
		{
			// filter by type
		    $deals = $deals->filter(new E4J\VikRestaurants\Deals\Filters\RuleFilter($type));
		}

		// fetch all the active deals
		$compatibleDeals = $deals->filter(new E4J\VikRestaurants\Deals\Filters\WeekdayFilter($ts))
		    ->filter(new E4J\VikRestaurants\Deals\Filters\ShiftFilter($cart->getCheckinTime(true)))
		    ->filter(new E4J\VikRestaurants\Deals\Filters\ServiceFilter($cart->getService()));

		$list = [];

		foreach ($deals as $deal)
		{
			// preload working days
			$deal['days_filter'] = $deal->getDays();

			// check whether the current deal is contained within the list of active ones
			$deal['active'] = $compatibleDeals->filter(new E4J\VikRestaurants\Collection\Filters\NumberFilter('id', $deal['id'], '='))->count();

			if ($ts <= 0 || $deal['active'])
			{
				$list[] = $deal;
			}
		}

		// bring the active deals on top
		usort($list, function($a, $b)
		{
			return $b['active'] - $a['active'];
		});

		return $list;
	}
	
	/**
	 * Get all the available deals between the selected date.
	 * Target products and Gift products are included.
	 *
	 * @param 	mixed    $cart 	Either the take-away cart instance or the check-in date.
	 * @param 	integer  $type 	The value of the type to filter deals. 
	 * 							Use 0 to skip type filtering.
	 *
	 * @return 	array 	 The list containing all the deals found.
	 *
	 * @uses 	getAvailableDeals()
	 * 
	 * @deprecated 1.10  Use E4J\VikRestaurants\Deals\DealHandler::getAvailableDeals() instead.
	 */
	public static function getAvailableFullDeals($ts, $type = 0)
	{
		$list = [];

		// get available deals
		$deals = self::getAvailableDeals($ts, $type);
		
		if (!count($deals))
		{
			return $list;
		}

		foreach ($deals as $deal)
		{
			$asArray = true;

			$tmp = $deal->getProperties();

			// preload products and gifts as array
			$tmp['products'] = $deal->getProducts($asArray);
			$tmp['gifts']    = $deal->getGifts($asArray);

			// map old properties for backward compatibility
			foreach ($tmp['products'] as $k => $product)
			{
				$tmp['percentot']                = $product['params']->percentot ?? 1;
				$tmp['amount']                   = $product['params']->amount ?? 0;
				$tmp['products'][$k]['required'] = $product['params']->required ?? 0;
				$tmp['products'][$k]['quantity'] = $product['params']->units ?? 1;
			}

			// map old properties for backward compatibility
			foreach ($tmp['gifts'] as $k => $gifts)
			{
				$tmp['gifts'][$k]['quantity'] = $gifts['params']->units ?? 1;
			}

			$list[] = $tmp;
		}
		
		return $list;
	}

	/**
	 * This method sort the deals by pushing all the unactive items at the bottom.
	 *
	 * @param 	array 	$deals 	The list containing the deals to sort.
	 *
	 * @return 	array 	The deals sorted.
	 * 
	 * @deprecated 1.10  Without replacement.
	 */
	public static function reOrderActiveDeals(array $deals)
	{		
		return $deals;
	}
	
	/**
	 * Return the index of the deal which matches the specified parameters. 
	 * 
	 * @param 	array 	 $matches  The associative array containing all the keys to match.
	 * @param 	array 	 $deals    The array containing all the available deals. 
	 * 							   The deals should be retrieved with the method getAvailableFullDeals.
	 *
	 * @return 	integer  The index of the deal found, otherwise false.
	 * 
	 * @see 	getAvailableFullDeals()
	 * 
	 * @deprecated 1.10  Without replacement.
	 */
	public static function isProductInDeals(array $matches, $deals)
	{
		if (!$matches)
		{
			return false;
		}
		
		foreach ($deals as $index => $deal)
		{
			if ($deal instanceof E4J\VikRestaurants\Deals\Deal)
			{
				// preload products as array
				$products = $deal->getProducts(true);
			}
			else
			{
				$products = (array) $deal['products'];
			}

			foreach ($products as $prod)
			{
				if (isset($matches['id_product']) && $matches['id_product'] > 0 && $matches['id_product'] != $prod['id_product'])
				{
					// product not matching
					continue;
				}

				if (isset($matches['id_option']) && $matches['id_option'] > 0 && $prod['id_option'] > 0 && $matches['id_option'] != $prod['id_option'])
				{
					// variation not matching
					continue;
				}

				if (isset($matches['quantity']) && $matches['quantity'] > 0 && $matches['quantity'] != ($prod['params']->units ?? 1))
				{
					// quantity not matching
					continue;
				}

				// map old properties for backward compatibility
				if ($deal instanceof E4J\VikRestaurants\Deals\Deal)
				{
					$deal->set('percentot', $prod['params']->percentot ?? 1);
					$deal->set(   'amount',    $prod['params']->amount ?? 0);
				}
				
				return $index;
			}
		}
		
		return false;
	}

	/**
	 * Helper method used to extend the paths in which the rules
	 * should be found.
	 *
	 * @param 	mixed 	$path  The path to include (optional).
	 *
	 * @return 	array   A list of supported directories.
	 *
	 * @since 	1.8
	 * @deprecated 1.10  Without replacement.
	 */
	public static function addIncludePath($path = null)
	{
		static $paths = [];

		// include path if specified
		if ($path && is_dir($path))
		{
			$paths[] = $path;
		}

		// return list of included paths
		return $paths;
	}

	/**
	 * Returns a list of supported deals.
	 *
	 * @return 	array
	 *
	 * @since 	1.8
	 * @deprecated 1.10  Use E4J\VikRestaurants\Deals\DealsFactory::getSupportedDeals() instead.
	 */
	public static function getSupportedDeals()
	{
		static $drivers = null;

		// fetch drivers only once
		if (is_null($drivers))
		{
			// configuration array for deals
			$config = [];

			// create deals factory instance
			$factory = E4J\VikRestaurants\Deals\DealsFactory::getInstance();

			/**
			 * This event can be used to support custom deals.
			 * It is enough to include the directory containing
			 * the new rules. Only the files that inherits the
			 * DealRule class will be taken.
			 *
			 * Example:
			 * // register custom deal(s)
			 * DealsHandler::addIncludePath($path);
			 * // assign plugin configuration to deal
			 * $config['customdeal'] = $this->params;
			 *
			 * @param 	array  &$config  It is possible to inject the configuration for
			 * 							 a specific deal. The parameters have to be assigned
			 * 							 by using the deal file name.
			 *
			 * @return 	void
			 *
			 * @since 	1.8
			 * @deprecated 1.10  Use onSetupTakeawayDeals instead.
			 */
			VREFactory::getEventDispatcher()->trigger('onLoadSupportedDeals', [&$config]);

			// setup decorator
			$containerDecorator = new E4J\VikRestaurants\DI\ContainerDecorator($factory->getContainer());

			foreach (static::addIncludePath() as $path)
			{
				$path = JPath::clean($path);

				// load all the plugins under the provided folder
				$containerDecorator->register($path, [
					'autoload'  => false,
					'protected' => true,
					'callback'  => function($classname) use ($path) {
						// var_dump(require_once JPath::clean($path . '/test.php'));
						return 'DealRule' . ucfirst($classname);
					},
				]);
			}

			// fetch all the supported deals
			$drivers = $factory->getSupportedDeals();
		}

		return $drivers;
	}

	/**
	 * Preflight checks before checking for some deals.
	 *
	 * @param   TakeAwayCart  &$cart  The cart with the items.
	 *
	 * @return  void
	 *
	 * @since   1.8
	 * @deprecated 1.10  Use E4J\VikRestaurants\Deals\DealsHandler::setup() instead.
	 */
	public static function beforeApply(&$cart)
	{
		(new E4J\VikRestaurants\Deals\DealsHandler($cart))->setup();
	}

	/**
	 * Applies the specified deal to the cart.
	 *
	 * @param   TakeAwayCart  &$cart  The cart with the items.
	 * @param   array         $deal   The deal to apply.
	 *
	 * @return  bool          True if applied, false otherwise.
	 *
	 * @since   1.8
	 * @deprecated 1.10  Use E4J\VikRestaurants\Deals\DealsHandler::serve() instead.
	 */
	public static function apply(&$cart, $deal)
	{
		return (new E4J\VikRestaurants\Deals\DealsHandler($cart))->serve($deal);
	}
}
