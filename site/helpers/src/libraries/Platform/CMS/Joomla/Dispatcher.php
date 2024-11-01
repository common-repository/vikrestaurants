<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\Joomla;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Event\EventResponse;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Implements the event dispatcher interface for the Joomla platform.
 * 
 * @since 1.9
 */
class Dispatcher implements DispatcherInterface
{
	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		// auto-import the default extension groups
		\JPluginHelper::importPlugin('e4j');
		\JPluginHelper::importPlugin('vikrestaurants');
	}

	/**
	 * @inheritDoc
	 */
	public function trigger(string $event, array $args = [])
	{
		$this->filter($event, $args);
	}

	/**
	 * @inheritDoc
	 */
	public function filter(string $event, array $args = [])
	{
		return new EventResponse(\VREFactory::getEventDispatcher()->trigger($event, $args));
	}
}
