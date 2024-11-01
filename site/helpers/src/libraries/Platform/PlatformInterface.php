<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Declares all the helper methods that may differ between every supported platform.
 * 
 * @since 1.9
 */
interface PlatformInterface
{
	/**
	 * Returns the event dispatcher instance.
	 * 
	 * @return 	E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface
	 */
	public function getDispatcher();

	/**
	 * Returns the form fields factory instance.
	 * 
	 * @return 	E4J\VikRestaurants\Platform\Form\FormFactoryInterface
	 */
	public function getFormFactory();

	/**
	 * Returns the payment factory instance.
	 * 
	 * @return 	E4J\VikRestaurants\Platform\Payment\PaymentFactoryInterface
	 */
	public function getPaymentFactory();

	/**
	 * Returns the URI helper instance.
	 *
	 * @return 	E4J\VikRestaurants\Platform\Uri\UriInterface
	 */
	public function getUri();
}
