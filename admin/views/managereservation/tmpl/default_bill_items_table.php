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

$currency = VREFactory::getCurrency();

?>

<table class="table order-items-table">

	<thead>

		<tr>

			<!-- ITEM -->

			<th width="50%" style="text-align: left;">
				<?php echo JText::translate('VRMANAGETKMENU3'); ?>
			</th>

			<!-- NOTES -->

			<th width="1%" style="text-align: center;">&nbsp;</th>

			<!-- NET -->

			<th width="10%" style="text-align: right;">
				<?php echo JText::translate('VRMANAGETKMENU5'); ?>
			</th>

			<!-- TAX -->

			<th width="10%" style="text-align: right;">
				<?php echo JText::translate('VRMANAGETKRES21'); ?>
			</th>

			<!-- GROSS -->

			<th width="12%" style="text-align: right;">
				<?php echo JText::translate('VRTKCARTOPTION3'); ?>
			</th>

			<!-- EDIT -->

			<th width="1%" style="text-align: center;">&nbsp;</th>

		</tr>
		
	</thead>

	<tbody>

		<?php foreach ($this->reservation->items as $i => $item): ?>

			<tr id="reservation-item-row-<?php echo (int) $i; ?>" class="reservation-item-row">

				<!-- ITEM -->
				
				<td data-column="item">
					<div>
						<small data-column="quantity"><?php echo $item->quantity; ?>x</small>
						<span class="td-primary"><?php echo $item->name; ?></span>
					</div>
					<div class="td-secondary"><?php echo $item->option_name; ?></div>
					<input type="hidden" name="item_json[]" value="<?php echo $this->escape(json_encode($item)); ?>" />
				</td>

				<!-- NOTES -->
				
				<td data-column="notes" style="text-align: center;">
					<?php if ($item->notes): ?>
						<i class="fas fa-question-circle big hasTooltip" title="<?php echo $this->escape($item->notes); ?>"></i>
					<?php else: ?>
						<i class="fas fa-question-circle big" style="display: none;"></i>
					<?php endif; ?>
				</td>

				<!-- NET -->

				<td data-column="net" class="text-nowrap" style="text-align: right;">
					<?php echo $currency->format($item->net); ?>
				</td>

				<!-- TAX -->

				<td data-column="tax" class="text-nowrap" style="text-align: right;">
					<?php echo $currency->format($item->tax); ?>
				</td>

				<!-- GROSS -->

				<td data-column="gross" class="text-nowrap" style="text-align: right;">
					<?php echo $currency->format($item->gross); ?>
				</td>

				<!-- EDIT -->
				
				<td data-column="edit" style="text-align: center;">
					<a href="javascript:void(0)" onclick="vreReservationItemEditClick(this)" data-index="<?php echo (int) $i; ?>" class="no-underline<?php echo $this->reservation->bill_closed ? ' disabled' : ''; ?>">
						<i class="fas fa-pen-square big"></i>
					</a>
				</td>

			</tr>

		<?php endforeach; ?>

	</tbody>

	<tfoot>

		<?php if ($this->reservation->discount_val > 0 || $this->reservation->coupon_str): ?>
			<tr class="order-discount">
				<td>
					<?php
					if ($this->reservation->discount_val > 0)
					{
						// remove discount
						echo $this->formFactory->createField()
							->type('button')
							->id('remove-discount')
							->class('btn-mini')
							->text(JText::translate('VRORDDISCMETHOD6'))
							->hidden(true);

						?>

						<div class="remove-discount-undo" style="display: none;">
							<?php
							echo $this->formFactory->createField()
								->type('button')
								->id('remove-discount-undo')
								->class('btn-mini')
								->text(JText::translate('VRE_REM_DISCOUNT_UNDO'))
								->hidden(true);
							?>

							<i class="fas fa-question-circle hasTooltip" title="<?php echo $this->escape(JText::translate('VRE_DISC_CHANGE_INFO')); ?>"></i>
						</div>

						<input type="hidden" name="remove_discount" value="0" />
						<?php
					}
					?>
				</td>
				<td colspan="5" style="text-align: right;">
					<span>
						<?php
						if ($this->reservation->coupon_str)
						{
							// extract coupon data
							list($code, $amount, $type) = explode(';;', $this->reservation->coupon_str);

							if ($amount == 0)
							{
								// no discount, we probably have a tracking coupon
								$coupon = $code;
							}
							else if ($type == 1)
							{
								// percentage amount
								$coupon = $code . ' : ' . $amount . '%';
							}
							else
							{
								// fixed amount
								$coupon = $code . ' : ' . $currency->format($amount);
							}
							?>
							<i class="fas fa-info-circle hasTooltip" title="<?php echo $this->escape($coupon); ?>"></i>
							<?php
						}

						echo JText::translate('VRINVDISCOUNTVAL');
						?>
					</span>

					<b><?php echo $currency->format($this->reservation->discount_val * -1); ?></b>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($this->reservation->tip_amount > 0): ?>
			<tr class="order-tip">
				<td>
					<?php
					// remove tip
					echo $this->formFactory->createField()
						->type('button')
						->id('remove-tip')
						->class('btn-mini')
						->text(JText::translate('VRORDTIPMETHOD3'))
						->hidden(true);

					?>

					<div class="remove-tip-undo" style="display: none;">
						<?php
						echo $this->formFactory->createField()
							->type('button')
							->id('remove-tip-undo')
							->class('btn-mini')
							->text(JText::translate('VRE_REM_TIP_UNDO'))
							->hidden(true);
						?>

						<i class="fas fa-question-circle hasTooltip" title="<?php echo $this->escape(JText::translate('VRE_DISC_CHANGE_INFO')); ?>"></i>
					</div>

					<input type="hidden" name="remove_tip" value="0" />
				</td>
				<td colspan="5" style="text-align: right;">
					<span><?php echo JText::translate('VRINVTIP'); ?></span>

					<b><?php echo $currency->format($this->reservation->tip_amount); ?></b>
				</td>
			</tr>
		<?php endif; ?>

		<!-- TOTALS -->

		<tr class="order-totals">

			<td colspan="6" style="text-align: right;">
				<div data-column="total_net" style="<?php echo $this->reservation->total_net == 0 ? 'display:none;' : ''; ?>">
					<span><?php echo JText::translate('VRINVTOTAL'); ?></span>

					<b><?php echo $currency->format($this->reservation->total_net); ?></b>

					<input type="hidden" name="total_net" value="<?php echo (float) $this->reservation->total_net; ?>" />
				</div>

				<div data-column="payment_charge" style="<?php echo $this->reservation->payment_charge == 0 ? 'display:none;' : ''; ?>">
					<span><?php echo JText::translate('VRINVPAYCHARGE'); ?></span>

					<b><?php echo $currency->format($this->reservation->payment_charge); ?></b>

					<input type="hidden" name="payment_tax" value="<?php echo (float) $this->reservation->payment_tax; ?>" />
					<input type="hidden" name="payment_charge" value="<?php echo (float) $this->reservation->payment_charge; ?>" />
				</div>

				<div data-column="total_tax" style="<?php echo $this->reservation->total_tax == 0 ? 'display:none;' : ''; ?>">
					<span><?php echo JText::translate('VRINVTAXES'); ?></span>

					<b><?php echo $currency->format($this->reservation->total_tax); ?></b>

					<input type="hidden" name="total_tax" value="<?php echo (float) $this->reservation->total_tax; ?>" />
				</div>

				<div data-column="bill_value">
					<span><?php echo JText::translate('VRINVGRANDTOTAL'); ?></span>

					<b><?php echo $currency->format($this->reservation->bill_value); ?></b>

					<input type="hidden" name="bill_value" value="<?php echo (float) $this->reservation->bill_value; ?>" />
				</div>

				<?php
				if ($this->reservation->tot_paid == 0 && $this->reservation->deposit && JHtml::fetch('vrehtml.status.isconfirmed', 'restaurant', $this->reservation->status))
				{
					// deposit probably not paid online
					$totalPaid = $this->reservation->deposit;
				}
				else
				{
					$totalPaid = $this->reservation->tot_paid;
				}

				if (!JHtml::fetch('vrehtml.status.ispaid', 'restaurant', $this->reservation->status) && $this->reservation->deposit > 0): ?>
					<div data-column="total_due" data-paid="<?php echo (float) $totalPaid; ?>">
						<span><?php echo JText::translate('VRORDERINVDUE'); ?></span>

						<b>
							<?php
							// substract total paid from bill value
							echo $currency->format(max(0, $this->reservation->bill_value - $totalPaid));
							?>
						</b>
					</div>
				<?php endif; ?>
			</td>

		</tr>

		<!-- ACTIONS -->

		<tr class="order-actions">

			<td colspan="6">
				<div class="pull-right">
					<?php
					echo $this->formFactory->createField()
						->type('button')
						->id('add-item-btn')
						->text(JText::translate('VRMANAGETKRES16'))
						->disabled($this->reservation->bill_closed)
						->hidden(true);
					?>
				</div>

				<?php if ($this->reservation->discount_val <= 0): ?>
					<div class="pull-left">
						<?php
						// add discount
						echo $this->formFactory->createField()
							->type('button')
							->id('add-discount')
							->text(JText::translate('VRORDDISCMETHOD4'))
							->hidden(true);
						?>

						<div style="display: none;">
							<?php
							echo $this->formFactory->createField()
								->type('groupedlist')
								->name('add_discount')
								->id('vr-coupon-sel')
								->hidden(true)
								->options(array_merge(
									// include placeholder and option to enter a manual discount
									[
										0 => [
											JHtml::fetch('select.option', '', ''),
											JHtml::fetch('select.option', 'manual', '- Manual -'),
										]
									],
									// get supported coupon codes
									JHtml::fetch('vrehtml.admin.coupons', $blank = false, $group = true, 'restaurant')
								));
							?>

							<i class="fas fa-question-circle hasTooltip" title="<?php echo $this->escape(JText::translate('VRE_DISC_CHANGE_INFO')); ?>"></i>
						</div>
					</div>
				<?php endif; ?>

				
				<?php if ($this->reservation->tip_amount <= 0): ?>
					<div class="pull-left">
						<?php
						// add tip
						echo $this->formFactory->createField()
							->type('button')
							->id('add-tip')
							->text(JText::translate('VRORDTIPMETHOD1'))
							->hidden(true);
						?>

						<div class="add-tip-undo" style="display: none;">
							<?php
							echo $this->formFactory->createField()
								->type('button')
								->id('add-tip-undo')
								->text(JText::translate('VRE_ADD_TIP_UNDO'))
								->hidden(true);
							?>

							<i class="fas fa-question-circle hasTooltip" title="<?php echo $this->escape(JText::translate('VRE_DISC_CHANGE_INFO')); ?>"></i>
						</div>
					</div>
				<?php endif; ?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"items.actions","type":"field"} -->

				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Items" footer (left-side).
				 * 
				 * NOTE: retrieved from "onDisplayViewReservation" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['items.actions']))
				{
					echo $this->forms['items.actions'];

					// unset actions form to avoid displaying it twice
					unset($this->forms['items.actions']);
				}
				?>

			</td>

		</tr>

	</tfoot>

</table>

<?php
JText::script('VRE_FILTER_SELECT_COUPON');
JText::script('VRE_MANUAL_DISCOUNT_PROMPT');
JText::script('VRE_MANUAL_TIP_PROMPT');
?>

<script>
	(function($, w) {
		'use strict';

		let ITEMS_COUNT = <?php echo count($this->reservation->items); ?>;

		w.vreReservationItemEditClick = (link) => {
			if ($(link).hasClass('disabled')) {
				return false;
			}

			vreOpenRestaurantItemCard(parseInt($(link).attr('data-index')));
			return true;
		}

		w.vreAddReservationItemCard = (data) => {
			const index = ++ITEMS_COUNT;

			// create table row
			const row = $('<tr id="reservation-item-row-' + index + '" class="reservation-item-row"></tr>');

			// append item
			row.append(
				'<td data-column="item">\n' +
					'<div>\n' +
						'<small data-column="quantity"></small>\n' +
						'<span class="td-primary"></span>\n' +
					'</div>\n' +
					'<div class="td-secondary"></div>\n' +
					'<input type="hidden" name="item_json[]" />\n' +
				'</td>'
			);

			// append notes
			row.append(
				'<td data-column="notes" style="text-align: center;">\n' +
					'<i class="fas fa-question-circle big" style="display: none;"></i>\n' +
				'</td>'
			);

			// append net
			row.append('<td data-column="net" class="text-nowrap" style="text-align: right;"></td>');

			// append tax
			row.append('<td data-column="tax" class="text-nowrap" style="text-align: right;"></td>');

			// append gross
			row.append('<td data-column="gross" class="text-nowrap" style="text-align: right;"></td>');

			// append edit
			row.append(
				'<td data-column="edit" style="text-align: center;">\n' +
					'<a href="javascript:void(0)" onclick="vreReservationItemEditClick(this)" data-index="' + index + '" class="no-underline">\n' +
						'<i class="fas fa-pen-square big"></i>\n' +
					'</a>\n' +
				'</td>'
			);

			$('#order-items-table table tbody').append(row);

			return row;
		}

		w.vreRefreshReservationItemCard = (row, data) => {
			// refresh quantity
			row.find('small[data-column="quantity"]').html(data.quantity + 'x');

			// update item name and variation
			const item = row.find('td[data-column="item"]');
			item.find('.td-primary').html(data.name);
			item.find('.td-secondary').html(data.option_name || '');

			// update notes
			const notes = row.find('td[data-column="notes"]').find('i');

			if (data.notes) {
				notes.show().tooltip('dispose').attr('title', data.notes).tooltip();
			} else {
				notes.hide();
			}

			const currency = Currency.getInstance();

			// update net
			row.find('td[data-column="net"]').html(currency.format(data.net));

			// update tax
			row.find('td[data-column="tax"]').html(currency.format(data.tax));

			// update gross
			row.find('td[data-column="gross"]').html(currency.format(data.gross));
		}

		// disable the possibility to edit the bill
		$(w).on('bill.changed', (event) => {
			if (event.bill.closed) {
				$('#order-items-table table tbody td[data-column="edit"] a').addClass('disabled');
				$('#add-item-btn').prop('disabled', true);
			} else {
				$('#order-items-table table tbody td[data-column="edit"] a').removeClass('disabled');
				$('#add-item-btn').prop('disabled', false);
			}
		});

		$(function() {
			$('#vr-coupon-sel').select2({
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_COUPON'),
				allowClear: true,
				width: 250,
			});

			$('#vr-coupon-sel').on('change', function() {
				if ($(this).val() !== 'manual') {
					return;
				}

				const data = {
					value:     0,
					percentot: 2,
				};

				// manual option, ask for a discount
				let input = prompt(Joomla.JText._('VRE_MANUAL_DISCOUNT_PROMPT'));

				if (input === null || input === '') {
					// aborted by the user
					return;
				}

				// check whether a percentage discount was specified
				if (input.match(/\%$/)) {
					data.percentot = 1;
					input = input.replace(/\%$/, '');
				}

				// replace comma separator for decimals
				input = input.replace(/,/g, '.');

				// register discount value
				data.value = Math.abs(parseFloat(input));

				if (isNaN(data.value)) {
					// invalid amount, unset option
					$(this).select2('val', '');
					return;
				}

				$('#adminForm').find('input[name^="manual_discount["]').remove();
				$('#adminForm').append('<input type="hidden" name="manual_discount[value]" value="' + data.value + '" />');
				$('#adminForm').append('<input type="hidden" name="manual_discount[percentot]" value="' + data.percentot + '" />');
			});

			// discount
			$('#add-discount').on('click', function() {
				$(this).hide();
				$(this).next().slideDown();
			});

			$('#remove-discount').on('click', function() {
				$(this).hide();
				$('.remove-discount-undo').show();

				$('input[name="remove_discount"]').val(1);
			});

			$('#remove-discount-undo').on('click', function() {
				$('.remove-discount-undo').hide();
				$('#remove-discount').show();

				$('input[name="remove_discount"]').val(0);
			});

			// tip
			$('#add-tip').on('click', function() {
				const data = {
					value:     0,
					percentot: 2,
				};

				// manual option, ask for a tip
				let input = prompt(Joomla.JText._('VRE_MANUAL_TIP_PROMPT'));

				if (input === null || input === '') {
					// aborted by the user
					return;
				}

				// check whether a percentage tip was specified
				if (input.match(/\%$/)) {
					data.percentot = 1;
					input = input.replace(/\%$/, '');
				}

				// replace comma separator for decimals
				input = input.replace(/,/g, '.');

				// register tip value
				data.value = parseFloat(input);

				if (isNaN(data.value) || data.value <= 0) {
					// invalid amount
					return;
				}

				$('#adminForm').find('input[name^="manual_tip["]').remove();
				$('#adminForm').append('<input type="hidden" name="manual_tip[value]" value="' + data.value + '" />');
				$('#adminForm').append('<input type="hidden" name="manual_tip[percentot]" value="' + data.percentot + '" />');

				$(this).hide();
				$('.add-tip-undo').show();
			});

			$('#add-tip-undo').on('click', function() {
				$('.add-tip-undo').hide();
				$('#add-tip').show();

				$('#adminForm').find('input[name^="manual_tip["]').remove();
			});

			$('#remove-tip').on('click', function() {
				$(this).hide();
				$('.remove-tip-undo').show();

				$('input[name="remove_tip"]').val(1);
			});

			$('#remove-tip-undo').on('click', function() {
				$('.remove-tip-undo').hide();
				$('#remove-tip').show();

				$('input[name="remove_tip"]').val(0);
			});
		});
	})(jQuery, window);
</script>