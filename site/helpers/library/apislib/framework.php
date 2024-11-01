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

/**
 * VikRestaurants APIs base framework.
 * This class is used to run all the installed plugins in a given directory.
 * The classname of the plugins must follow the standard below:
 * e.g. File = plugin.php   		Class = Plugin
 * e.g. File = plugin_name.php   	Class = PluginName
 *
 * All the events are runnable only if the user is correctly authenticated.
 *
 * @since 1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\Framework\API instead.
 */
class FrameworkAPIs extends E4J\VikRestaurants\API\Framework\API
{
	/**
	 * The path of the folder containing all the available plugins.
	 *
	 * @var   array
	 * @since 1.8.2
	 */
	private $includePaths = [];

	/**
	 * The instance of the API framework.
	 *
	 * @var APIs
	 */
	protected static $instance = null;

	/**
	 * Class constructor.
	 * @protected This class can be accessed only through the static getInstance() method.
	 *
	 * @param   string  $path  The dir path containing all the plugins.
	 *
	 * @see     APIs::getInstance()
	 */
	protected function __construct($path = null)
	{
		// register default API plugins
		$containerDecorator = (new E4J\VikRestaurants\DI\ContainerDecorator)
			->register(VREHELPERS . '/src/libraries/API/Plugins', [
				'suffix'    => 'PluginAPI',
				'namespace' => 'E4J\\VikRestaurants\\API\\Plugins',
			]);

		// construct with default resources
		parent::__construct(
			$containerDecorator->getContainer(),
			new E4J\VikRestaurants\API\Framework\MaxAttemptsBanner(
				\VREFactory::getConfig()->getUint('apimaxfail', 10)
			)
		);

		if (empty($path))
		{
			// use default folder if not specified
			$path = [
				dirname(__FILE__) . '/apis/plugins',
			];
		}

		// set include paths
		$this->setIncludePaths($path);

		/**
		 * Register an alias for the refactored plugins for BC.
		 * 
		 * @deprecated 1.11  Use the default event names instead.
		 */
		$this->getContainer()
			->alias('connection_ping', 'connectionping')
			->alias('get_orders_list', 'orderslist')
			->alias('get_order_details', 'orderdetails')
			->alias('pull_orders', 'orderspull')
			->alias('book_table', 'booktable')
			->alias('ics_sync', 'syncics')
			->alias('table_available', 'tableavailable');
	}

	/**
	 * Get the instance of the APIs object.
	 * 
	 * @param   string  $path   The dir path containing all the plugins.
	 *
	 * @return  APIs    The instance of the API framework.
	 */
	public static function getInstance($path = null)
	{
		if (static::$instance === null)
		{
			static::$instance = new static($path);
		}

		return static::$instance;
	}

	/**
	 * @inheritDoc
	 */
	protected function registerEvent(E4J\VikRestaurants\API\Event $event = null, E4J\VikRestaurants\API\Response $response = null)
	{
		if ($response && $event instanceof EventAPIs)
		{
			// register deprecation log
			$response->appendContent("<small style=\"color:#900;font-style:italic;\">This event implements a deprecated interface. You should update your integration before the release of the 1.11 version of VikRestaurants.</small>");
		}

		return parent::registerEvent($event, $response);
	}

	/**
	 * Get the path of the specified event.
	 *
	 * @return  mixed   The event path if exists, false otherwise.
	 */
	public function getEventPath($event = null)
	{
		// get all include paths
		$paths = $this->getIncludePaths();

		if (is_null($event))
		{
			// method scope has changed
			trigger_error(sprintf('%s() requires the event name', __METHOD__), E_USER_NOTICE);

			// return path for BC
			return array_shift($paths);
		}

		// trim trailing .php from event name
		$event = preg_replace("/\.php$/i", '', $event);
		
		// iterate supported paths
		foreach ($paths as $path)
		{
			// build event path
			$tmp = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $event . '.php';

			// make sure the file exists
			if (is_file($tmp))
			{
				return $tmp;
			}
		}

		return false;
	}

	/**
	 * Gets a list of supported include paths.
	 *
	 * @return  array
	 *
	 * @since   1.8.2
	 */
	public function getIncludePaths()
	{
		return $this->includePaths;
	}

	/**
	 * Sets the include paths to search for plugins.
	 *
	 * @param   array   $paths  Array with paths to search in.
	 *
	 * @return  self    This object to support chaining.
	 *
	 * @since   1.8.2
	 */
	public function setIncludePaths($paths)
	{
		$this->includePaths = [];

		return $this->addIncludePaths($paths);
	}

	/**
	 * Adds one path to include in plugin search.
	 * Proxy of addIncludePaths().
	 *
	 * @param   string  $path  The path to search for plugins.
	 *
	 * @return  self    This object to support chaining.
	 *
	 * @since   1.8.2
	 */
	public function addIncludePath($path)
	{
		return $this->addIncludePaths($path);
	}

	/**
	 * Adds one or more paths to include in plugin search.
	 *
	 * @param   mixed  $paths  The path or array of paths to search for plugins.
	 *
	 * @return  self   This object to support chaining.
	 *
	 * @since   1.8.2
	 */
	public function addIncludePaths($paths)
	{
		if (empty($paths))
		{
			return $this;
		}

		$includePaths = $this->getIncludePaths();

		$containerDecorator = new E4J\VikRestaurants\DI\ContainerDecorator($this->getContainer());

		foreach ((array) $paths as $path)
		{
			$path = JPath::clean($path);

			if (in_array($path, $includePaths))
			{
				// path already registered
				continue;
			}

			// register provided path
			$includePaths[] = $path;

			// load all the plugins under the provided folder
			$containerDecorator->register($path, [
				'autoload'  => false,
				'protected' => true,
				'callback'  => function($classname) {
					return str_replace(' ', '', ucwords(str_replace('_', ' ', $classname)));
				},
			]);
		}

		// update include paths
		$this->includePaths = $includePaths;

		return $this;
	}

	/**
	 * @inheritDoc
	 * 
	 * @param   string  $plg_name  The name of the plugin to get.
	 *                             If not specified it will be replaced by "*" (all plugins).
	 */
	public function getPluginsList($plg_name = '')
	{
		if ($plg_name && $plg_name !== '*')
		{
			// load only the requested plugin and wrap it in an array for BC
			return [$this->getPlugin($plg_name)];
		}

		// load all the plugins
		return parent::getPluginsList();
	}
}
