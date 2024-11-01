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
class VikRestaurantsModelConfigtk extends VikRestaurantsModelConfiguration
{
	/**
	 * Hook identifier for triggers.
	 *
	 * @var string
	 */
	protected $hook = 'Configtk';

	/**
	 * Validates and prepares the settings to be stored.
	 *
	 * @param 	array 	&$args  The configuration associative array.
	 *
	 * @return 	void
	 */
	protected function validate(&$args)
	{
		if (isset($args['tkdefstatus']) && JHtml::fetch('vrehtml.status.isapproved', 'takeaway', $args['tkdefstatus']))
		{
			// disable self confirmation in case the status is an auto-approval
			$args['tkselfconfirm'] = 0;
		}

		if (isset($args['tklocktime']) && $args['tklocktime'] < 5)
		{
			// do not save an invalid lock time
			unset($args['tklocktime']);
		}

		if (isset($args['tkminint']))
		{
			// make sure the take-away minute interval is not lower than 5 minutes
			$args['tkminint'] = max(5, (int) $args['tkminint']);
		}

		if (empty($args['tkallowdate']))
		{
			// turn off min/max dates in case the date selection is disabled
			$args['tkmindate'] = 0;
			$args['tkmaxdate'] = 0;
		}
		else
		{
			// disable live orders in case the date selection is enabled
			$args['tkwhenopen'] = 0;
		}

		if (!empty($args['tkwhenopen']))
		{
			// turn off pre-orders in case of live orders
			$args['tkpreorder'] = 0;
		}

		if (!empty($args['enabledelivery']) && !empty($args['enablepickup']))
		{
			// both services have been enabled
			$args['deliveryservice'] = 2;
		}
		else if (!empty($args['enabledelivery']))
		{
			// delivery service enabled only
			$args['deliveryservice'] = 1;
			// force delivery as default service
			$args['tkdefaultservice'] = 'delivery';
		}
		else if (!empty($args['enablepickup']))
		{
			// takeaway service enabled only
			$args['deliveryservice'] = 0;
			// force takeaway as default service
			$args['tkdefaultservice'] = 'pickup';
		}

		unset($args['enabledelivery'], $args['enablepickup']);

		if (isset($args['dsprice']))
		{
			// delivery charge cannot offer a discount
			$args['dsprice'] = max(0, $args['dsprice']);
		}

		if (isset($args['tkmailcustwhen']) && is_array($args['tkmailcustwhen']))
		{
			// stringify list of accepted status codes
			$args['tkmailcustwhen'] = json_encode($args['tkmailcustwhen']);
		}

		if (isset($args['tkmailoperwhen']) && is_array($args['tkmailoperwhen']))
		{
			// stringify list of accepted status codes
			$args['tkmailoperwhen'] = json_encode($args['tkmailoperwhen']);
		}

		if (isset($args['tkmailadminwhen']) && is_array($args['tkmailadminwhen']))
		{
			// stringify list of accepted status codes
			$args['tkmailadminwhen'] = json_encode($args['tkmailadminwhen']);
		}

		// validate customer e-mail template
		if (isset($args['tkmailtmpl']))
		{
			if (empty($args['tkmailtmpl']))
			{
				$args['tkmailtmpl'] = 'takeaway_email_tmpl.php';
			}
			else
			{
				$args['tkmailtmpl'] = basename($args['tkmailtmpl']);
			}
		}

		// validate admin e-mail template
		if (isset($args['tkadminmailtmpl']))
		{
			if (empty($args['tkadminmailtmpl']))
			{
				$args['tkadminmailtmpl'] = 'takeaway_admin_email_tmpl.php';
			}
			else
			{
				$args['tkadminmailtmpl'] = basename($args['tkadminmailtmpl']);
			}
		}

		// validate cancellation e-mail template
		if (isset($args['tkcancmailtmpl']))
		{
			if (empty($args['tkcancmailtmpl']))
			{
				$args['tkcancmailtmpl'] = 'takeaway_cancellation_email_tmpl.php';
			}
			else
			{
				$args['tkcancmailtmpl'] = basename($args['tkcancmailtmpl']);
			}
		}

		// validate review e-mail template
		if (isset($args['tkreviewmailtmpl']))
		{
			if (empty($args['tkreviewmailtmpl']))
			{
				$args['tkreviewmailtmpl'] = 'takeaway_review_email_tmpl.php';
			}
			else
			{
				$args['tkreviewmailtmpl'] = basename($args['tkreviewmailtmpl']);
			}
		}

		// validate stock e-mail template
		if (isset($args['tkstockmailtmpl']))
		{
			if (empty($args['tkstockmailtmpl']))
			{
				$args['tkstockmailtmpl'] = 'takeaway_stock_email_tmpl.php';
			}
			else
			{
				$args['tkstockmailtmpl'] = basename($args['tkstockmailtmpl']);
			}
		}

		if (isset($args['tklistablecols']) && is_array($args['tklistablecols']))
		{
			$listable_cols = [];

			// stringify order list columns
			foreach ($args['tklistablecols'] as $k => $v)
			{
				$tmp = explode(':', $v);

				if ($tmp[1] == 1)
				{
					$listable_cols[] = $tmp[0];
				} 
			}

			$args['tklistablecols'] = implode(',', $listable_cols);
		}

		if (isset($args['tklistablecf']) && is_array($args['tklistablecf']))
		{
			$listable_cols = [];

			// stringify order list custom fields
			foreach ($args['tklistablecf'] as $k => $v)
			{
				$tmp = explode(':', $v);

				if ($tmp[1] == 1)
				{
					$listable_cols[] = $tmp[0];
				} 
			}

			$args['tklistablecf'] = implode(',', $listable_cols);
		}
	}
}
