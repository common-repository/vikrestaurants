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

$feed = !empty($displayData['feed']) ? $displayData['feed'] : null;

// prepare modal footer
$footer  = '<div class="pull-left">';
$footer .= '<input type="checkbox" value="1" id="rss-feed-remind">';
$footer .= '<label for="rss-feed-remind" style="line-height: 13px;">' . __('Remind me later') . '</label>';
$footer .= '</div>';
$footer .= '<button type="button" class="btn btn-danger" id="rss-feed-dismiss">' . __('Don\'t show again', 'vikrestaurants') . '</button>';

// prepare modal to display opt-in
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-rss-feed',
	[
		'title'       => '<i class="fas fa-rss-square"></i> ' . $feed->category . ' - ' . $feed->title,
		'closeButton' => true,
		'keyboard'    => false,
		'top'         => true,
		'width'       => 70,
		'height'      => 80,
		'footer'      => $footer,
	],
	$feed->content
);

?>

<style>
	#jmodal-rss-feed img {
		max-width: 100%;
	}
</style>

<script>
	(function($) {
		'use strict';

		$(function() {
			let dismissed = false;
		
			if (typeof localStorage !== 'undefined') {
				dismissed = localStorage.getItem('vikrestaurants.rss.dismissed.<?php echo $feed->id; ?>') ? true : false;
			}

			if (!dismissed) {
				// open modal with a short delay
				setTimeout(() => {
					wpOpenJModal('rss-feed');
				}, 1500);
			}

			$('#rss-feed-remind').on('change', function() {
				const btn = $('#rss-feed-dismiss');

				if ($(this).is(':checked')) {
					btn.removeClass('btn-danger').addClass('btn-success');
					btn.text('<?php echo addslashes(__('Close')); ?>');
				} else {
					btn.removeClass('btn-success').addClass('btn-danger');
					btn.text('<?php echo addslashes(__('Don\'t show again', 'vikrestaurants')); ?>');
				}
			});

			$('#rss-feed-dismiss').on('click', function() {
				if ($(this).prop('disabled')) {
					// already submitted
					return false;
				}

				$(this).prop('disabled', true);

				// prepare request
				let url  = 'admin-ajax.php?action=vikrestaurants&task=rss.';
				let data = {
					id: '<?php echo $feed->id; ?>',
				};

				// look for reminder option
				if ($('#rss-feed-remind').is(':checked')) {
					url += 'remind';
					// show again in 2 hours
					data.delay = 120;
				} else {
					url += 'dismiss';
				}

				doAjax(
					url,
					data,
					(resp) => {
						// auto-dismiss on save
						wpCloseJModal('rss-feed');
					},
					(error) => {
						// alert error message
						alert(error.responseText || Joomla.JText._('CONNECTION_LOST'));

						// avoid to spam the dialog again and again at every page load
						if (typeof localStorage !== 'undefined') {
							localStorage.setItem('vikrestaurants.rss.dismissed.<?php echo $feed->id; ?>', 1);
						}

						// auto-dismiss on failure
						wpCloseJModal('rss-feed');
					}
				);
			});
		});
	})(jQuery);
</script>