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
 * VikRestaurants custom field password handler.
 *
 * @since 1.9
 */
class PasswordField extends Field
{
	/**
	 * @inheritDoc
	 */
	public function getType()
	{
        // we don't need to translate this type
		return 'Password';
	}

    /**
     * @inheritDoc
     */
    protected function getDisplayData(array $data)
    {
        // password fields should never have the value autocompleted
        $data['value'] = '';

        return parent::getDisplayData($data);
    }
}
