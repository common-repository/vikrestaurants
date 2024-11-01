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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.fontawesome');

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewOrderinfo". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$this->addons = $this->onDisplayView();

?>

<style>

	/* modal content pane */

	.contentpane.component {
		padding: 0 10px;
		height: 100%;
		/* do not scroll */
		overflow: hidden;
	}

	/* do not use scroll on devices smaller than 1440px */

	@media screen and (max-width: 1439px) {
		.order-container .order-left-box,
		.order-container .order-right-box {
			overflow-y: scroll;
			height: 100%;
		}
		.order-container .order-left-box .order-left-bottom-box,
		.order-container .order-right-box .order-status-history {
			overflow-y: hidden;
			height: auto;
			max-height: 100%;
			margin-bottom: 12px;
		}
		.order-container .order-right-box .order-payment-details {
			height: auto;
		}
	}

</style>

<!-- container -->

<div class="order-container">

	<!-- left box : order details, customer info, order items -->

	<div class="order-left-box">

		<!-- top box : order details, customer info -->

		<div class="order-left-top-box">

			<!-- left box : order details -->

			<div class="order-global-details">
				<?php echo $this->loadTemplate('details'); ?>
			</div>

			<!-- right box : customer indo -->

			<div class="order-customer-details">
				<?php echo $this->loadTemplate('customer'); ?>

				<!-- Custom Fields Toggle Button -->

				<?php if ($this->order->hasFields && $this->order->items): ?>
					<button type="button" class="btn" id="custom-fields-btn"><?php echo JText::translate('VRSHOWCUSTFIELDS'); ?></button>
				<?php endif; ?>
			</div>

		</div>

		<!-- bottom box: order items, custom fields -->

		<div class="order-left-bottom-box">

			<!-- left box : order items -->

			<?php if ($this->order->items): ?>
				<div class="order-items-list">
					<?php echo $this->loadTemplate('items'); ?>
				</div>
			<?php endif; ?>

			<!-- right box : custom fields -->

			<?php if ($this->order->hasFields): ?>
				<div class="order-custom-fields" style="<?php echo ($this->order->items ? 'display: none;' : ''); ?>">
					<?php echo $this->loadTemplate('fields'); ?>
				</div>
			<?php endif; ?>

		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"main.bottom","type":"field"} -->

		<?php
		// plugins can use the "main.bottom" key to introduce custom
		// HTML at the bottom of the page
		if (isset($this->addons['main.bottom']))
		{
			echo $this->addons['main.bottom'];

			// unset details form to avoid displaying it twice
			unset($this->addons['main.bottom']);
		}
		?>

	</div>

	<!-- right box : payment details, invoices -->

	<div class="order-right-box">

		<!-- top box : payment details -->

		<div class="order-payment-details">
			<?php echo $this->loadTemplate('payment'); ?>
		</div>

	</div>

</div>

<?php
JText::script('VRSHOWCUSTFIELDS');
JText::script('VRHIDECUSTFIELDS');
JText::script('VRSHOWNOTES');
JText::script('VRHIDENOTES');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#custom-fields-btn').on('click', function() {
				const fields = $('.order-custom-fields');

				if (fields.is(':visible')) {
					fields.hide();

					$(this).text(Joomla.JText._('VRSHOWCUSTFIELDS'));
				} else {
					fields.show();

					$(this).text(Joomla.JText._('VRHIDECUSTFIELDS'));
				}
			});
		});
	})(jQuery);
</script>