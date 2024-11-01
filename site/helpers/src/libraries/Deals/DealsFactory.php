<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Deals;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DI\Container;
use E4J\VikRestaurants\DI\ContainerDecorator;

/**
 * Used to create the available deals.
 *
 * @since 1.9
 */
class DealsFactory
{
	/**
	 * The object holding all the registered deal rules.
	 * 
	 * @var Container
	 */
	private $container;

	/**
	 * The global instance of the deals factory.
	 *
	 * @var DealsFactory
	 */
	protected static $instance = null;

	/**
	 * Accesses the global deals factory class.
	 * 
	 * @return  DealsFactory
	 */
	public static function getInstance()
	{
		if (static::$instance === null)
		{
			// auto-register all the default deals
			$containerDecorator = (new ContainerDecorator)
				->register(VREHELPERS . '/src/libraries/Deals/Rules', [
					'suffix'    => 'DealRule',
					'namespace' => 'E4J\\VikRestaurants\\Deals\\Rules',
				]);
			
			// instantiate deals factory
			static::$instance = new static($containerDecorator->getContainer());

			/**
			 * Trigger event to let the plugins register new deal rules.
			 * Here's described how:
			 * 
			 * $factory->registerDealProvider('custom_offer', function(array $options)
			 * {
			 *     require_once JPath::clean(dirname(__FILE__) . '/rules/CustomOfferDealRule.php');
			 *     return new CustomOfferDealRule($options);
			 * });
			 *
			 * @param  	E4J\VikRestaurants\Deals\DealsFactory  $factory
			 *
			 * @return 	void
			 *
			 * @since   1.9
			 */
			\VREFactory::getPlatform()->getDispatcher()->trigger('onSetupTakeawayDeals', [static::$instance]);
		}

		return static::$instance;
	}

	/**
	 * Class constructor.
	 * 
	 * @param  Container  $container  The container holding the registered deal providers.
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Returns the container holding all the registered deal rules.
	 * 
	 * @return  Container
	 */
	final public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Registers a new deal provider for lazy initialization.
	 * 
	 * @param   string    $rule      The deal rule name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the deal
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerDealProvider(string $rule, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set($rule, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Returns the deal registered with the specified rule ID.
	 * 
	 * @param   string    $rule  The deal rule identifier.
	 * @param   mixed     The deal configuration.
	 * 
	 * @return  DealRule  The deal rule instance.
	 * 
	 * @throws  \Exception
	 */
	public function getDeal(string $rule, $options = [])
	{
		if (!$this->container->has($rule))
		{
			// deal rule not found
			throw new \Exception(sprintf('Deal rule [%s] not found', $rule), 404);
		}

		/** @var DealRule */
		$deal = $this->container->get(
			// deal rule ID
			$rule,
			// deal rule constructor arguments
			$options
		);

		// make sure we have a valid deal rule instance
		if (!$deal instanceof DealRule)
		{
			// invalid deal rule
			throw new \UnexpectedValueException(sprintf('Deal rule [%s] is not valid', $rule), 406);
		}

		return $deal;
	}

	/**
	 * Returns a list containing all the registered deals.
	 * 
	 * @param   array       $options  An associative array containing the deals configuration.
	 *
	 * @return 	DealRule[]  A list of deal rules found.
	 */
	public function getSupportedDeals(array $options = [])
	{
		$rules = [];
		
		foreach ($this->container->keys() as $id)
		{
			try
			{
				if (isset($options[$id]))
				{
					// use the config provided for this deal
					$config = $options[$id];
				}
				else
				{
					// pass all the configuration
					$config = $options;
				}

				/** @var DealRule */
				$deal = $this->getDeal($id, $options);

				// register deal within the map
				$rules[$deal->getID()] = $deal;
			}
			catch (\Exception $e)
			{
				// ignore deal rule
			}
		}

		// sort deal rules by name
		uasort($rules, function($a, $b)
		{
			return strcmp($a->getName(), $b->getName());
		});

		return $rules;
	}
}
