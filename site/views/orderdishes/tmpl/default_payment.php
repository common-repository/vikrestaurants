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

?>

<div class="vr-overlay" id="vrpaymentoverlay" style="display: none;">

	<div class="vr-modal-box">

		<div class="vr-modal-head">

			<div class="vr-modal-head-title">
				<h3><?php echo JText::translate('VREORDERFOOD_PAY_NOW'); ?></h3>
			</div>

			<div class="vr-modal-head-dismiss">
				<a href="javascript: void(0);" onClick="vrClosePaymentOverlay();">×</a>
			</div>

		</div>

		<div class="vr-modal-body">

			<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&task=orderdish.paynow' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" name="dishesPaymentForm">

				<!-- PAYMENT METHOD -->

				<div class="vr-payments-list">

					<?php
					/**
					 * This form is displayed from the layout below:
					 * /components/com_vikrestaurants/layouts/blocks/paymentmethods.php (joomla)
					 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/paymentmethods.php (wordpress)
					 *
					 * @since 1.9
					 */
					echo JLayoutHelper::render('blocks.paymentmethods', [
						'payments'   => $this->payments,
						'id_payment' => $this->reservation->id_payment,
					]);
					?>
					
				</div>

				<!-- GRATUITY -->

				<div class="vr-bill-gratuity">

					<div class="vrtkdeliverytitlediv"><?php echo JText::translate('VRTIPFORPROPERTY'); ?></div>

					<div class="vrtk-additem-quantity-box">

						<div class="vrtk-additem-quantity-box-inner">

							<span class="quantity-actions">
								<a href="javascript: void(0);" data-role="tip.remove" class="vrtk-action-remove disabled">
									<i class="fas fa-minus"></i>
								</a>

								<input type="text" name="gratuity" value="0" size="4" id="vrtk-gratuity-input" onkeypress="return (event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode == 13;" />

								<a href="javascript: void(0);" data-role="tip.add" class="vrtk-action-add">
									<i class="fas fa-plus"></i>
								</a>
							</span>

						</div>

						<div class="vrtk-ceil-tip" style="display: none;">
							<input type="checkbox" value="1" name="ceiltip" id="vrtk-ceil-tip-checkbox" />
							<label for="vrtk-ceil-tip-checkbox"><?php echo JText::translate('VRTIPROUNDED'); ?></label>
						</div>

					</div>

				</div>

				<!-- ACTIONS BAR -->

				<div class="dish-item-overlay-footer">

					<button type="button" class="vre-btn secondary" data-role="close">
						<?php echo JText::translate('VRTKADDCANCELBUTTON'); ?>
					</button>

					<button type="button" class="vre-btn success" data-role="save">
						<?php echo JText::translate('VRCARTPAYNOWTOTALBTN'); ?>
					</button>

				</div>

				<input type="hidden" name="option" value="com_vikrestaurants" />
				<input type="hidden" name="task" value="orderdish.paynow" />
				<input type="hidden" name="ordnum" value="<?php echo (int) $this->reservation->id; ?>" />
				<input type="hidden" name="ordkey" value="<?php echo $this->escape($this->reservation->sid); ?>" />

			</form>

			<!-- end body -->

		</div>

	</div>

</div>

<?php
JText::script('VRCARTPAYNOWTOTALBTN');
?>

<script>
	(function($) {
		'use strict';

		const vrUpdatePaymentTotal = (total) => {
			if (isNaN(total)) {
				// get cart total
				total = vrGetCartTotal();
			}

			// sum tip
			let tip = parseInt($('#vrtk-gratuity-input').val());

			if (!isNaN(tip) && tip > 0) {
				total += tip;
			}

			// check if we should ceil the total amount
			if ($('#vrtk-ceil-tip-checkbox').is(':checked')) {
				total = Math.ceil(total);
			}

			// format total as currency
			total = Currency.getInstance().format(total);
			// fetch total text
			total = Joomla.JText._('VRCARTPAYNOWTOTALBTN').replace(/%s/, total);
			// update text
			$('#vrpaymentoverlay button[data-role="save"]').text(total);
		}

		window.vrOpenPaymentOverlay = () => {
			let total = vrGetCartTotal();

			// show gratuity checkbox in case of decimals
			if (Math.ceil(total) != total) {
				$('#vrpaymentoverlay .vrtk-ceil-tip').show();
			}

			// refresh total
			vrUpdatePaymentTotal(total);

			// show modal
			$('#vrpaymentoverlay').show();

			// prevent body from scrolling
			$('body').css('overflow', 'hidden');
		}

		window.vrClosePaymentOverlay = () => {
			// make body scrollable again
			$('body').css('overflow', 'auto');

			// hide overlay
			$('#vrpaymentoverlay').hide();
		}

		$(function() {
			$('#vrtk-gratuity-input').on('change', function() {
				// get gratuity
				let tip = parseInt($(this).val());
				
				if (tip > 0) {
					// allow (-) button again
					$('#vrpaymentoverlay .vrtk-action-remove').removeClass('disabled');
				} else {
					// disable (-) button
					$('#vrpaymentoverlay .vrtk-action-remove').addClass('disabled');
				}

				vrUpdatePaymentTotal();
			});

			$('.quantity-actions *[data-role]').on('click', function() {
				// get gratuity input
				const input = $('#vrtk-gratuity-input');

				// get current gratuity
				let tip = parseInt(input.val());

				// fetch units to add/decrease
				let units = $(this).data('role') == 'tip.add' ? 1 : -1;
				
				if (tip + units >= 0) {
					// update only in case the gratuity is equals or higher than 0
					input.val(tip + units);

					// update gratuity
					input.trigger('change');
				}
			});

			$('#vrtk-ceil-tip-checkbox').on('change', () => {
				vrUpdatePaymentTotal();
			});

			// Actions

			$('#vrpaymentoverlay .dish-item-overlay-footer button[data-role="close"]').on('click', () => {
				vrClosePaymentOverlay();
			});

			$('#vrpaymentoverlay .dish-item-overlay-footer button[data-role="save"]').on('click', () => {
				document.dishesPaymentForm.submit();
			});

			// Modal

			$('.vr-modal-box').on('click', (e) => {
				// ignore outside click
				e.stopPropagation();
			});

			$('#vrpaymentoverlay').on('click', () => {
				// close overlay when the background is clicked
				vrClosePaymentOverlay();
			});
		});
	})(jQuery);
</script>