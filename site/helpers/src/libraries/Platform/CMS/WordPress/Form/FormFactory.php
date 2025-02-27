<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\WordPress\Form;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Form\FormFactoryInterface;

/**
 * Factory class used to render form controls on WordPress platform.
 * 
 * @since 1.9
 */
class FormFactory implements FormFactoryInterface
{
	/**
	 * @inheritDoc
	 */
	public function createField($options = [])
    {
        return new FormField($options);
    }
}
