<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Types;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\Field;

/**
 * VikRestaurants custom field HTML text handler.
 *
 * @since 1.9
 */
class HtmlField extends Field
{
	/**
	 * @inheritDoc
	 */
	public function getType()
	{
        // we don't need to translate this type
		return 'HTML';
	}

    /**
     * Returns the HTML of the field input.
     *
     * @param   array   $data  An array of display data.
     *
     * @return  string  The HTML of the input.
     */
    protected function getInput($data)
    {
        // the layout of the input is always equals to the provided HTML
        return $this->get('html', '');
    }
}
