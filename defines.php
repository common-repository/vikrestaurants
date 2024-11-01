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

// Define _JEXEC constant in order to avoid any (compatibility) errors
defined('_JEXEC') or define('_JEXEC', 1);

// Software version
define('VIKRESTAURANTS_SOFTWARE_VERSION', '1.3.3');

// Software debugging flag
define('VIKRESTAURANTS_DEBUG', false);

// Base path
define('VIKRESTAURANTS_BASE', dirname(__FILE__));

// Libraries path
define('VIKRESTAURANTS_LIBRARIES', VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'libraries');

// Languages path
define('VIKRESTAURANTS_LANG', basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'languages');

// Core Media URI
define('VIKRESTAURANTS_CORE_MEDIA_URI', plugin_dir_url(__FILE__) . 'media/');

// Assets URI
define('VREASSETS_URI', plugin_dir_url(__FILE__) . 'site/assets/');
define('VREASSETS_ADMIN_URI', plugin_dir_url(__FILE__) . 'admin/assets/');

// URI Constants for admin and site sections (with trailing slash)
define('VRE_BASE_URI', plugin_dir_url(__FILE__));
define('VRE_ADMIN_URI', VRE_BASE_URI . 'admin/');
define('VRE_SITE_URI', VRE_BASE_URI . 'site/');
define('VRE_MODULES_URI', VRE_BASE_URI . 'modules/');

// Path Constants for admin and site sections (with NO trailing directory separator)
define('VREADMIN', VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'admin');
define('VREADMIN_URI', VRE_ADMIN_URI);
define('VREBASE', VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'site');
define('VREBASE_URI', VRE_SITE_URI);

define('VREMODULES', VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'modules');
define('VREMODULES_URI', VRE_MODULES_URI);

// Helpers path
define('VREHELPERS', VREBASE . DIRECTORY_SEPARATOR . 'helpers');

// Libraries path
define('VRELIB', VREHELPERS . DIRECTORY_SEPARATOR . 'library');

// Mail Templates path
define('VREMAIL_TEMPLATES_RESTAURANT', VREHELPERS . DIRECTORY_SEPARATOR . 'mail_tmpls');
define('VREMAIL_TEMPLATES_TAKEAWAY', VREHELPERS . DIRECTORY_SEPARATOR . 'tk_mail_tmpls');

// Upload path
$upload = wp_upload_dir();

define('VRE_UPLOAD_DIR_PATH', $upload['basedir'] . DIRECTORY_SEPARATOR . 'vikrestaurants');
define('VRE_UPLOAD_DIR_URI', $upload['baseurl'] . '/vikrestaurants/');

// Customers avatar path
define('VRECUSTOMERS_AVATAR', VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'customers' . DIRECTORY_SEPARATOR . 'avatar');

// Customers avatar URI
define('VRECUSTOMERS_AVATAR_URI', VRE_UPLOAD_DIR_URI . 'customers/avatar/');

// Media path
define('VREMEDIA', VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'media');

// Media small path
define('VREMEDIA_SMALL', VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'media@small');

// Media URI
define('VREMEDIA_URI', VRE_UPLOAD_DIR_URI . 'media/');

// Media small URI
define('VREMEDIA_SMALL_URI', VRE_UPLOAD_DIR_URI . 'media@small/');

// Invoice path
define('VREINVOICE', VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'archive');

// Invoice URI
define('VREINVOICE_URI', VRE_UPLOAD_DIR_URI . 'pdf/archive/');

// Mail attachments path
define('VRE_MAIL_ATTACHMENTS', VREBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'attachments');

// Customizer path
define('VRE_CSS_CUSTOMIZER', VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'customizer');
define('VRE_CUSTOM_CODE_FOLDER', VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'codehub');

// Customizer URI
define('VRE_CSS_CUSTOMIZER_URI', VRE_UPLOAD_DIR_URI . 'css/customizer/');

// Joomla BC
defined('JPATH_SITE') or define('JPATH_SITE', 'JPATH_SITE');
defined('JPATH_ADMINISTRATOR') or define('JPATH_ADMINISTRATOR', 'JPATH_ADMINISTRATOR');

/**
 * Site pre-processing flag.
 * When this flag is enabled, the plugin will try to dispatch the
 * site controller within the "init" action. This is made by 
 * fetching the shortcode assigned to the current URI.
 *
 * By disabling this flag, the site controller will be dispatched 
 * with the headers already sent.
 */
define('VIKRESTAURANTS_SITE_PREPROCESS', true);
