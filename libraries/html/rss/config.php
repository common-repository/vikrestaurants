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

$config   = !empty($displayData['config'])   ? $displayData['config']   : null;
$channels = !empty($displayData['channels']) ? $displayData['channels'] : [];
$view     = $displayData['view'];

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">

		<!-- OPT IN - Checkbox -->

		<?php
		echo $view->formFactory->createField()
			->type('checkbox')
			->name('rss_optin_status')
			->checked($config['optin'])
			->label(__('Enable RSS Service', 'vikrestaurants'))
			->onchange('rssOptinValueChanged(this.checked)');
		?>

		<!-- DISPLAY DASHBOARD - Select -->

		<?php
		echo $view->formFactory->createField()
			->type('checkbox')
			->name('rss_display_dashboard')
			->checked($config['dashboard'])
			->label(__('Display on Dashboard', 'vikrestaurants'))
			->control([
				'class'   => 'rss-child-setting',
				'visible' => $config['optin'],
			]);
		?>

		<?php
		// allow channels management for PRO licenses
		if (VikRestaurantsLicense::isPro())
		{
			// iterate supported channels
			foreach ($channels as $label => $url)
			{
				$checked = in_array($url, (array) $config['channels']);

				echo $view->formFactory->createField()
					->type('checkbox')
					->name('rss_channel_' . md5($url))
					->checked($checked)
					->label(ucwords($label))
					->onchange('rssChannelValueChanged(this.checked, \'' . $url . '\')')
					->control([
						'class'   => 'rss-child-setting',
						'visible' => $config['optin'],
					]);

				if ($checked)
				{
					echo $view->formFactory->createField()
						->type('hidden')
						->name('rss_channel_url')
						->value($url)
						->multiple(true);
				}
			}
		}
		?>

	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		// toggle RSS settings according to the opt-in choice
		w.rssOptinValueChanged = (checked) => {
			if (checked) {
				$('.rss-child-setting').show();
			} else {
				$('.rss-child-setting').hide();
			}
		}

		// toggle RSS channel according to the checkbox status
		w.rssChannelValueChanged = (checked, url) => {
			// get existing input URL
			const urlInput = $('input[name="rss_channel_url[]"][value="' + url + '"]');

			if (checked && urlInput.length == 0) {
				$('#adminForm').append('<input type="hidden" name="rss_channel_url[]" value="' + url + '" />');
			} else {
				urlInput.remove();
			}
		}
	})(jQuery, window);
</script>