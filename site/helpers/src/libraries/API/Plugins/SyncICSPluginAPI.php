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
 * Event used to generate a ICS string.
 * Acts as a subscription URL to keep external calendars up-to-date.
 *
 * @since 1.7
 * @since 1.9  Renamed from IcsSync.
 */
class SyncICSPluginAPI extends Event
{
	/**
	 * @inheritDoc
	 */
	protected function execute(array $args, Response $response)
	{
		$response->setStatus(1);

		if (!$args)
		{
			$input = \JFactory::getApplication()->input;
			
			// No payload found, recover arguments from request.
			// Get requested type (0: restaurant, 1: take-away).
			$args['type'] = $input->getUint('type', 0);
		}
		else
		{
			// make sure we have a valid type
			if (!isset($args['type']))
			{
				// use default one
				$args['type'] = 0;
			}
		}

		/**
		 * Use ICS export driver.
		 *
		 * @since 1.8
		 */
		\VRELoader::import('library.order.export.factory');

		try
		{
			// get ICS export driver
			$driver = \VREOrderExportFactory::getInstance('ics', $args['type'] == 0 ? 'restaurant' : 'takeaway', $args);
		}
		catch (\Exception $e)
		{
			// driver not found, register response
			$response->setStatus(0)->setContent($e->getMessage());

			// rethrow exception
			throw $e;
		}

		// download calendar
		$driver->download();
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		/**
		 * @todo translate
		 */
		return 'ICS Sync';
	}

	/**
	 * @inheritDoc
	 */
	public function getShortDescription()
	{
		/**
		 * @todo translate
		 */
		return 'Sync your calendars/applications with all your existing orders and reservations.';
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
		return \JLayoutHelper::render('apis.plugins.ics_sync', ['plugin' => $this]);
	}
}
