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
 * The APIs event (plugin) representation.
 * The classname of a plugin must follow the standard below:
 * e.g. File = plugin.php   		Class = Plugin
 * e.g. File = plugin_name.php   	Class = PluginName
 *
 * @since 1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\Event instead.
 */
abstract class EventAPIs extends E4J\VikRestaurants\API\Event
{
	/**
	 * Class constructor.
	 *
	 * @param  string  $name     The name of the event.
	 * @param  array   $options  A configuration array/object.
	 */
	public function __construct(string $name = '', $options = [])
	{
		parent::__construct($name, $options);

		$app = JFactory::getApplication();

		if ($app->isClient('administrator'))
		{
			// display warning to inform the user that this loading process is deprecated and should be
			// updated as soon as possible, because starting from the 1.11 version, the events will be
			// no more compatible.
			$app->enqueueMessage(
				sprintf(
					'The event [%s] implements a deprecated interface. You should update your integration before the release of the 1.11 version of VikRestaurants.',
					$name
				),
				'warning'
			);
		}
	}

	/**
	 * @inheritDoc
	 * 
	 * @since 1.9
	 */
	public function getShortDescription()
	{
		// get plugin description
		$description = (string) $this->getDescription();

		if ($description)
		{
			// split description line by line
			$chunks = preg_split("/(\R|<br\s*\/\s*>)/", $description);
			// remove empty lines
			$chunks = array_filter($chunks);
			// use only the first paragraph
			$description = strip_tags(array_shift($chunks));
		}

		return $description;
	}

	/**
	 * @inheritDoc
	 */
	final protected function execute(array $args, E4J\VikRestaurants\API\Response $response)
	{
		// create old response object for BC
		$responseBC = new ResponseAPIs;

		// trigger action with the deprecated method
		$buffer = $this->doAction($args, $responseBC);

		// copy the received response into the provided one
		$responseBC->copy($response);

		return $buffer;
	}

	/**
	 * The custom action that the event have to perform.
	 * This method should not contain any exit or die functions,
	 * otherwise the event won't be properly terminated.
	 *
	 * @param   array     $args      The provided arguments for the event.
	 * @param   Response  $response  The response object for admin.
	 *
	 * @return  mixed     The response to output or the error message (Error).
	 * 
	 * @deprecated 1.11  Without replacement. Used to for backward compatibility.
	 */
	abstract protected function doAction(array $args, ResponseAPIs &$response);
}
