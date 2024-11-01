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
 * Layout variables
 * -----------------
 * @var  int       $timer   The timeout interval in seconds.
 * @var  int|null  $itemid  An optional menu item ID.
 */
extract($displayData);

if (empty($timer))
{
	// use default timer interval (1 minute)
	$timer = 60;
}

if (!isset($itemid))
{
	// use current itemid
	$itemid = JFactory::getApplication()->input->get('Itemid', null, 'uint');
}

$vik = VREApplication::getInstance();
?>

<script>
	(function($, w) {
		'use strict';

		if (typeof w.WIDGET_PREFLIGHTS === 'object') {
			// scripts already loaded
			return;
		}

		/**
		 * A lookup of preflights to be used before refreshing
		 * the contents of the widgets.
		 *
		 * If needed, a widget can register its own callback
		 * to be executed before the AJAX request is started.
		 *
		 * The property name MUST BE equals to the ID of 
		 * the widget that is registering its callback.
		 *
		 * @var object
		 */
		w.WIDGET_PREFLIGHTS = {};

		/**
		 * A lookup of callbacks to be used when refreshing
		 * the contents of the widgets.
		 *
		 * If needed, a widget can register its own callback
		 * to be executed once the AJAX request is completed.
		 *
		 * The property name MUST BE equals to the ID of 
		 * the widget that is registering its callback.
		 *
		 * @var object
		 */
		w.WIDGET_CALLBACKS = {};

		let dashboardThread;
		let dashboardThreadStartTime;
		let dashboardThreadInterval = <?php echo $timer * 1000; ?>;

		/**
		 * A lookup used to hold the configuration of the
		 * supported widgets.
		 * 
		 * @var object
		 */
		let widgets = {};

		w.registerDashboardWidget = (id, group, config) => {
			widgets[id] = {
				config: config,
				group: group,
			};
		}

		w.startDashboardListener = (ms) => {
			// refresh dashboard every minute
			dashboardThread = setTimeout(refreshDashboardListener, ms ? ms : dashboardThreadInterval);
		}

		w.stopDashboardListener = () => {
			// clear dashboard thread
			clearTimeout(dashboardThread);
		}

		w.refreshDashboardListener = () => {
			if (dashboardThread) {
				clearTimeout(dashboardThread);
			}

			dashboardThreadStartTime = new Date();

			Object.keys(widgets).forEach((id) => {
				updateWidgetContents(id);
			});

			startDashboardListener();
		}

		w.waitListenerForAction = (promise) => {
			// freeze current time
			let now = new Date();

			// stop dashboard listener
			stopDashboardListener();

			// calculate remaining time since the last execution
			let remaining = Math.abs(dashboardThreadInterval - Math.floor(now - dashboardThreadStartTime));

			// wait until the promise ends
			promise.finally(() => {
				// restart thread	
				startDashboardListener(remaining);
			});
		}

		/**
		 * A pool containing the active AJAX requests for each
		 * widget, so that we can abort an existing request
		 * before launching a new one.
		 *
		 * @var object
		 */
		w.CHARTS_REQUESTS_POOL = {};

		w.updateWidgetContents = (id, config) => {
			// abort any existing request already made for this widget
			if (CHARTS_REQUESTS_POOL.hasOwnProperty(id)) {
				CHARTS_REQUESTS_POOL[id].abort();
			}

			if (typeof config !== 'object') {
				config = widgets[id]?.config || {};
			}

			const group = widgets[id]?.group || '';

			// keep a reference to the widget
			const box = $('#widget-' + id);

			// get widget class
			const widget = box.data('widget');

			// prepare request data
			Object.assign(config, {
				id:     id,
				widget: widget,
				group:  group,
			});

			console.log(config);

			// hide generic error message
			$(box).find('.widget-error-box').hide();
			// show widget body
			$(box).find('.widget-body').show();

			if (WIDGET_PREFLIGHTS.hasOwnProperty(id)) {
				// let the widget prepares the contents without
				// waiting for the request completion
				WIDGET_PREFLIGHTS[id](box, config);
			}

			// make request to load widget dataset
			const xhr = UIAjax.do(
				'<?php echo $vik->ajaxUrl('index.php?option=com_vikrestaurants&task=oversight.loadwidgetdata&tmpl=component' . ($itemid ? '&Itemid=' . $itemid : ''), false); ?>',
				config,
				(resp) => {
					// delete request from pool
					delete CHARTS_REQUESTS_POOL[id];

					// check if the widget registered its own update method
					if (WIDGET_CALLBACKS.hasOwnProperty(id)) {
						// let the widget callback finalizes the update
						WIDGET_CALLBACKS[id](box, resp, config);
					} else {
						// replace widget body
						$(box).find('.widget-body').html(resp);
					}
				},
				(error) => {
					// delete request from pool
					delete CHARTS_REQUESTS_POOL[id];

					// hide widget body
					$(box).find('.widget-body').hide();
					// show generic error message
					$(box).find('.widget-error-box').show();
				}
			);

			// update request pool
			CHARTS_REQUESTS_POOL[id] = xhr;
		}

		$(function() {
			// immediately load contents
			refreshDashboardListener();
		});
	})(jQuery, window);
</script>