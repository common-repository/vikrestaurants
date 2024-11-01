<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants custom fields factory.
 *
 * @since 1.9
 */
final class Factory
{
	/**
	 * Returns a list of supported custom fields types.
	 *
	 * @return  array
	 * 
	 * @throws  \Exception
	 */
	public static function getSupportedTypes()
	{
		$types = [];

		// define a list of types that should be excluded, as we don't want to allow the customers
		// to create new custom fields of "hidden" or "HTML" type
		$excluded = ['hidden', 'html'];

		// load all files inside types folder
		$files = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Types' . DIRECTORY_SEPARATOR . '*.php');

		foreach ($files as $file)
		{
			// extract type from file name
			$type = strtolower(preg_replace("/Field\.php$/i", '', basename($file)));

			if (in_array($type, $excluded))
			{
				// ignore type
				continue;
			}

			try
			{
				// try to instantiate the type
				$field = Field::getInstance($type);

				// attach type to list
				$types[$type] = $field->getType();
			}
			catch (\Exception $e)
			{
				// catch error and go ahead
			}
		}

		/**
		 * Trigger hook to allow external plugins to support custom types.
		 * New types have to be appended to the given associative array.
		 * The key of the array is the unique ID of the type, the value is
		 * a readable name of the type.
		 *
		 * @param   array  &$types  An array of types.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadCustomFieldsTypes', [&$types]);

		// sort types by ascending name and preserve keys
		asort($types);

		return $types;
	}

	/**
	 * Returns a list of supported custom fields services.
	 * 
	 * @param   bool   $objects  True to return the objects, false to return the service name only.
	 *
	 * @return  array
	 * 
	 * @throws  \Exception
	 */
	public static function getSupportedServices($objects = false)
	{
		$services = [];

		// load all files inside types folder
		$files = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . '*.php');

		foreach ($files as $file)
		{
			// extract service from file name
			$service = strtolower(preg_replace("/Service\.php$/i", '', basename($file)));

			if ($service === 'null')
			{
				// always ignore Null Pointer pattern
				continue;
			}

			/**
			 * Trigger hook to allow external plugins to include new services for the custom
			 * fields that have been implemented out of this project. Plugins must include
			 * here the file holding the class of the service.
			 *
			 * @param   string  $service  The requested custom field service.
			 *
			 * @return  string  The classname of the object.
			 *
			 * @since   1.9
			 */
			$result = \VREFactory::getPlatform()->getDispatcher()->filter('onLoadCustomFieldService', [$service]);

			/** @var E4J\VikRestaurants\Event\EventResponse $result */

			// take the first available one
			$classname = $result->first();

			if (!$classname)
			{
				// construct service class name
				$classname = 'E4J\\VikRestaurants\\CustomFields\\Services\\' . ucfirst($service) . 'Service';
			}

			if (class_exists($classname))
			{
				/** @var E4J\VikRestaurants\CustomFields\FieldService */
				$serviceInstance = new $classname;

				// make sure we have a valid instance
				if ($serviceInstance instanceof FieldService)
				{
					// attach service instance to list
					$services[$service] = $serviceInstance;
				}
			}
		}

		/**
		 * Trigger hook to allow external plugins to support custom services.
		 * New services have to be appended to the given associative array.
		 * The key of the array is the unique ID of the service, the value is
		 * a readable name of the service.
		 *
		 * @param   array  &$services  An array of services.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadCustomFieldsServices', [&$services]);

		// sort services by ascending name and preserve keys
		asort($services);

		if (!$objects)
		{
			// requested only the name of the services
			$services = array_map(function($serviceInstance)
			{
				return $serviceInstance->getName();
			}, $services);
		}

		return $services;
	}

	/**
	 * Returns a list of supported rules.
	 *
	 * @return  array
	 * 
	 * @throws  \Exception
	 */
	public static function getSupportedRules()
	{
		$rules = [];

		// load all files inside rules folder
		$files = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Rules' . DIRECTORY_SEPARATOR . '*.php');

		foreach ($files as $file)
		{
			// extract rule from file name
			$rule = strtolower(preg_replace("/Rule\.php$/i", '', basename($file)));

			try
			{
				// try to instantiate the rule
				$rule = FieldRule::getInstance($rule);

				// attach rule to list
				$rules[$rule->getID()] = $rule->getName();
			}
			catch (\Exception $e)
			{
				// catch error and go ahead
			}
		}

		/**
		 * Trigger hook to allow external plugins to support custom rules.
		 * New rules have to be appended to the given associative array.
		 * The key of the array is the unique ID of the rule, the value is
		 * a readable name of the rule.
		 *
		 * @param   array  &$rules  An array of rules.
		 *
		 * @return  void
		 *
		 * @since   1.9
		 */
		\VREFactory::getPlatform()->getDispatcher()->trigger('onLoadCustomFieldsRules', [&$rules]);

		// sort rules by ascending name
		asort($rules);

		return $rules;
	}

	/**
	 * Dispatches a field rule.
	 *
	 * @param   Field  $field  The custom field instance.
	 * @param   mixed  $value  The value of the field set in request.
	 * @param   array  &$args  The array data to fill-in in case of
	 *                         specific rules (name, e-mail, etc...).
	 *
	 * @return  void
	 */
	public static function dispatchRule(Field $field, $value, array &$args)
	{
		if (!$field->get('rule'))
		{
			// missing rule, do not go ahead...
			return;
		}

		/**
		 * Trigger hook to allow external plugins to dispatch a custom rule.
		 * It is possible to access the rule of the field with:
		 * $field->get('rule');
		 *
		 * @param   Field  $field  The custom field instance.
		 * @param   mixed  $value  The value of the field set in request.
		 * @param   array  &$args  The array data to fill-in in case of
		 *                         specific rules (name, e-mail, etc...).
		 *
		 * @return  bool   True to avoid dispatching the default system rules.
		 *
		 * @since   1.9
		 */
		$result = \VREFactory::getPlatform()->getDispatcher()->filter('onDispatchCustomFieldRule', [$field, $value, &$args]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		if ($result->isTrue())
		{
			// do not need to go ahead, a plugin did all the needed stuff
			return;
		}

		try
		{
			// create rule instance
			$rule = FieldRule::getInstance($field->get('rule'));
		}
		catch (\Exception $e)
		{
			// the rule has been probably added by a third party plugin, which does not
			// need to dispatch a custom action
			return null;
		}

		// dispatch the rule
		$rule->dispatch($value, $args, $field);
	}

	/**
	 * Renders a field rule.
	 *
	 * @param   Field  $field  The custom field instance.
	 * @param   array  &$args  The array data to fill-in in case of
	 *                         specific rules (name, e-mail, etc...).
	 *
	 * @return  void
	 */
	public static function renderRule(Field $field, array &$data)
	{
		if (!$field->get('rule'))
		{
			// missing, rule do not go ahead...
			return;
		}

		/**
		 * Trigger hook to allow external plugins to manipulate the data to
		 * display or the type of layout to render. In case one of the attached
		 * plugins returned a string, then the field will use it as HTML in
		 * place of the default layout.
		 *
		 * It is possible to access the rule of the field with:
		 * $field->get('rule');
		 *
		 * @param   Field  $field  The custom field instance.
		 * @param   array  &$data  An array of display data.
		 *
		 * @return  mixed  The new layout of the field. Do not return anything
		 *                 to keep using the layout defined by the field.
		 *
		 * @since   1.9
		 */
		$result = \VREFactory::getPlatform()->getDispatcher()->filter('onRenderCustomFieldRule', [$field, &$data]);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		// implode list of returned HTML
		$input = implode("\n", $result->toArray());

		if ($input)
		{
			// return the input fetched by the plugin
			return $input;
		}

		try
		{
			// create rule instance
			$rule = FieldRule::getInstance($field->get('rule'));
		}
		catch (\Exception $e)
		{
			// the rule has been probably added by a third party plugin, which does not
			// need to display a custom input
			return null;
		}

		// render the rule
		return $rule->render($data, $field);
	}
}
