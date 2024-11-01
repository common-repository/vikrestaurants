<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * Adjusts the value of some settings.
 *
 * @since 1.9
 */
class ConfigurationFixer extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		$config = \VREFactory::getConfig();

		// Before the 1.9 version of VikRestaurants the setting used to detect the first available time
		// was collecting just an index, which were then multiplied by the selected interval.
		// From now on, this value directly collects the minutes to apply. Therefore, we need to manually
		// adjust that value after updating the software.
		$config->set('asapafter', $config->getUint('asapafter') * $config->getUint('tkminint'));

		// Before the 1.9 version of VikRestaurants the setting used to store the SMS template was 
		// containing the original text and the translations too. From now on the translations are
		// properly saved within the apposite database table used to translate the configuration
		// settings.
		$this->adjustSmsTemplate($config, 'smstmplcust');
		$this->adjustSmsTemplate($config, 'smstmpltkcust');

		// Before the 1.9 version of VikRestaurants, the listable custom fields were stored in a JSON string
		// containing the name of all the selected custom fields. In case the administrator updates the value
		// of a custom field, the latter was automatically ignored as the name didn't match anymore. For this
		// reason, instead of storing the names, we should store the ID of the fields.
		$this->adjustListableCustomFields($config, 'restaurant');
		$this->adjustListableCustomFields($config, 'takeaway');

		// Before the 1.9 version of VikRestaurants, the custom fields were always displayed with a sort of
		// "Material" style. We should force the system to keep using this style instead of the default one,
		// as the websites might be configured to work that way.
		$config->set('fields_layout_style', 'material');

		return true;
	}

	/**
	 * Moves the SMS template translations.
	 * 
	 * @param   mixed   $config
	 * @param   string  $param
	 * 
	 * @return  void
	 */
	protected function adjustSmsTemplate($config, string $param)
	{
		// decode saved templates
		$setting = $config->getArray($param);

		if (!$setting)
		{
			// no saved templates
			return;
		}

		$default = \VikRestaurants::getDefaultLanguage();

		// fetch the original message from the map
		if (isset($setting[$default]))
		{
			// preserve only the original text
			$config->set($param, $setting[$default]);

			// remove the original text to avoid saving it as a translation
			unset($setting[$default]);
		}

		$model = \JModelVRE::getInstance('langconfig');

		foreach ($setting as $lang => $text)
		{
			// register translation
			$model->save([
				'id'       => 0,
				'param'    => $param,
				'setting'  => $text,
				'tag'      => $lang,
			]);
		}
	}

	/**
	 * Adjusts the setting holding the listable custom fields.
	 * 
	 * @param   mixed   $config
	 * @param   string  $group
	 * 
	 * @return  void
	 */
	protected function adjustListableCustomFields($config, string $group)
	{
		$ids = [];

		$param = $group === 'restaurant' ? 'listablecf' : 'tklistablecf';

		// obtain a list containing all the stored values
		$listable = $config->getArray($param, []);

		// obtain all the custom fields
		$fields = \E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance();

		// filter fields by group
		if ($group === 'restaurant')
		{
			$fields = $fields->filter(new \E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter);
		}
		else
		{
			$fields = $fields->filter(new \E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter);
		}

		// iterate all the custom fields
		foreach ($fields as $field)
		{
			// check whether the current field name is included within the configuration setting
			if (in_array($field->langname, $listable))
			{
				// register the field ID
				$ids[] = $field->id;
			}
		}

		// commit the changes
		$config->set($param, implode(',', $ids));
	}
}
