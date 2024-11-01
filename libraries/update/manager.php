<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  update
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.database.helper');
VikRestaurantsLoader::import('update.changelog');
VikRestaurantsLoader::import('update.license');

/**
 * Class used to handle the upgrade of the plugin.
 *
 * @since 1.0
 */
class VikRestaurantsUpdateManager
{
	/**
	 * Checks if the current version should be updated.
	 *
	 * @param 	string 	 $version 	The version to check.
	 *
	 * @return 	boolean  True if should be updated, otherwise false.
	 */
	public static function shouldUpdate($version)
	{
		if (is_null($version))
		{
			return false;
		}

		return version_compare($version, VIKRESTAURANTS_SOFTWARE_VERSION, '<');
	}

	/**
	 * Executes the SQL file for the installation of the plugin.
	 *
	 * @return 	void
	 *
	 * @uses 	execSqlFile()
	 * @uses 	installAcl()
	 * @uses 	installProSettings()
	 */
	public static function install()
	{
		self::execSqlFile(VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'install.mysql.utf8.sql');
		
		$dbo = JFactory::getDbo();

		// extract current lang tag by splitting regional code and country code
		$tag = preg_split("/[_-]/", JFactory::getLanguage()->getTag());

		// create customf field containing rule (phone number) and country code
		$field = new stdClass;
		$field->rule   = VRCustomFields::PHONE_NUMBER;
		$field->choose = end($tag);	

		// update all custom fields with PHONE NUMBER rule
		$dbo->updateObject('#__vikrestaurants_custfields', $field, 'rule');

		$config = VREFactory::getConfig();

		// create the configuration record with the email address of the current user
		$config->set('adminemail', JFactory::getUser()->email);

		// footer must be disabled by default
		$config->set('showfooter', false);

		// auto turn off settings not supported by LITE version
		$config->set('enablereviews', 0);
		$config->set('revtakeaway', 0);
		$config->set('tkenablestock', 0);
		$config->set('apifw', 0);

		// get JUri object
		$uri = JUri::getInstance();
		// get host from URI and split chunks by dot
		$domain = explode('.', $uri->toString(array('host')));
		// exclude TLD
		array_pop($domain);
		// join remaining parts
		$domain = implode(' ', $domain);
		// make the words uppercase
		$domain = ucwords($domain);
		// update restaurant name with domain
		$config->set('restname', $domain);

		// search for the default Privacy Policy page
		$post = get_page_by_path('privacy-policy');

		if ($post)
		{
			// get Privacy Policy URL
			$pp_link = get_permalink($post->ID);

			if ($pp_link)
			{
				// update GDPR link with existing Privacy Policy
				$config->set('policylink', $pp_link);
			}
		}

		// truncate the payment gateways table
		$dbo->setQuery("TRUNCATE TABLE `#__vikrestaurants_gpayments`");
		$dbo->execute();

		// install terms and conditions custom fields
		$fieldTable = JTableVRE::getInstance('customf', 'VRETable');

		$tos = array(
			'name'     => 'I read and accept the terms and conditions',
			'type'     => 'checkbox',
			'required' => 1,
			'poplink'  => $config->get('policylink'),
			'group'    => 0,
			'id'       => 0,
		);

		// save for restaurant
		$fieldTable->save($tos);

		// save for take-away
		$tos['group'] = 1;
		$fieldTable->save($tos);

		self::installAcl();
		self::installProSettings();

		// write CSS custom file
		$path = VREBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'vre-custom.css';
		$handle = fopen($path, 'w+');
		fwrite($handle, "/* put below your custom css code for VikRestaurants */\n\n");
		fclose($handle);

		// import folder helper
		JLoader::import('adapter.filesystem.folder');

		// create overrides folder
		JFolder::create(VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'overrides');

		// create languages folder
		JFolder::create(VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'languages');

		// create media folders
		JFolder::create(VREMEDIA);
		JFolder::create(VREMEDIA_SMALL);

		// create customers folders
		JFolder::create(VRECUSTOMERS_AVATAR);

		// create invoice templates folders
		JFolder::create(VREINVOICE);

		// create mail folders
		JFolder::create(VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'tmpl');
		JFolder::create(VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'tk_mail' . DIRECTORY_SEPARATOR . 'tmpl');

		// create CSS folder
		JFolder::create(VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'css');

		// create audio folder
		JFolder::create(VRE_UPLOAD_DIR_PATH . DIRECTORY_SEPARATOR . 'audio');

		// create CSS customizer folder
		JFolder::create(VRE_CSS_CUSTOMIZER);

		// create Code Hub folder
		JFolder::create(VRE_CUSTOM_CODE_FOLDER);

		// copy default images into the WordPress media folder
		static::doBackup(VREBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media', VREMEDIA);
		static::doBackup(VREBASE . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media@small', VREMEDIA_SMALL);
	}

	/**
	 * Executes the SQL file for the uninstallation of the plugin.
	 *
	 * @param 	boolean  $drop 	True to drop the tables of VikRestaurants from the database.
	 *
	 * @return 	void
	 *
	 * @uses 	execSqlFile()
	 * @uses 	uninstallAcl()
	 * @uses 	uninstallProSettings()
	 */
	public static function uninstall($drop = true)
	{
		if ($drop)
		{
			self::execSqlFile(VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'uninstall.mysql.utf8.sql');
		}
		
		self::uninstallAcl();
		self::uninstallProSettings();

		// clear cached documentation
		VikRestaurantsScreen::clearCache();

		// import folder helper
		JLoader::import('adapter.filesystem.folder');

		// delete "vikrestaurants" folder in uploads dir
		// and all its children recursively
		JFolder::delete(VRE_UPLOAD_DIR_PATH);
	}

	/**
	 * Launches the process to finalise the update.
	 *
	 * @param 	string 	$version 	The current version.
	 *
	 * @uses 	getFixer()
	 * @uses 	installSql()
	 * @uses 	installAcl()
	 */
	public static function update($version)
	{
		$fixer = self::getFixer($version);

		// trigger before installation routine

		$res = $fixer->beforeInstallation();

		if ($res === false)
		{
			return false;
		}

		// install SQL statements

		$res = self::installSql($version);

		if ($res === false)
		{
			return false;
		}

		// install ACL

		$res = self::installAcl();

		if ($res === false)
		{
			return false;
		}

		// restore backed up files

		try
		{
			self::restoreBackup();
		}
		catch (Exception $e)
		{
			// raise error instead of aborting the update process
			JFactory::getApplication()->enqueueMessage("Impossible to restore backup.\n" . $e->getMessage(), 'error');
		}

		// trigger after installation routine

		$res = $fixer->afterInstallation();

		return ($res === false ? false : true);
	}

	/**
	 * Backups the specified source within the given destination.
	 *
	 * @param 	string 	 $src 	The file/folder to backup.
	 * 						  	In case of folder, only the first-level files
	 * 				 		  	will be moved within the destination path.
	 * @param 	string 	 $dest 	The destination folder.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @throws 	RuntimeException
	 */
	public static function doBackup($src, $dest)
	{
		// import folder helper
		JLoader::import('adapter.filesystem.folder');

		// clean paths
		$src  = JPath::clean($src);
		$dest = JPath::clean($dest);

		/**
		 * Make sure the destination folder exists.
		 * 
		 * @since 1.3 Auto-create the folder in case it does not exist.
		 */
		if (!JFolder::exists($dest) && !JFolder::create($dest))
		{
			// throws exception in case of debug enabled
			if (VIKRESTAURANTS_DEBUG)
			{
				throw new RuntimeException(sprintf('Destination folder [%s] not found.', $dest), 404);
			}

			// missing destination
			return false;
		}

		// check if the source is a single file
		if (is_file($src))
		{
			$files = (array) $src;
		}
		// otherwise check if the source is a folder
		else if (JFolder::exists($src))
		{
			// folder path, filter ('.' means all), no recursive, return full path, exclude elements
			$files = JFolder::files($src, '.', false, true, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
		}
		else
		{
			/**
			 * Throws exception in case of debug enabled.
			 * 
			 * @since 1.3 Skip error in case the source is the custom CSS file for the front-end.
			 */
			if (VIKRESTAURANTS_DEBUG && basename($src) !== 'vre-custom.css')
			{
				throw new RuntimeException(sprintf('Invalid source path [%s].', $src), 400);
			}

			// nothing to backup
			return false;
		}

		// make sure we don't have an empty array
		if (!count($files))
		{
			// nothing to backup
			return false;
		}

		/**
		 * Define an array of files to ignore.
		 * 
		 * @since 1.2.4
		 */
		static $skip = [];

		if (!$skip)
		{
			// we need to exclude all the core e-mail templates to prevent the
			// system from restoring outdated code (restaurant)
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/mail/tmpl/admin_email_tmpl.php');
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/mail/tmpl/cancellation_email_tmpl.php');
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/mail/tmpl/customer_email_tmpl.php');
			
			// we need to exclude all the core e-mail templates to prevent the
			// system from restoring outdated code (take-away)
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl/takeaway_admin_email_tmpl.php');
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl/takeaway_cancellation_email_tmpl.php');
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl/takeaway_customer_email_tmpl.php');
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl/takeaway_review_email_tmpl.php');
			$skip[] = JPath::clean(VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl/takeaway_stock_email_tmpl.php');
		}

		$res = true;

		foreach ($files as $file)
		{
			/**
			 * In case the file is contained within the skip list,
			 * do not restore the backup.
			 * 
			 * @since 1.2.4
			 */
			if (in_array($file, $skip))
			{
				continue;
			}

			// create full destination file
			$fileDest = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($file);

			// proceed only in case the destination file doesn't exist yet
			// or the 2 files have a different bytes size
			if (!is_file($fileDest) || filesize($file) != filesize($fileDest))
			{
				// copy file
				if (!JFile::copy($file, $fileDest))
				{
					// throws exception in case of debug enabled
					if (VIKRESTAURANTS_DEBUG)
					{
						throw new RuntimeException(sprintf('Impossible to copy [%s] onto [%s].', $file, $fileDest), 500);
					}

					$res = false;
				}
			}
		}

		return $res;
	}

	/**
	 * Restores all the files that have been backed-up using doBackup() method.
	 *
	 * @return 	boolean  True on success, false otherwise.
	 *
	 * @throws 	RuntimeException
	 */
	public static function restoreBackup()
	{
		$lookup = array(
			// custom CSS
			array(
				// target to restore
				VRE_UPLOAD_DIR_PATH . '/css/vre-custom.css',
				// destination folder
				VREBASE . '/assets/css',
			),
			// restaurant mail templates
			array(
				// target to restore
				VRE_UPLOAD_DIR_PATH . '/mail/tmpl',
				// destination folder
				VREBASE . '/helpers/mail_tmpls',	
			),
			// take-away mail templates
			array(
				// target to restore
				VRE_UPLOAD_DIR_PATH . '/tk_mail/tmpl',
				// destination folder
				VREBASE . '/helpers/tk_mail_tmpls',	
			),
			// languages
			array(
				// target to restore
				VRE_UPLOAD_DIR_PATH . '/languages',
				// destination folder
				VIKRESTAURANTS_BASE . '/languages',
			),
			// audio
			array(
				// target to restore
				VRE_UPLOAD_DIR_PATH . '/audio',
				// destination folder
				VREADMIN . '/assets/audio',
			),
		);

		$res    = true;
		$errors = array();

		// iterate list
		foreach ($lookup as $chunk)
		{
			list($src, $dest) = $chunk;

			try
			{
				// do backup by using reversed arguments
				$res = static::doBackup($src, $dest) && $res;
			}
			catch (Exception $e)
			{
				$res = false;

				// catch any raised exceptions
				$errors[] = $e->getMessage();
			}
		}

		// re-throw exception in case of debug enabled
		if ($errors && VIKRESTAURANTS_DEBUG)
		{
			throw new RuntimeException(implode("\n", $errors), 500);
		}

		return $res;
	}

	/**
	 * Get the script class to run the installation methods.
	 *
	 * @param 	string 	$version 	The current version.
	 *
	 * @return 	VikRestaurantsUpdateFixer
	 */
	protected static function getFixer($version)
	{
		VikRestaurantsLoader::import('update.fixer');
	
		return new VikRestaurantsUpdateFixer($version);
	}

	/**
	 * Provides the installation of the ACL routines.
	 *
	 * @return 	boolean  True on success, otherwise false.	
	 */
	protected static function installAcl()
	{
		JLoader::import('adapter.acl.access');
		$actions = JAccess::getActions('vikrestaurants');

		// No actions found!
		// Probably, the main folder is not called "vikrestaurants".
		if (!$actions)
		{
			return false;
		}

		$roles = array(
			get_role('administrator'),
		);

		foreach ($roles as $role)
		{
			if ($role)
			{
				foreach ($actions as $action)
				{
					$cap = JAccess::adjustCapability($action->name, 'com_vikrestaurants');
					$role->add_cap($cap, true);
				}
			}
		}

		return true;
	}

	/**
	 * Sets up the options for using the Pro version.
	 *
	 * @return 	void
	 */
	protected static function installProSettings()
	{
		VikRestaurantsChangelog::install();
		VikRestaurantsLicense::install();
	}

	/**
	 * Sets up the options for using the Pro version.
	 *
	 * @return 	void
	 */
	protected static function uninstallProSettings()
	{
		VikRestaurantsChangelog::uninstall();
		VikRestaurantsLicense::uninstall();
	}

	/**
	 * Provides the uninstallation of the ACL routines.
	 *
	 * @return 	boolean  True on success, otherwise false.	
	 */
	protected static function uninstallAcl()
	{
		JLoader::import('adapter.acl.access');
		$actions = JAccess::getActions('vikrestaurants');

		// No actions found!
		// Probably, something went wrong while installing the plugin.
		if (!$actions)
		{
			return false;
		}

		$roles = array(
			get_role('administrator'),
		);

		foreach ($roles as $role)
		{
			if ($role)
			{
				foreach ($actions as $action)
				{
					$cap = JAccess::adjustCapability($action->name, 'com_vikrestaurants');
					$role->remove_cap($cap);
				}
			}
		}

		return true;
	}

	/**
	 * Run all the proper SQL files.
	 *
	 * @param 	string 	 $version 	The current version.
	 *
	 * @return 	boolean  True on success, otherwise false.
	 *
	 * @uses 	execSqlFile()
	 */
	protected static function installSql($version)
	{
		$dbo = JFactory::getDbo();

		$ok = true;

		$sql_base = VIKRESTAURANTS_BASE . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR . 'mysql' . DIRECTORY_SEPARATOR;

		try
		{
			foreach (glob($sql_base . '*.sql') as $file)
			{
				$name  = basename($file);
				$sql_v = substr($name, 0, strrpos($name, '.'));

				if (version_compare($sql_v, $version, '>'))
				{
					// in case the SQL version is newer, execute the queries listed in the file
					self::execSqlFile($file, $dbo);
				}
			}
		}
		catch (Exception $e)
		{
			$ok = false;
		}

		return $ok;
	}

	/**
	 * Executes all the queries contained in the specified file.
	 *
	 * @param 	string 		$file 	The SQL file to launch.
	 * @param 	JDatabase 	$dbo 	The database driver handler.
	 *
	 * @return 	void
	 */
	protected static function execSqlFile($file, $dbo = null)
	{
		if (!is_file($file))
		{
			return;
		}

		if ($dbo === null)
		{
			$dbo = JFactory::getDbo();
		}

		$handle = fopen($file, 'r');

		$bytes = '';
		while (!feof($handle))
		{
			$bytes .= fread($handle, 8192);
		}

		fclose($handle);

		foreach (JDatabaseHelper::splitSql($bytes) as $q)
		{
			$dbo->setQuery($q);
			$dbo->execute();
		}
	}
}
