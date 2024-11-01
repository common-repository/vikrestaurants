<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\Form;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Factory class used to render form controls depending on the platform currently in use.
 * 
 * @since 1.9
 */
interface FormFactoryInterface
{
	/**
	 * Creates a field according to the specified configuration.
	 * 
	 * @param   array|object  $options  The field configuration.
	 * 
	 * @return  E4J\VikRestaurants\Form\FormField
	 */
	public function createField($options = []);
}
