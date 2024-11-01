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
 * @var  string  $suffix  An optional suffix to support different configurations.
 */
extract($displayData);

$suffix = isset($suffix) ? $suffix : '';

?>

<script>
	(function($) {
		'use strict';

		$(function() {
			// handle main configuration nav buttons
			$('.vretabli').on('click', function() {
				if ($(this).hasClass('vreconfigtabactive')) {
					// pane already selected
					return false;
				}

				$('.vretabli').removeClass('vreconfigtabactive');
				$(this).addClass('vreconfigtabactive');

				let tab = $(this).data('id');
				
				$('.vretabview').hide();
				$('#vretabview' + tab).show();

				/**
				 * Store active tab in a cookie and keep it there until the session expires.
				 *
				 * @since 1.8.3
				 */
				document.cookie = 'vikrestaurants.config<?php echo $suffix; ?>.tab=' + tab + '; path=/';
			});

			// create lambda to register the selected tab within a cookie
			const cacheActiveTab = (pane, tab) => {
				let paneId = $(pane).attr('id').replace(/^vretabview/, '');
				let tabId  = $(tab).data('id');

				document.cookie = 'vikrestaurants.config<?php echo $suffix; ?>.tab' + paneId + '=' + tabId + '; path=/';
			};

			// handle configuration panel nav buttons
			$('.vretabview .config-panel-subnav li').on('click', function() {
				if ($(this).hasClass('active')) {
					// pane already selected
					return false;
				}

				// back to parent tab
				const pane = $(this).closest('.vretabview');

				pane.find('.config-panel-subnav li').removeClass('active');
				pane.find('.config-panel-tabview-inner').hide();

				$(this).addClass('active');
				pane.find('.config-panel-tabview-inner')
					.filter('[data-id="' + $(this).data('id') + '"]')
						.show();

				cacheActiveTab(pane, this);
			});

			// check if the URL requested a specific setting
			if (document.location.hash) {
				// get setting input (starts with the specified hash)
				const input = $('*[name^="' + document.location.hash.replace(/^#/, '') + '"]').first();

				if (!input.length) {
					// setting not found
					return;
				}

				// extract fieldset ID
				const idFieldset = input.closest('.config-panel-tabview-inner[data-id]').data('id');

				// find tab view to which the input belong
				const tabView = input.closest('.vretabview');

				// extract tabView index from ID
				const matches = tabView.attr('id').match(/^vretabview(\d+)$/);

				if (matches && matches.length) {
					// activate the tab view of the input
					$('.vretabli[data-id="' + matches[1] + '"]').trigger('click');
					// active the inner fieldset
					$('.config-panel-subnav li[data-id="' + idFieldset + '"]').trigger('click');
					// set the focus to the input
					$(input).focus();
					// animate to the input position
					$('html, body').animate({ scrollTop: input.offset().top - 200 });
				}
			}

			// trigger "click" of the selected menu item to notify any attached subscribers
			$('.vretabview .config-panel-subnav li.active').trigger('click');
		});
	})(jQuery);
</script>