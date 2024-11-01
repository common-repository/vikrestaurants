<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

?>

<script>
	(function($, w) {
		'use strict';

		let vikwp_running = false;

		w.vikWpValidateLicenseKey = () => {
			// request
			let lickey = $('#lickey').val();

			if (vikwp_running || !lickey.length) {
				// prevent double submission until request is over
				return;
			}

			// start running
			vikWpStartValidation();

			doAjax('admin-ajax.php?action=vikrestaurants&task=license.validate', {
				key: lickey
			}, (resp) => {
				vikWpStopValidation();

				// redirect to getpro view
				document.location.href = 'admin.php?page=vikrestaurants&view=getpro';
			}, (err) => {
				vikWpStopValidation();

				// raise error with a short delay to complete loading animation
				// before prompting the alert with the error
				setTimeout(() => {
					alert(err.responseText);
				}, 32);
			});
		}

		const vikWpStartValidation = () => {
			vikwp_running = true;
			$('#vikwpvalidate').prepend('<i class="fas fa-sync-alt"></i>');
		}

		const vikWpStopValidation = () => {
			vikwp_running = false;
			$('#vikwpvalidate').find('i').remove();
		}

		$(function() {
			$('#lickey').keyup(function() {
				$(this).val($(this).val().trim());
			});

			$('#lickey').keypress((e) => {
				if (e.which == 13) {
					// enter key code pressed, run the validation
					vikWpValidateLicenseKey();
					return false;
				}
			});
		});
	})(jQuery, window);
</script>