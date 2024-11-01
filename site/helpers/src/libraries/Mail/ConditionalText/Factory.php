<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\DI\Container;
use E4J\VikRestaurants\DI\ContainerDecorator;

/**
 * Factory class used to easily instantiate conditional text filters and actions.
 *
 * @since 1.9
 */
class Factory
{
	/**
	 * The object holding all the registered conditional text filters and actions.
	 * 
	 * @var Container
	 */
	private $container;

	/**
	 * The global instance of the conditional texts factory.
	 *
	 * @var Factory
	 */
	protected static $instance = null;

	/**
	 * Accesses the global conditional texts factory class.
	 * 
	 * @return  Factory
	 */
	public static function getInstance()
	{
		if (static::$instance === null)
		{
			$containerDecorator = new ContainerDecorator;
				
			// auto-register all the default conditional text filters
			$containerDecorator->register(VREHELPERS . '/src/libraries/Mail/ConditionalText/Filters', [
				'template'  => 'filter.{id}',
				'suffix'    => 'Filter',
				'namespace' => 'E4J\\VikRestaurants\\Mail\\ConditionalText\\Filters',
			]);

			// auto-register all the default conditional text actions
			$containerDecorator->register(VREHELPERS . '/src/libraries/Mail/ConditionalText/Actions', [
				'template'  => 'action.{id}',
				'suffix'    => 'Action',
				'namespace' => 'E4J\\VikRestaurants\\Mail\\ConditionalText\\Actions',
			]);
			
			// instantiate conditional text factory
			static::$instance = new static($containerDecorator->getContainer());

			/**
			 * Trigger event to let the plugins register new conditional text actions and filters.
			 * Here's described how:
			 * 
			 * $factory->registerFilterProvider('custom', function(array $options)
			 * {
			 *     require_once JPath::clean(dirname(__FILE__) . '/filters/CustomFilter.php');
			 *     return new CustomFilter($options);
			 * });
			 *
			 * @param  	E4J\VikRestaurants\Mail\ConditionalText\Factory  $factory
			 *
			 * @return 	void
			 *
			 * @since   1.9
			 */
			\VREFactory::getPlatform()->getDispatcher()->trigger('onSetupConditionalTexts', [static::$instance]);
		}

		return static::$instance;
	}

	/**
	 * Class constructor.
	 * 
	 * @param  Container  $container  The container holding the registered conditional text providers.
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Returns the container holding all the registered conditional text actions and filters.
	 * 
	 * @return  Container
	 */
	final public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Registers a new filter provider for lazy initialization.
	 * 
	 * @param   string    $filter    The filter name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerFilterProvider(string $filter, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set('filter.' . $filter, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Registers a new action provider for lazy initialization.
	 * 
	 * @param   string    $action    The action name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerActionProvider(string $action, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set('action.' . $action, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Returns the filter registered with the specified ID.
	 * 
	 * @param   string  $filter   The filter identifier.
	 * @param   mixed   $options  The filter configuration.
	 * 
	 * @return  ConditionalTextFilter
	 * 
	 * @throws  \Exception
	 */
	public function getFilter(string $filter, $options = [])
	{
		if (!$this->container->has('filter.' . $filter))
		{
			// filter not found
			throw new \Exception(sprintf('Conditional text filter [%s] not found', $filter), 404);
		}

		/** @var ConditionalTextFilter */
		$filterResource = $this->container->get(
			// filter ID
			'filter.' . $filter,
			// filter constructor arguments
			$options
		);

		// make sure we have a valid filter instance
		if (!$filterResource instanceof ConditionalTextFilter)
		{
			// invalid filter
			throw new \UnexpectedValueException(sprintf('Conditional text filter [%s] is not valid', $filter), 406);
		}

		return $filterResource;
	}

	/**
	 * Returns the action registered with the specified ID.
	 * 
	 * @param   string  $action   The action identifier.
	 * @param   mixed   $options  The action configuration.
	 * 
	 * @return  ConditionalTextAction
	 * 
	 * @throws  \Exception
	 */
	public function getAction(string $action, $options = [])
	{
		if (!$this->container->has('action.' . $action))
		{
			// action not found
			throw new \Exception(sprintf('Conditional text action [%s] not found', $action), 404);
		}

		/** @var ConditionalTextAction */
		$actionResource = $this->container->get(
			// action ID
			'action.' . $action,
			// action constructor arguments
			$options
		);

		// make sure we have a valid action instance
		if (!$actionResource instanceof ConditionalTextAction)
		{
			// invalid action
			throw new \UnexpectedValueException(sprintf('Conditional text action [%s] is not valid', $action), 406);
		}

		return $actionResource;
	}

	/**
	 * Returns a list containing all the supported filters.
	 *
	 * @return 	ConditionalTextFilter[]
	 */
	public function getSupportedFilters()
	{
		$filters = [];
		
		foreach ($this->container->keys() as $id)
		{
			if (strpos($id, 'filter.') !== 0)
			{
				// we are not observing a filter
				continue;
			}

			// get rid of the prefix
			$id = substr($id, 7);

			try
			{
				/** @var ConditionalTextFilter */
				$filter = $this->getFilter($id);

				if (!$filter instanceof ConditionalTextManageable)
				{
					// only manageable filters can be registered here
					continue;
				}

				// register filter within the map
				$filters[$id] = $filter;
			}
			catch (\Exception $e)
			{
				// ignore conditional text filter
			}
		}

		// sort conditional text filters by name
		uasort($filters, function($a, $b)
		{
			return strcmp($a->getName(), $b->getName());
		});

		return $filters;
	}

	/**
	 * Returns a list containing all the supported actions.
	 *
	 * @return 	ConditionalTextAction[]
	 */
	public function getSupportedActions()
	{
		$actions = [];
		
		foreach ($this->container->keys() as $id)
		{
			if (strpos($id, 'action.') !== 0)
			{
				// we are not observing a action
				continue;
			}

			// get rid of the prefix
			$id = substr($id, 7);

			try
			{
				/** @var ConditionalTextAction */
				$action = $this->getAction($id);

				if (!$action instanceof ConditionalTextManageable)
				{
					// only manageable actions can be registered here
					continue;
				}

				// register action within the map
				$actions[$id] = $action;
			}
			catch (\Exception $e)
			{
				// ignore conditional text action
			}
		}

		// sort conditional text actions by name
		uasort($actions, function($a, $b)
		{
			return strcmp($a->getName(), $b->getName());
		});

		return $actions;
	}	
}
