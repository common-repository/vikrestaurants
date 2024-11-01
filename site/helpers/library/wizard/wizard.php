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

VRELoader::import('library.wizard.step');

/**
 * Collection class used to manage the steps needed to complete
 * a basic configuration of VikRestaurants.
 *
 * @since 1.8.3
 * 
 * @deprecated 1.10  Use E4J\VikRestaurants\Wizard\Wizard instead.
 */
class VREWizard extends E4J\VikRestaurants\Wizard\Wizard
{
	/**
	 * Class constructor proxy.
	 * 
	 * @return  self
	 */
	public static function getInstance()
	{
		return new static();
	}

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		// add default include path
		$this->addIncludePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes');
	}
}
