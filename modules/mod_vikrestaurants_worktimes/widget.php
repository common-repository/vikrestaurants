<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_worktimes
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

jimport('adapter.module.widget');

/**
 * Worktimes Module implementation for WP.
 *
 * @see 	JWidget
 * @since 	1.0
 */
class ModVikrestaurantsWorktimes_Widget extends JWidget
{
	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		// attach the absolute path of the module folder
		parent::__construct(dirname(__FILE__));

		try
		{
			/**
			 * Convert this widget into a block.
			 * 
			 * @since 1.3.2
			 */
			$this->registerBlockType(
				VIKRESTAURANTS_CORE_MEDIA_URI,
				[
					'icon' => 'clock',
					'keywords' => [
						__('VikRestaurants', 'vikrestaurants'),
						__('Working Times', 'vikrestaurants'),
						__('Widget'),
					],
				]
			);
		}
		catch (Throwable $error)
		{
			// there's a conflict with an outdated plugin
		}
	}
}
