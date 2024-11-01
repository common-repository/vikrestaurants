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
 * Manager class used to setup the LITE version of the plugin.
 *
 * @since 1.2.4
 */
abstract class VikRestaurantsLiteManager
{
	/**
	 * Flag used to avoid initializing the setup more than once.
	 * 
	 * @var boolean
	 */
	private static $setup = false;

	/**
	 * Accessor used to start the setup.
	 * 
	 * @param 	mixed  $helper  The implementor instance or a static class.
	 * 
	 * @return 	void
	 */
	final public static function setup($helper = null)
	{
		if (!static::$setup && !static::guessPro())
		{
			if (!$helper)
			{
				// use the default implementor
				VikRestaurantsLoader::import('lite.helper');
				$helper = new VikRestaurantsLiteHelper();
			}

			// set up only once and in case of missing PRO version
			static::$setup = static::doSetup($helper);
		}
	}

	/**
	 * Helper method used to assume whether the PRO version is
	 * installed or not, because it is not enough to check whether
	 * a PRO license is registered. In example, we cannot automatically
	 * re-enable the LITE restrictions after a PRO license expires.
	 * 
	 * @return 	boolean
	 */
	public static function guessPro()
	{
		// immediately check whether we have a valid PRO license
		if (VikRestaurantsLicense::isPro())
		{
			return true;
		}

		// Missing PRO license or expired... First make sure the
		// license key was specified.
		if (!VikRestaurantsLicense::getKey())
		{
			// missing license key, never allow usage of PRO features
			return false;
		}

		// Check whether the PRO license was ever installed, which
		// can be easily done by looking for the PayPal integration.
		return VikRestaurantsLoader::import('payments.paypal');
	}

	/**
	 * Setup implementor.
	 * 
	 * @param 	mixed  $helper  The implementor instance or a static class.
	 * 
	 * @return 	boolean
	 */
	protected static function doSetup($helper)
	{
		/**
		 * Filters which capabilities a role has.
		 *
		 * @since 2.0.0
		 *
		 * @param 	bool[]  $capabilities  Array of key/value pairs where keys represent a capability name and boolean values
		 *                                 represent whether the role has that capability.
		 * @param 	string  $cap           Capability name.
		 * @param 	string  $name          Role name.
		 */
		add_filter('role_has_cap', array($helper, 'restrictCapabilities'));

		/**
		 * Dynamically filter a user's capabilities.
		 *
		 * @since 2.0.0
		 * @since 3.7.0 Added the `$user` parameter.
		 *
		 * @param 	bool[]    $allcaps  Array of key/value pairs where keys represent a capability name
		 *                              and boolean values represent whether the user has that capability.
		 * @param 	string[]  $caps     Required primitive capabilities for the requested capability.
		 * @param 	array     $args     Arguments that accompany the requested capability check.
		 * @param 	WP_User   $user     The user object.
		 */
		add_filter('user_has_cap', array($helper, 'restrictCapabilities'));

		/**
		 * Fires after WordPress has finished loading but before any headers are sent.
		 *
		 * Most of WP is loaded at this stage, and the user is authenticated. WP continues
		 * to load on the {@see 'init'} hook that follows (e.g. widgets), and many plugins instantiate
		 * themselves on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
		 *
		 * @since 1.5.0
		 */
		add_action('init', array($helper, 'preventEditReservationAccess'), 5);

		/**
		 * Fires before the controller of VikRestaurants is dispatched.
		 * Useful to require libraries and to check user global permissions.
		 *
		 * @since 1.0
		 */
		add_action('vikrestaurants_before_dispatch', array($helper, 'listenTosFieldSavingTask'));
		add_action('vikrestaurants_before_dispatch', array($helper, 'displayBanners'));

		/**
		 * Fires before the controller of VikRestaurants is dispatched.
		 * Useful to require libraries and to check user global permissions.
		 *
		 * @since 1.0
		 */
		add_action('vikrestaurants_before_dispatch', array($helper, 'preventMapAccessFromWizard'), 5);

		/**
		 * Fires after the controller of VikRestaurants is dispatched.
		 * Useful to include web resources (CSS and JS).
		 * 
		 * If the controller terminates the process (exit or die),
		 * this hook won't be fired.
		 *
		 * @since 1.0
		 */
		add_action('vikrestaurants_after_dispatch', array($helper, 'includeLiteAssets'));

		/**
		 * Trigger event after completing the wizard setup.
		 * This is useful, in example, to rearrange the registered steps.
		 *
		 * @param 	boolean    $status  True on success, false otherwise.
		 * @param 	VREWizard  &$wizard  The wizard instance.
		 *
		 * @since 	1.2
		 */
		add_action('vikrestaurants_after_setup_wizard', array($helper, 'removeWizardSteps'), 15, 2);

		/**
		 * Fires after the controller displays the view.
		 *
		 * @param 	JView  $view  The view instance.
		 *
		 * @since 	1.0
		 */
		add_action('vikrestaurants_after_display_customf', array($helper, 'displayTosFieldManagementForm'));

		/**
		 * Fires after the controller displays the view.
		 *
		 * @param 	JView  $view  The view instance.
		 *
		 * @since 	1.0
		 */
		add_action('vikrestaurants_after_display_reservations', array($helper, 'replaceBillManagementLink'));

		/**
		 * Fires after the controller displays the view.
		 *
		 * @param 	JView  $view  The view instance.
		 *
		 * @since 	1.0
		 */
		add_action('vikrestaurants_after_display_managereservation', array($helper, 'adjustToolbarFromReservationManagement'));

		/**
		 * Trigger hook to allow the plugins to bind the object that
		 * is going to be saved.
		 *
		 * @param 	boolean  $save   False to abort saving.
		 * @param 	mixed 	 &$src 	 The array/object to bind.
		 * @param 	JTable   $table  The table instance.
		 *
		 * @throws 	Exception  It is possible to throw an exception to abort
		 *                     the saving process and return a readable message.
		 *
		 * @since 	1.0
		 */
		add_filter('vikrestaurants_before_save_menu', array($helper, 'applyRestaurantMenuSaveRestrictions'), 10, 3);

		/**
		 * Trigger hook to allow the plugins to bind the object that
		 * is going to be saved.
		 *
		 * @param 	boolean  $save   False to abort saving.
		 * @param 	mixed 	 &$src 	 The array/object to bind.
		 * @param 	JTable   $table  The table instance.
		 *
		 * @throws 	Exception  It is possible to throw an exception to abort
		 *                     the saving process and return a readable message.
		 *
		 * @since 	1.0
		 */
		add_filter('vikrestaurants_before_save_menusproduct', array($helper, 'applyRestaurantProductSaveRestrictions'), 10, 3);

		/**
		 * Trigger hook to allow the plugins to bind the object that
		 * is going to be saved.
		 *
		 * @param 	boolean  $save   False to abort saving.
		 * @param 	mixed 	 &$src 	 The array/object to bind.
		 * @param 	JTable   $table  The table instance.
		 *
		 * @throws 	Exception  It is possible to throw an exception to abort
		 *                     the saving process and return a readable message.
		 *
		 * @since 	1.0
		 */
		add_filter('vikrestaurants_before_save_tkmenu', array($helper, 'applyTakeAwayMenuSaveRestrictions'), 10, 3);

		/**
		 * Fires after the controller displays the view.
		 *
		 * @param 	JView  $view  The view instance.
		 *
		 * @since 	1.0
		 */
		add_action('vikrestaurants_after_display_managetkmenu', array($helper, 'applyTakeAwayMenuVisualRestrictions'));

		/**
		 * Trigger hook to allow the plugins to bind the object that
		 * is going to be saved.
		 *
		 * @param 	boolean  $save   False to abort saving.
		 * @param 	mixed 	 &$src 	 The array/object to bind.
		 * @param 	JTable   $table  The table instance.
		 *
		 * @throws 	Exception  It is possible to throw an exception to abort
		 *                     the saving process and return a readable message.
		 *
		 * @since 	1.0
		 */
		add_filter('vikrestaurants_before_save_tkentry', array($helper, 'applyTakeAwayProductSaveRestrictions'), 10, 3);

		/**
		 * Fires after the controller displays the view.
		 *
		 * @param 	JView  $view  The view instance.
		 *
		 * @since 	1.0
		 */
		add_action('vikrestaurants_after_display_managetkentry', array($helper, 'applyTakeAwayProductVisualRestrictions'));

		/**
		 * Trigger hook to allow the plugins to bind the object that
		 * is going to be saved.
		 *
		 * @param 	boolean  $save   False to abort saving.
		 * @param 	mixed 	 &$src 	 The array/object to bind.
		 * @param 	JTable   $table  The table instance.
		 *
		 * @throws 	Exception  It is possible to throw an exception to abort
		 *                     the saving process and return a readable message.
		 *
		 * @since 	1.0
		 */
		add_filter('vikrestaurants_before_save_tkentrygroup', array($helper, 'applyTakeAwayToppingsGroupSaveRestrictions'), 10, 3);

		/**
		 * Trigger hook to allow the plugins to bind the object that
		 * is going to be saved.
		 *
		 * @param 	boolean  $save   False to abort saving.
		 * @param 	mixed 	 &$src 	 The array/object to bind.
		 * @param 	JTable   $table  The table instance.
		 *
		 * @throws 	Exception  It is possible to throw an exception to abort
		 *                     the saving process and return a readable message.
		 *
		 * @since 	1.0
		 */
		add_filter('vikrestaurants_before_save_tkarea', array($helper, 'applyTakeAwayDeliveryAreaSaveRestrictions'), 10, 3);

		/**
		 * Fires after the controller displays the view.
		 *
		 * @param 	JView  $view  The view instance.
		 *
		 * @since 	1.0
		 */
		add_action('vikrestaurants_after_display_editconfig', array($helper, 'removeReviewsPanelFromConfiguration'));
		add_action('vikrestaurants_after_display_editconfig', array($helper, 'removeStocksPanelFromConfiguration'));
		add_action('vikrestaurants_after_display_editconfig', array($helper, 'removeApplicationsTabFromConfiguration'));
	}
}
