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

JHtml::fetch('formbehavior.chosen');

?>

<style>
	div#customizer-toolbar {
		flex-basis: 100%;
	}
</style>

<div id="customizer-toolbar" class="hidden-phone">

	<div class="btn-toolbar">

		<div class="btn-group pull-left preview-related-field" style="<?php echo $this->filters['preview_status'] ? '' : 'display: none;'; ?>">
			<select id="customizer-action-page">
				<?php echo JHtml::fetch('select.options', $this->menuItems, 'value', 'text', $this->filters['preview_page']); ?>
			</select>
		</div>

		<div class="btn-group pull-right">
			<button type="button" class="btn" id="customizer-action-toggle"><?php echo JText::translate('VRE_CUSTOMIZER_TOGGLE_PREVIEW'); ?></button>
		</div>

	</div>

</div>

<script>
	(function($) {
		'use strict';

		$(function() {
			VikRenderer.chosen('#customizer-toolbar');

			const expDate = new Date();
			expDate.setMonth(expDate.getMonth() + 1);

			$('#customizer-action-toggle').on('click', () => {
				const status = toggleCustomizerPreview();

				// register status in a cookie
				document.cookie = 'vikrestaurants.customizer.preview.status=' + status + '; expires=' + expDate.toUTCString() + '; path=/; SameSite=Lax';
			});

			$('#customizer-action-page').on('change', function() {
				// get selected URL
				const url = $(this).val();

				// switch preview URL
				changeCustomizerPreviewPage(url, true);

				// register URL in a cookie
				document.cookie = 'vikrestaurants.customizer.preview.page=' + url + '; expires=' + expDate.toUTCString() + '; path=/; SameSite=Lax';
			});

			onInstanceReady(() => {
				return window.configObserver;
			}).then(() => {
				window.configObserver.exclude('#customizer-action-page');
			});
		});
	})(jQuery);
</script>