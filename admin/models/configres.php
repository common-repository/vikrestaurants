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

VRELoader::import('models.configuration', VREADMIN);

/**
 * VikRestaurants restaurant configuration model.
 *
 * @since 1.9
 */
class VikRestaurantsModelConfigres extends VikRestaurantsModelConfiguration
{
	/**
	 * Hook identifier for triggers.
	 *
	 * @var string
	 */
	protected $hook = 'Configres';

	/**
	 * Validates and prepares the settings to be stored.
	 *
	 * @param 	array 	&$args  The configuration associative array.
	 *
	 * @return 	void
	 */
	protected function validate(&$args)
	{
		if (isset($args['defstatus']) && JHtml::fetch('vrehtml.status.isapproved', 'restaurant', $args['defstatus']))
		{
			// disable self confirmation in case the status is an auto-approval
			$args['selfconfirm'] = 0;
		}

		if (isset($args['averagetimestay']) && $args['averagetimestay'] < 5)
		{
			// do not save an invalid average time of stay
			unset($args['averagetimestay']);
		}

		if (isset($args['minuteintervals']))
		{
			// make sure the restaurant minute interval is not lower than 5 minutes
			$args['minuteintervals'] = max(5, (int) $args['minuteintervals']);
		}

		if (isset($args['tablocktime']) && $args['tablocktime'] < 5)
		{
			// do not save an invalid lock time
			unset($args['tablocktime']);
		}

		if (isset($args['askdeposit']) && $args['askdeposit'] == 0)
		{
			// deposit disabled, unset amount
			$args['resdeposit'] = 0;
		}

		if (isset($args['resdeposit']))
		{
			// the reservation deposit cannot be a negative number
			$args['resdeposit'] = max(0, $args['resdeposit']);
		}

		if (isset($args['minimumpeople']))
		{
			// the minimum number of participants cannot be lower than 1
			$args['minimumpeople'] = max(1, $args['minimumpeople']);
		}

		if (isset($args['maximumpeople']))
		{
			// the maximum number of participants cannot be lower than 2
			$args['maximumpeople'] = max(2, $args['maximumpeople']);
		}

		if (isset($args['minimumpeople']) && isset($args['maximumpeople']) && $args['minimumpeople'] >= $args['maximumpeople'])
		{
			// invalid capacity received, avoid saving
			unset($args['minimumpeople'], $args['maximumpeople']);
		}

		if (isset($args['safefactor']))
		{
			// the safe factor cannot be lower than 1
			$args['safefactor'] = max(1, $args['safefactor']);
		}

		if (isset($args['mailcustwhen']) && is_array($args['mailcustwhen']))
		{
			// stringify list of accepted status codes
			$args['mailcustwhen'] = json_encode($args['mailcustwhen']);
		}

		if (isset($args['mailoperwhen']) && is_array($args['mailoperwhen']))
		{
			// stringify list of accepted status codes
			$args['mailoperwhen'] = json_encode($args['mailoperwhen']);
		}

		if (isset($args['mailadminwhen']) && is_array($args['mailadminwhen']))
		{
			// stringify list of accepted status codes
			$args['mailadminwhen'] = json_encode($args['mailadminwhen']);
		}

		// validate customer e-mail template
		if (isset($args['mailtmpl']))
		{
			if (empty($args['mailtmpl']))
			{
				$args['mailtmpl'] = 'email_tmpl.php';
			}
			else
			{
				$args['mailtmpl'] = basename($args['mailtmpl']);
			}
		}

		// validate admin e-mail template
		if (isset($args['adminmailtmpl']))
		{
			if (empty($args['adminmailtmpl']))
			{
				$args['adminmailtmpl'] = 'admin_email_tmpl.php';
			}
			else
			{
				$args['adminmailtmpl'] = basename($args['adminmailtmpl']);
			}
		}

		// validate cancellation e-mail template
		if (isset($args['cancmailtmpl']))
		{
			if (empty($args['cancmailtmpl']))
			{
				$args['cancmailtmpl'] = 'cancellation_email_tmpl.php';
			}
			else
			{
				$args['cancmailtmpl'] = basename($args['cancmailtmpl']);
			}
		}

		if (isset($args['listablecols']) && is_array($args['listablecols']))
		{
			$listable_cols = [];

			// stringify reservations list columns
			foreach ($args['listablecols'] as $k => $v)
			{
				$tmp = explode(':', $v);

				if ($tmp[1] == 1)
				{
					$listable_cols[] = $tmp[0];
				} 
			}

			$args['listablecols'] = implode(',', $listable_cols);
		}

		if (isset($args['listablecf']) && is_array($args['listablecf']))
		{
			$listable_cols = [];

			// stringify reservations list custom fields
			foreach ($args['listablecf'] as $k => $v)
			{
				$tmp = explode(':', $v);

				if ($tmp[1] == 1)
				{
					$listable_cols[] = $tmp[0];
				} 
			}

			$args['listablecf'] = implode(',', $listable_cols);
		}
	}
}
