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
 * VikRestaurants platform factory class.
 * @final 	This class cannot be extended.
 *
 * @since  	1.7
 * @since 	1.8.2 Renamed from UIFactory
 */
final class VREFactory
{
	/**
	 * The configuration handler of VikRestaurants.
	 *
	 * @var E4J\VikRestaurants\Config\AbstractConfiguration
	 */
	private static $config = null;

	/**
	 * The API Framework instance.
	 *
	 * @var E4J\VikRestaurants\API\API
	 */
	private static $api = null;

	/**
	 * Application event dispatcher.
	 *
	 * @var VREEventDispatcher
	 * @since 1.7.4
	 */
	private static $eventDispatcher = null;

	/**
	 * The currency object.
	 *
	 * @var E4J\VikRestaurants\Currency\Currency 
	 * @since 1.8
	 */
	private static $currency = null;

	/**
	 * The translator object.
	 *
	 * @var VRELanguageTranslator
	 * @since 1.8
	 */
	private static $translator = null;

	/**
	 * The wizard object.
	 *
	 * @var E4J\VikRestaurants\Wizard\Wizard
	 * @since 1.8.3
	 */
	private static $wizard = null;

	/**
	 * Application platform handler.
	 * 
	 * @var E4J\VikRestaurants\Platform\PlatformInterface
	 * @since 1.9
	 */
	private static $platform = null;

	/**
	 * Backup manager instance.
	 * 
	 * @var E4J\VikRestaurants\Backup\BackupManager
	 * @since 1.9
	 */
	private static $backupManager = null;

	/**
	 * Datasheet factory instance.
	 * 
	 * @var E4J\VikRestaurants\DataSheet\DataSheetFactory
	 * @since 1.9
	 */
	private static $dataSheetFactory = null;

	/**
	 * Coding hub instance.
	 * 
	 * @var E4J\VikRestaurants\CodeHub\CodeHub
	 * @since 1.9
	 */
	private static $codeHub = null;

	/**
	 * Class constructor.
	 */
	private function __construct()
	{
		// this class cannot be instantiated
	}

	/**
	 * Class cloner.
	 */
	private function __clone()
	{
		// cloning function not accessible
	}

	/**
	 * Instantiate a new configuration object.
	 *
	 * @return 	E4J\VikRestaurants\Config\AbstractConfiguration
	 */
	public static function getConfig()
	{
		if (self::$config === null)
		{
			// handle configuration with the database
			self::$config = new \E4J\VikRestaurants\Config\Pool\DatabaseConfiguration();
		}

		return self::$config;
	}

	/**
	 * Instantiate a new Framework API object.
	 *
	 * @return 	E4J\VikRestaurants\API\API
	 * 
	 * @since  1.7
	 * @since  1.9 Renamed from getApis().
	 */
	public static function getAPI()
	{
		if (self::$api === null)
		{
			/**
			 * Instantiate the API Framework.
			 * Still use the deprecated classes to keep supporting
			 * plugins built for the old framework.
			 * 
			 * @deprecated 1.11 Replace with the code below.
			 */
			VikRestaurants::loadFrameworkApis();
			self::$api = FrameworkAPIs::getInstance();

			// auto-register all the default API plugins
			// $containerDecorator = (new E4J\VikRestaurants\DI\ContainerDecorator)
			// 	->register(VREHELPERS . '/src/libraries/API/Plugins', [
			// 		'suffix'    => 'PluginAPI',
			// 		'namespace' => 'E4J\\VikRestaurants\\API\\Plugins',
			// 	]);
			// 
			// self::$api = new E4J\VikRestaurants\API\Framework\API(
			// 	$containerDecorator->getContainer(),
			// 	new E4J\VikRestaurants\API\Framework\MaxAttemptsBanner(
			// 		self::getConfig()->getUint('apimaxfail', 10)
			// 	)
			// );

			/**
			 * Trigger event to let the plugins alter the API framework.
			 * It is possible to use this event to include third-party applications.
			 * Here's described how:
			 * 
			 * $api->registerEventProvider('test_plugin', function(string $event, array $options)
			 * {
			 *     require_once JPath::clean(dirname(__FILE__) . '/plugins/TestPluginEvent.php');
			 *     return new TestPluginEvent($event, $options);
			 * });
			 *
			 * @param  	E4J\VikRestaurants\API\API  &$api
			 *
			 * @return 	void
			 *
			 * @since   1.9
			 */
			static::getPlatform()->getDispatcher()->trigger('onStartVikRestaurantsAPI', [&self::$api]);

			/**
			 * Trigger event to let the plugins alter the application framework.
			 * It is possible to use this event to include third-party applications.
			 * 
			 * In example:
			 * $api->addIncludePath($path);
			 * $api->addIncludePaths([$path1, $path2, ...]);
			 * 
			 * NOTE: it is needed to keep using this event for BC.
			 *
			 * @param  	FrameworkAPIs  &$api  The API framework instance.
			 *
			 * @return 	void
			 *
			 * @since 1.8.2
			 * @deprecated 1.11  Use 'onStartVikRestaurantsAPI' event instead.
			 */
			static::getEventDispatcher()->trigger('onInitApplicationFramework', array(&self::$api));

			if (!self::$api instanceof E4J\VikRestaurants\API\API)
			{
				// a plugin manipulated the framework with an unexpected value
				throw new UnexpectedValueException('Invalid API framework', 406);
			}
		}

		return self::$api;
	}

	/**
	 * Proxy for getAPI().
	 *
	 * @return 	E4J\VikRestaurants\API\API
	 * 
	 * @since 1.7
	 * @deprecated 1.11  Use getAPI() instead.
	 */
	public static function getApis()
	{
		return static::getAPI();
	}

	/**
	 * Returns the internal event dispatcher instance.
	 *
	 * @return 	VREEventDispatcher 	The event dispatcher.
	 *
	 * @since 	1.7.4
	 * 
	 * @deprecated 1.10  Use \VREFactory::getPlatform()->getDispatcher() instead.
	 */
	public static function getEventDispatcher()
	{
		if (static::$eventDispatcher === null)
		{
			VRELoader::import('library.event.dispatcher');

			// obtain the software version always from the database
			$version = static::getConfig()->get('version', VIKRESTAURANTS_SOFTWARE_VERSION);

			// build options array
			$options = array(
				'alias' 	=> 'com_vikrestaurants',
				'version' 	=> $version,
				'admin' 	=> JFactory::getApplication()->isClient('administrator'),
				'call' 		=> null, // call is useless as it would be always the same
			);

			static::$eventDispatcher = VREEventDispatcher::getInstance($options);
		}

		return static::$eventDispatcher;
	}

	/**
	 * Instantiate a new currency object.
	 *
	 * @return 	E4J\VikRestaurants\Currency\Currency
	 *
	 * @since 	1.8
	 */
	public static function getCurrency()
	{
		if (static::$currency === null)
		{
			/** @var E4J\VikRestaurants\Config\AbstractConfiguration */
			$config = static::getConfig();

			// obtain configuration data
			$data = [
				'currencyname'     => $config->getString('currencyname', 'EUR'),
				'currencysymb'     => $config->getString('currencysymb', '€'),
				'symbpos'          => $config->getUint('symbpos', 1),
				'currdecimalsep'   => $config->getString('currdecimalsep', '.'),
				'currthousandssep' => $config->getString('currthousandssep', ','),
				'currdecimaldig'   => $config->getUint('currdecimaldig', 2),
			];

			/**
			 * Try to translate currency.
			 *
			 * @since 1.8
			 */
			VikRestaurants::translateConfig($data);

			// create global currency object
			static::$currency = new \E4J\VikRestaurants\Currency\Currency([
				'code'      => $data['currencyname'],
				'symbol'    => $data['currencysymb'],
				'position'  => abs($data['symbpos']),
				'separator' => array($data['currdecimalsep'], $data['currthousandssep']),
				'digits'    => $data['currdecimaldig'],
				// include space if we have position equals to [1,2]
				'space'     => $data['symbpos'] > 0
			]);
		}

		return static::$currency;
	}

	/**
	 * Instantiate a new translator object.
	 *
	 * @return 	VRELanguageTranslator
	 *
	 * @since 	1.8
	 */
	public static function getTranslator()
	{
		if (static::$translator === null)
		{
			VRELoader::import('library.language.translator');

			static::$translator = VRELanguageTranslator::getInstance();
		}

		return static::$translator;
	}

	/**
	 * Instantiate a new wizard object.
	 *
	 * @return 	E4J\VikRestaurants\Wizard\Wizard
	 *
	 * @since 	1.8.3
	 */
	public static function getWizard()
	{
		if (static::$wizard === null)
		{
			// get global wizard instance
			$wizard = new \E4J\VikRestaurants\Wizard\Wizard;

			// complete setup only if not yet completed
			if (!$wizard->isDone())
			{
				// define list of steps to load
				$steps = [
					new \E4J\VikRestaurants\Wizard\Steps\SystemStep,
					new \E4J\VikRestaurants\Wizard\Steps\SectionsStep,
					new \E4J\VikRestaurants\Wizard\Steps\OpeningsStep,
					new \E4J\VikRestaurants\Wizard\Steps\RestaurantRoomsStep,
					new \E4J\VikRestaurants\Wizard\Steps\RestaurantTablesStep,
					new \E4J\VikRestaurants\Wizard\Steps\RestaurantProductsStep,
					new \E4J\VikRestaurants\Wizard\Steps\RestaurantMenusStep,
					new \E4J\VikRestaurants\Wizard\Steps\TakeAwayAttributesStep,
					new \E4J\VikRestaurants\Wizard\Steps\TakeAwayMenusStep,
					new \E4J\VikRestaurants\Wizard\Steps\TakeAwayToppingsStep,
					new \E4J\VikRestaurants\Wizard\Steps\TakeAwayToppingsGroupsStep,
					new \E4J\VikRestaurants\Wizard\Steps\TakeAwayServicesStep,
					new \E4J\VikRestaurants\Wizard\Steps\TakeAwayAreasStep,
					new \E4J\VikRestaurants\Wizard\Steps\PaymentsStep,
				];

				// set up wizard
				$wizard->setup($steps);

				// set up steps dependencies
				$wizard['openings']->addDependency($wizard['sections']);
				
				$wizard['rooms']->addDependency($wizard['sections']);
				$wizard['tables']->addDependency($wizard['sections'], $wizard['rooms']);
				$wizard['products']->addDependency($wizard['sections']);
				$wizard['menus']->addDependency($wizard['sections'], $wizard['products']);

				$wizard['tkattributes']->addDependency($wizard['sections']);
				$wizard['tkmenus']->addDependency($wizard['sections']);
				$wizard['tktoppings']->addDependency($wizard['sections']);
				$wizard['tkgroups']->addDependency($wizard['sections'], $wizard['tkmenus'], $wizard['tktoppings']);
				$wizard['tkservices']->addDependency($wizard['sections']);
				$wizard['tkareas']->addDependency($wizard['sections'], $wizard['tkservices']);
			}

			// cache wizard
			static::$wizard = $wizard;
		}

		return static::$wizard;
	}

	/**
	 * Returns the current platform handler.
	 *
	 * @return 	E4J\VikRestaurants\Platform\PlatformInterface
	 * 
	 * @since   1.9
	 */
	public static function getPlatform()
	{
		// check if platform class is already instantiated
		if (is_null(static::$platform))
		{
			if (VersionListener::isJoomla())
			{
				// running Joomla platform
				static::$platform = new \E4J\VikRestaurants\Platform\CMS\JoomlaPlatform();
			}
			else
			{
				// running WordPress platform
				static::$platform = new \E4J\VikRestaurants\Platform\CMS\WordPressPlatform();
			}
		}

		return static::$platform;
	}

	/**
	 * Returns the current backup manager instance.
	 *
	 * @return 	E4J\VikRestaurants\Backup\Manager
	 * 
	 * @since   1.9
	 */
	public static function getBackupManager()
	{
		// check if backup manager class is already instantiated
		if (is_null(static::$backupManager))
		{
			// auto-register all the default backup export types
			$containerDecorator = new E4J\VikRestaurants\DI\ContainerDecorator;

			// register all the supported backup export types
			$containerDecorator->register(VREHELPERS . '/src/libraries/Backup/Export/Types', [
				'template'  => 'export.{id}',
				'suffix'    => 'ExportType',
				'namespace' => 'E4J\\VikRestaurants\\Backup\\Export\\Types',
			]);

			// register all the supported backup import rules
			$containerDecorator->register(VREHELPERS . '/src/libraries/Backup/Import/Rules', [
				'template'  => 'import.{id}',
				'suffix'    => 'ImportRule',
				'namespace' => 'E4J\\VikRestaurants\\Backup\\Import\\Rules',
			]);

			// instantiate backup manager
			static::$backupManager = new \E4J\VikRestaurants\Backup\Manager($containerDecorator->getContainer());

			/**
			 * Trigger event to let the plugins alter the backup manager.
			 * It is possible to use this event to include third-party backup export types.
			 * Here's described how:
			 * 
			 * $backup->registerExportTypeProvider('test', function()
			 * {
			 *     require_once JPath::clean(dirname(__FILE__) . '/backup/TestExportType.php');
			 *     return new TestExportType;
			 * });
			 *
			 * @param   E4J\VikRestaurants\Backup\Manager  $backupManager
			 *
			 * @return  void
			 *
			 * @since   1.9
			 */
			static::getPlatform()->getDispatcher()->trigger('onPrepareBackupManager', [static::$backupManager]);
		}

		return static::$backupManager;
	}

	/**
	 * Returns the current datasheet factory instance.
	 *
	 * @return 	E4J\VikRestaurants\DataSheet\DataSheetFactory
	 * 
	 * @since   1.9
	 */
	public static function getDataSheetFactory()
	{
		// check if datasheet factory class is already instantiated
		if (is_null(static::$dataSheetFactory))
		{
			// auto-register all the default type handlers
			$containerDecorator = new E4J\VikRestaurants\DI\ContainerDecorator;

			// register all the supported datasheet types
			$containerDecorator->register(VREHELPERS . '/src/libraries/DataSheet/Export/Models', [
				'template'  => 'datasheet.{id}',
				'suffix'    => 'DataSheet',
				'namespace' => 'E4J\\VikRestaurants\\DataSheet\\Export\\Models',
			]);

			// register all the supported datasheet export drivers
			$containerDecorator->register(VREHELPERS . '/src/libraries/DataSheet/Export/Drivers', [
				'template'  => 'export.{id}',
				'suffix'    => 'ExportDriver',
				'namespace' => 'E4J\\VikRestaurants\\DataSheet\\Export\\Drivers',
			]);

			// instantiate datasheet factory
			static::$dataSheetFactory = new \E4J\VikRestaurants\DataSheet\DataSheetFactory($containerDecorator->getContainer());

			/**
			 * Trigger event to let the plugins alter the datasheet factory.
			 * It is possible to use this event to include third-party datasheet types.
			 * Here's described how:
			 * 
			 * $hub->registerDataSheetProvider('customers', function()
			 * {
			 *     require_once JPath::clean(dirname(__FILE__) . '/export/model/customers.php');
			 *     return new CustomersDataSheet;
			 * });
			 *
			 * @param   E4J\VikRestaurants\DataSheet\DataSheetFactory  $dataSheetFactory
			 *
			 * @return  void
			 *
			 * @since   1.9
			 */
			static::getPlatform()->getDispatcher()->trigger('onPrepareDatasheetFactory', [static::$dataSheetFactory]);
		}

		return static::$dataSheetFactory;
	}

	/**
	 * Returns the current coding hub instance.
	 *
	 * @return 	E4J\VikRestaurants\CodeHub\CodeHub
	 * 
	 * @since   1.9
	 */
	public static function getCodeHub()
	{
		// check if coding hub class is already instantiated
		if (is_null(static::$codeHub))
		{
			// auto-register all the default code handlers
			$containerDecorator = new E4J\VikRestaurants\DI\ContainerDecorator;

			// register all the supported code handlers
			$containerDecorator->register(VREHELPERS . '/src/libraries/CodeHub/Handlers', [
				'suffix'    => 'Handler',
				'namespace' => 'E4J\\VikRestaurants\\CodeHub\\Handlers',
			]);

			// instantiate coding hub
			static::$codeHub = new \E4J\VikRestaurants\CodeHub\CodeHub($containerDecorator->getContainer(), VRE_CUSTOM_CODE_FOLDER);

			/**
			 * Trigger event to let the plugins alter the coding hub.
			 * It is possible to use this event to include third-party code handlers (e.g. css).
			 * Here's described how:
			 * 
			 * $hub->registerCodeProvider('css', function()
			 * {
			 *     require_once JPath::clean(dirname(__FILE__) . '/code/css.php');
			 *     return new CssHandler;
			 * });
			 *
			 * @param   E4J\VikRestaurants\CodeHub\CodeHub  $hub
			 *
			 * @return  void
			 *
			 * @since   1.9
			 */
			static::getPlatform()->getDispatcher()->trigger('onPrepareCodeHub', [static::$codeHub]);
		}

		return static::$codeHub;
	}
}
