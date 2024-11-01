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
 * VikRestaurants take-away delivery areas view.
 *
 * @since 1.7
 */
class VikRestaurantsViewtkmapareas extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		/** @var E4J\VikRestaurants\DeliveryArea\AreasCollection */
		$this->shapes = E4J\VikRestaurants\DeliveryArea\AreasCollection::getInstance()
			->filter(new E4J\VikRestaurants\DeliveryArea\Filters\PublishedFilter());
		
		// display the template (default.php)
		parent::display($tpl);
	}
}
