<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.rss
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

// prepare modal to display opt-in
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-rss-optin',
	[
		'title'       => '<i class="fas fa-rss-square"></i> ' . __('VikRestaurants - RSS Opt in', 'vikrestaurants'),
		'closeButton' => false,
		'keyboard'    => false,
		'top'         => true,
		'width'       => 70,
		'height'      => 80,
		'footer'      => '<button type="button" class="btn btn-success" id="rss-optin-save">' . __('Save') . '</button>',
	],
	$this->sublayout('modal')
);

?>

<script>
	(function($) {
		'use strict';

		$(function() {
			let aborted = false;
			
			if (typeof localStorage !== 'undefined') {
				aborted = localStorage.getItem('vikrestaurants.rss.aborted') ? true : false;
			}

			if (!aborted) {
				// open modal with a short delay
				setTimeout(() => {
					wpOpenJModal('rss-optin');
				}, 1500);
			}

			$('#rss-optin-save').on('click', function() {
				if ($(this).prop('disabled')) {
					// already submitted
					return false;
				}

				$(this).prop('disabled', true);

				// check opt-in status
				let status = $('input[name="rss_optin_status"]').is(':checked') ? 1 : 0;

				// make AJAX request
				doAjax(
					'admin-ajax.php?action=vikrestaurants&task=rss.optin',
					{
						status: status,
					},
					(resp) => {
						// auto-dismiss on save
						wpCloseJModal('rss-optin');
					},
					(error) => {
						// alert error message
						alert(error.responseText || Joomla.JText._('CONNECTION_LOST'));

						// avoid to spam the dialog again and again at every page load
						if (typeof localStorage !== 'undefined') {
							localStorage.setItem('vikrestaurants.rss.aborted', 1);
						}

						// auto-dismiss on failure
						wpCloseJModal('rss-optin');
					}
				);
			});
		});
	})(jQuery);
</script>