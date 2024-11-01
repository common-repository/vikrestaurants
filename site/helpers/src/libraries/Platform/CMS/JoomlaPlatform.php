<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\CMS\Joomla\Dispatcher as JoomlaDispatcher;
use E4J\VikRestaurants\Platform\CMS\Joomla\Form\FormFactory as JoomlaFormFactory;
use E4J\VikRestaurants\Platform\CMS\Joomla\PaymentFactory as JoomlaPaymentFactory;
use E4J\VikRestaurants\Platform\CMS\Joomla\Uri as JoomlaUri;
use E4J\VikRestaurants\Platform\PlatformAware;

/**
 * Implements the Joomla platform interface.
 * 
 * @since 1.9
 */
class JoomlaPlatform extends PlatformAware
{
	/**
	 * @inheritDoc
	 */
	protected function createDispatcher()
	{
		return new JoomlaDispatcher;
	}

	/**
	 * @inheritDoc
	 */
	protected function createFormFactory()
	{
		return new JoomlaFormFactory;
	}

	/**
	 * @inheritDoc
	 */
	protected function createPaymentFactory()
	{
		return new JoomlaPaymentFactory;
	}

	/**
	 * @inheritDoc
	 */
	protected function createUri()
	{
		return new JoomlaUri;
	}
}
