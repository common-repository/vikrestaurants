<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateAdapter;

/**
 * Update adapter for com_vikrestaurants 1.9.1 version.
 *
 * NOTE. do not call exit() or die() because the update won't be finalised correctly.
 * Return false instead to stop in anytime the flow without errors.
 *
 * @since 1.9.1
 */
class UpdateAdapter1_9_1 extends UpdateAdapter
{
	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		\JModelLegacy::addIncludePath(VREADMIN . DIRECTORY_SEPARATOR . 'models');

		// setup update rules
		$this->attachRule('afterupdate', 'ConfigurationFixer');
        $this->attachRule('afterupdate', 'StatusCodesMapper');
	}
}
