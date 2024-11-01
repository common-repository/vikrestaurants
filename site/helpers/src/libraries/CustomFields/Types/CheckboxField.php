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
 * VikRestaurants custom field checkbox handler.
 *
 * @since 1.9
 */
class CheckboxField extends Field
{
	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRCUSTOMFTYPEOPTION5');
	}

	/**
	 * @inheritDoc
	 */
	public function getReadableValue($value)
	{
		if ($value == 1
			|| $value == 'JYES'
			|| $value == \JText::translate('JYES'))
		{
			return \JText::translate('JYES');
		}

		return \JText::translate('JNO');
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData(array $data)
	{
		// fetch "checked" status
		$data['checked'] = !empty($data['value'])
			&& (
				$data['value'] == 1
				|| $data['value'] == 'JYES'
				|| $data['value'] == \JText::translate('JYES')
			);

		/**
		 * In case the checkbox is required, always uncheck it
		 * as the user needs to manually opt in every time for
		 * law compliance.
		 * 
		 * @since 1.9.1
		 */
		if ($this->get('required') == 1)
		{
			$data['checked'] = false;
		}

		// use the provided value or 1
		$data['value'] = $this->get('value', 1);

		return parent::getDisplayData($data);
	}
}
