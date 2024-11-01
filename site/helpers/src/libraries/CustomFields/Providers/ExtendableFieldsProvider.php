<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Providers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\DatasetProvider;
use E4J\VikRestaurants\CustomFields\Field;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * An abstract provider that allows the extendability of the registered fields.
 * 
 * @since 1.9
 */
abstract class ExtendableFieldsProvider implements DatasetProvider
{
	/** @var DispatcherInterface */
	protected $dispatcher;

	/**
	 * Class constructor.
	 * 
	 * @param  DispatcherInterface  $dispatcher
	 */
	public function __construct(DispatcherInterface $dispatcher = null)
	{
		if ($dispatcher)
		{
			$this->dispatcher = $dispatcher;
		}
		else
		{
			// dispatcher not provider, use the default one
			$this->dispatcher = \VREFactory::getPlatform()->getDispatcher();
		}
	}

	/**
	 * Helper methods used to extend the provided fields with the ones returned by a custom plugin.
	 * 
	 * @param   Field[]  &$fields    The array of fields to extend.
	 * @param   string   $eventName  The name of the event to trigger.
	 * @param   array    $eventArgs  The array of arguments to pass.
	 * 
	 * @return  void
	 */
	final protected function extendFields(array &$fields, string $eventName, array $eventArgs = [])
	{
		/**
		 * Trigger event to let the plugins add custom fields within the user registration form.
		 * The array to return must include instances of the `E4J\VikRestaurants\CustomFields\Field` class
		 * or associative arrays/objects containing the details of the field to create.
		 *
		 * @return  array   An array of custom fields.
		 *
		 * @since   1.9
		 */
		$result = $this->dispatcher->filter($eventName, $eventArgs);

		/** @var E4J\VikRestaurants\Event\EventResponse $result */

		$flatten = [];

		// flatten the returned fields
		foreach ($result as $data)
		{
			// check if we have a linear array
			if (is_array($data) && (array_keys($data) === range(0, count($data) - 1)))
			{
				$flatten = array_merge($flatten, $data);
			}
			else
			{
				$flatten[] = $data;
			}
		}

		$addons = [];

		foreach ($flatten as $customField)
		{
			if ($customField instanceof Field)
			{
				// field instance provider
				$addons[] = $customField;
			}
			else if (is_array($customField) || is_object($customField))
			{
				// instantiate the field with the provided data
				$addons[] = Field::getInstance($customField);
			}
		}

		// append the fields returned by the plugin to the default ones
		$fields = array_merge($fields, $addons);
	}
}
