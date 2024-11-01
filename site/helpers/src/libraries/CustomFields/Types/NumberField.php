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
 * VikRestaurants custom field number handler.
 *
 * @since 1.10
 */
class NumberField extends Field
{
	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRCUSTOMFTYPEOPTION7');
	}

	/**
	 * @inheritDoc
	 */
	protected function extract()
	{
		// get field settings
		$settings = $this->getSettings();

		// extract value by using the parent class
		$value = (float) parent::extract($args);

		// if min setting exists, make sure the value is not lower
		if (isset($settings['min']) && strlen($settings['min']))
		{
			$value = max(array($value, (float) $settings['min']));
		}

		// if max setting exists, make sure the value is not higher
		if (isset($settings['max']) && strlen($settings['max']))
		{
			$value = min(array($value, (float) $settings['max']));
		}

		// if decimals are not supported, round the value
		if (empty($settings['decimals']))
		{
			$value = round($value);
		}

		return $value;
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData(array $data)
	{
		// get field settings
		$settings = $this->getSettings();

		// set input range
		$data['min'] = isset($settings['min']) ? $settings['min'] : '';
		$data['max'] = isset($settings['max']) ? $settings['max'] : '';
		// set input step
		$data['step'] = !empty($settings['decimals']) ? 'any' : 1;

		return parent::getDisplayData($data);
	}
}
