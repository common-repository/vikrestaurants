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
 * Reservation codes rule encapsulation.
 *
 * @since 1.8
 * @deprecated 1.10  Use the E4J\VikRestaurants\ReservationCodes\CodeRule instead.
 */
abstract class ResCodesRule extends E4J\VikRestaurants\ReservationCodes\CodeRule
{
	/**
	 * @inheritDoc
	 */
	public function __construct(array $options = [])
	{
		parent::__construct($options);

		$app = JFactory::getApplication();

		if ($app->isClient('administrator'))
		{
			// display warning to inform the user that this loading process is deprecated and should be
			// updated as soon as possible, because starting from the 1.10 version, this class will be
			// no more compatible
			$app->enqueueMessage(
				sprintf(
					'The [%s] code implements a deprecated interface. You should update your integration before the release of the 1.10 version of VikRestaurants.',
					get_class($this)
				),
				'warning'
			);
		}
	}
}
