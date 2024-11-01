<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API\Plugins;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\Event;
use E4J\VikRestaurants\API\Response;

/**
 * Event used to perform a test connection between the caller and this end-point.
 *
 * @since 1.7
 * @since 1.9  Renamed from ConnectionPing.
 */
class ConnectionPingPluginAPI extends Event
{
	/**
	 * @inheritDoc
	 */
	protected function execute(array $args, Response $response)
	{
		// connection ping done correctly
		$response->setStatus(1);

		/**
		 * Include some details about the program, 
		 * such as the version and the platform id.
		 *
		 * @since 1.8.4
		 */	

		// prepare response
		$obj = new \stdClass;
		$obj->status   = 1;
		$obj->version  = VIKRESTAURANTS_SOFTWARE_VERSION;
		$obj->platform = \VersionListener::getPlatform();

		/**
		 * Let the application framework safely output the response.
		 *
		 * @since 1.8.4
		 */
		return $obj;
	}

	/**
	 * @inheritDoc
	 */
	public function alwaysAllowed()
	{
		// the plugin is always allowed
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		/**
		 * @todo translate
		 */
		return 'Connection Ping';
	}

	/**
	 * @inheritDoc
	 */
	public function getShortDescription()
	{
		/**
		 * @todo translate
		 */
		return 'This plugin is needed to verify the connection between the application client and the server.';
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		/**
		 * Read the description HTML from a layout.
		 *
		 * @since 1.8
		 */
		return \JLayoutHelper::render('apis.plugins.connection_ping', ['plugin' => $this]);
	}
}
