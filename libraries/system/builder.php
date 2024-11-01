<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  system
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Helper class to setup the plugin.
 *
 * @since 1.0
 */
class VikRestaurantsBuilder
{
	/**
	 * Loads the .mo language related to the current locale.
	 *
	 * @return 	void
	 */
	public static function loadLanguage()
	{
		$app = JFactory::getApplication();

		// the language file is located in /languages folder
		$path 	 = VIKRESTAURANTS_LANG;

		$handler = VIKRESTAURANTS_LIBRARIES . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
		$domain  = 'vikrestaurants';

		// init language
		$lang = JFactory::getLanguage();
		
		$lang->attachHandler($handler . 'system.php', $domain);
		
		if ($app->isAdmin())
		{
			$lang->attachHandler($handler . 'adminsys.php', $domain);
			$lang->attachHandler($handler . 'admin.php', $domain);
		}
		else
		{
			$lang->attachHandler($handler . 'site.php', $domain);
		}

		$lang->load($domain, $path);
	}

	/**
	 * Pushes the plugin pages into the WP admin menu.
	 *
	 * @return 	void
	 *
	 * @link 	https://developer.wordpress.org/resource/dashicons/#star-filled
	 */
	public static function setupAdminMenu()
	{
		JLoader::import('adapter.acl.access');
		$capability = JAccess::adjustCapability('core.manage', 'com_vikrestaurants');

		// set default plugin menu title
		$default_title = JText::translate('COM_VIKRESTAURANTS_MENU');
		// let other plugins can filter the menu title
		$title = apply_filters('vikrestaurants_menu_title', $default_title);

		add_menu_page(
			JText::translate('COM_VIKRESTAURANTS'),         // page title
			$title ? $title : $default_title,       // menu title
			$capability,                            // capability
			'vikrestaurants',                       // slug
			array('VikRestaurantsBody', 'getHtml'), // callback
			'dashicons-coffee',                     // icon
			71                                      // ordering
		);
	}

	/**
	 * Setup HTML helper classes.
	 * This method should be used to register custom function
	 * for example to render own layouts.
	 *
	 * @return 	void
	 */
	public static function setupHtmlHelpers()
	{
		// helper method to render calendars layout
		JHtml::register('renderCalendar', function($data)
		{
			JHtml::fetch('script', VRE_SITE_URI . 'assets/js/jquery-ui.min.js');
			JHtml::fetch('stylesheet', VRE_SITE_URI . 'assets/css/jquery-ui.min.css');

			$layout = new JLayoutFile('html.plugins.calendar', null, array('component' => 'com_vikrestaurants'));
			
			return $layout->render($data);
		});

		// helper method to get the plugin layout file handler
		JHtml::register('layoutfile', function($layoutId, $basePath = null, $options = array())
		{
			$input = JFactory::getApplication()->input;

			if (!isset($options['component']) && !$input->getBool('option'))
			{
				// force layout file in case there is no active plugin
				$options['component'] = 'com_vikrestaurants';
			}

			return new JLayoutFile($layoutId, $basePath, $options);
		});

		// helper method to include the system JS file
		JHtml::register('system.js', function()
		{
			static $loaded = 0;

			if (!$loaded)
			{
				// include only once
				$loaded = 1;

				$internalFilesOptions = array('version' => VIKRESTAURANTS_SOFTWARE_VERSION);

				JHtml::fetch('script', VIKRESTAURANTS_CORE_MEDIA_URI . 'js/system.js', $internalFilesOptions, array('id' => 'vre-sys-script'));
				JHtml::fetch('stylesheet', VIKRESTAURANTS_CORE_MEDIA_URI . 'css/system.css', $internalFilesOptions, array('id' => 'vre-sys-style'));

				/**
				 * Load bootstrap only in the back-end to prevent any conflict with the installed theme.
				 * 
				 * @since 1.3
				 */
				if (JFactory::getApplication()->isClient('administrator'))
				{
					JHtml::fetch('script', VIKRESTAURANTS_CORE_MEDIA_URI . 'js/bootstrap.min.js', $internalFilesOptions, array('id' => 'bootstrap-script'));
					JHtml::fetch('stylesheet', VIKRESTAURANTS_CORE_MEDIA_URI . 'css/bootstrap.lite.css', $internalFilesOptions, array('id' => 'bootstrap-lite-style'));
				}
			}
		});

		// helper method to register select2 plugin
		JHtml::register('select2', function()
		{
			// stop loading select2 outside of VRE
			// JHtml::fetch('vrehtml.assets.select2');
		});
	}

	/**
	 * This method is used to configure the payments framework.
	 * Here should be registered all the default gateways supported
	 * by the plugin.
	 *
	 * @return 	void
	 */
	public static function configurePaymentFramework()
	{
		// push the pre-installed gateways within the payment drivers list
		add_filter('get_supported_payments_vikrestaurants', function($drivers)
		{
			$list = glob(VIKRESTAURANTS_LIBRARIES . DIRECTORY_SEPARATOR . 'payments' . DIRECTORY_SEPARATOR . '*.php');

			return array_merge($drivers, $list);
		});

		// load payment handlers when dispatched
		add_action('load_payment_gateway_vikrestaurants', function(&$drivers, $payment)
		{
			$classname = null;
			
			VikRestaurantsLoader::import('payments.' . $payment);

			switch ($payment)
			{
				case 'paypal':
					$classname = 'VikRestaurantsPayPalPayment';
					break;

				case 'paypal_express_checkout':
					$classname = 'VikRestaurantsPayPalExpressCheckoutPayment';
					break;

				case 'offline_credit_card':
					$classname = 'VikRestaurantsOfflineCreditCardPayment';
					break;

				case 'bank_transfer':
					$classname = 'VikRestaurantsBankTransferPayment';
					break;
			}

			if ($classname)
			{
				$drivers[] = $classname;
			}
		}, 10, 2);

		// echo directly the payment HTML as showPayment() only returns it
		add_action('vikrestaurants_payment_after_begin_transaction', function(&$payment, &$html)
		{
			echo $html;
		}, 10, 2);

		// manipulate response to be compliant with notifypayment task
		add_action('vikrestaurants_payment_after_validate_transaction', function(&$payment, &$status, &$response)
		{
			// manipulate the response to be compliant with the old payment system
			$response = [
				'verified'    => (int) $status->isVerified(),
				'tot_paid'    => $status->amount,
				'log'         => $status->log,
				'transaction' => $status->transaction,
			];
		}, 10, 3);
	}

	/**
	 * This method is used to configure the sms drivers framework.
	 * Here should be registered all the default drivers supported
	 * by the plugin.
	 *
	 * @return 	void
	 */
	public static function configureSmsFramework()
	{
		// push the pre-installed drivers within the sms drivers list
		add_filter('get_supported_sms_drivers_vikrestaurants', function($drivers)
		{
			$list = glob(VIKRESTAURANTS_LIBRARIES . DIRECTORY_SEPARATOR . 'sms' . DIRECTORY_SEPARATOR . '*.php');

			return array_merge($drivers, $list);
		});

		// load sms handlers when dispatched
		add_action('load_sms_driver_vikrestaurants', function(&$drivers, $driver)
		{
			$classname = null;
			
			VikRestaurantsLoader::import('sms.' . $driver);

			switch ($driver)
			{
				case 'clickatell':
					$classname = 'VikRestaurantsSmsClickatell';
					break;

				case 'clicksend':
					$classname = 'VikRestaurantsSmsClicksend';
					break;

				case 'cmtelecom':
					$classname = 'VikRestaurantsSmsCmtelecom';
					break;

				case 'smshosting':
					$classname = 'VikRestaurantsSmsHosting';
					break;

				case 'tellustalk':
					$classname = 'VikRestaurantsSmsTellustalk';
					break;
			}

			if ($classname)
			{
				$drivers[] = $classname;
			}
		}, 10, 2);
	}

	/**
	 * Registers all the widget contained within the modules folder.
	 *
	 * @return 	void
	 */
	public static function setupWidgets()
	{
		JLoader::import('adapter.module.factory');

		// load all the modules
		JModuleFactory::load(VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'modules');

		/**
		 * Loads also the widgets to display within the
		 * admin dashboard of WordPress.
		 *
		 * @since 1.1
		 */
		add_action('wp_dashboard_setup', function()
		{
			JLoader::import('adapter.dashboard.admin');

			// set up folder containing the widget to load
			$path = VIKRESTAURANTS_LIBRARIES . DIRECTORY_SEPARATOR . 'dashboard';
			// define the classname prefix
			$prefix = 'JDashboardWidgetVikRestaurants';

			try
			{
				// load and register widgets
				JDashboardAdmin::load($path, $prefix);
			}
			catch (Exception $e)
			{
				// silently suppress exception to avoid breaking the website

				if (VIKRESTAURANTS_DEBUG)
				{
					// propagate error in case of debug enabled
					throw $e;
				}
			}
		});
	}

	/**
	 * Configures the RSS feeds reader.
	 *
	 * @return 	JRssReader
	 *
	 * @since 	1.1
	 */
	public static function setupRssReader()
	{
		// autoload RSS handler class
		JLoader::import('adapter.rss.reader');

		/**
		 * Hook used to manipulate the RSS channels to which the plugin is subscribed.
		 *
		 * @param 	array    $channels  A list of RSS permalinks.
		 * @param 	boolean  $status    True to return only the published channels.
		 *
		 * @return 	array    A list of supported channels.
		 *
		 * @since 	1.1
		 */
		$channels = apply_filters('vikrestaurants_fetch_rss_channels', array(), true);

		if (VIKRESTAURANTS_DEBUG)
		{
			/**
			 * Filters the transient lifetime of the feed cache.
			 *
			 * @since 2.8.0
			 *
			 * @param 	integer  $lifetime  Cache duration in seconds. Default is 43200 seconds (12 hours).
			 * @param 	string   $filename  Unique identifier for the cache object.
			 */
			add_filter('wp_feed_cache_transient_lifetime', function($time, $url) use ($channels)
			{
				// in case of debug enabled, cache the feeds only for 60 seconds
				if ($url == $channels || in_array($url, $channels))
				{
					$time = 60;
				}

				return $time;
			}, 10, 2);
		}

		// instantiate RSS reader
		$rss = JRssReader::getInstance($channels, 'vikrestaurants');

		/**
		 * Hook used to apply some stuff before returning the RSS reader.
		 *
		 * @param 	JRssReader  &$rss  The RSS reader handler.
		 *
		 * @return 	void
		 *
		 * @since 	1.1
		 */
		do_action_ref_array('vikrestaurants_before_use_rss', array(&$rss));

		return $rss;
	}

	/**
	 * Sets up the wizard.
	 *
	 * @return 	void
	 *
	 * @since 	1.1
	 */
	public static function setupWizard()
	{
		add_action('vikrestaurants_setup_wizard', function($wizard)
		{
			// include path to load additional steps
			$wizard->addIncludePath(VIKRESTAURANTS_LIBRARIES . DIRECTORY_SEPARATOR . 'wizard');
		}, 10, 2);

		add_action('vikrestaurants_after_setup_wizard', function($wizard)
		{
			// add step to manage the shortcodes
			$wizard->addStep(new VREWizardStepShortcodes());

			// add sections step as dependency
			$wizard['shortcodes']->addDependency($wizard['sections']);

			// add step to install sample data (after sections widget)
			$wizard->addStepAfter(new VREWizardStepSampleData(), 'sections');
		}, 10, 2);
	}

	/**
	 * Implements the tools needed to manage the overrides
	 * without having to use a FTP client.
	 *
	 * @return 	void
	 *
	 * @since 	1.2
	 */
	public static function setupOverridesManager()
	{
		// manually append fieldset to manage page overrides
		add_action('vikrestaurants_display_view_configapp_customizer', function($forms, $view, $setup)
		{
			if (!$forms)
			{
				// init forms array
				$forms = [];
			}

			// render configuration layout
			$html = JLayoutHelper::render('html.overrides.config', [
				'view' => $view,
			]);

			// add fieldset to forms
			$forms[__('Page Overrides', 'vikrestaurants')] = $html;

			// register an icon for this new fieldset
			$setup->icons[__('Page Overrides', 'vikrestaurants')] = 'fas fa-cut';

			return $forms;
		}, 10, 3);
	}

	/**
	 * Registers all the events used to backup the extendable files when needed.
	 *
	 * @return 	void
	 */
	public static function setupMirroring()
	{
		/**
		 * Backup all the files below before accessing the configuration page.
		 *
		 * - custom CSS
		 * - mail templates
		 */
		add_action('vikrestaurants_before_display_editconfig', function()
		{
			// import update manager
			VikRestaurantsLoader::import('update.manager');

			try
			{
				// backup custom CSS file
				VikRestaurantsUpdateManager::doBackup(
					// target to backup
					VREBASE . '/assets/css/vre-custom.css',
					// destination folder
					VRE_UPLOAD_DIR_PATH . '/css'
				);

				// backup restaurant mail templates
				VikRestaurantsUpdateManager::doBackup(
					// target to backup
					VREBASE . '/helpers/mail_tmpls',
					// destination folder
					VRE_UPLOAD_DIR_PATH . '/mail/tmpl'
				);

				// backup take-away mail templates
				VikRestaurantsUpdateManager::doBackup(
					// target to backup
					VREBASE . '/helpers/tk_mail_tmpls',
					// destination folder
					VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl'
				);
			}
			catch (Exception $e)
			{
				// raise error and avoid breaking the flow
				JFactory::getApplication()->enqueueMessage("Impossible to complete back-up.\n" . $e->getMessage(), 'error');
			}
		});

		/**
		 * Backup all the files below before accessing the dashboard page.
		 *
		 * - languages
		 * - audio
		 */
		add_action('vikrestaurants_before_display_vikrestaurants', function()
		{
			// check if we are doing an AJAX request
			if (!wp_doing_ajax())
			{
				// import update manager
				VikRestaurantsLoader::import('update.manager');

				try
				{
					// backup language files
					VikRestaurantsUpdateManager::doBackup(
						// target to backup
						VIKRESTAURANTS_BASE . '/languages',
						// destination folder
						VRE_UPLOAD_DIR_PATH . '/languages'
					);

					// backup audio files
					VikRestaurantsUpdateManager::doBackup(
						// target to backup
						VREADMIN . '/assets/audio',
						// destination folder
						VRE_UPLOAD_DIR_PATH . '/audio'
					);
				}
				catch (Exception $e)
				{
					// raise error and avoid breaking the flow
					JFactory::getApplication()->enqueueMessage("Impossible to complete back-up.\n" . $e->getMessage(), 'error');
				}
			}
		});
	}
}
