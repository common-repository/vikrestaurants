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

$toppings_map_costs = [];

?>

<form id="vrtk-additem-form">

	<div class="vrtk-additem-container">
		
		<!-- ITEM QUANTITY -->

		<div class="vrtk-additem-quantity-box" id="vrtk-additem-quantity">
			<span class="quantity-label"><?php echo JText::translate('VRTKADDQUANTITY'); ?>:</span>

			<span class="quantity-actions">
				<a href="javascript: void(0);" class="vrtk-action-remove no-underline <?php echo ($this->item->quantity <= 1 ? 'disabled' : ''); ?>">
					<i class="fas fa-minus"></i>
				</a>

				<input type="text" name="quantity" value="<?php echo $this->item->quantity; ?>" size="4" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />

				<a href="javascript: void(0);" class="vrtk-action-add no-underline">
					<i class="fas fa-plus"></i>
				</a>
			</span>
		</div>
		
		<div class="vrtk-additem-middle">
		
			<!-- ADDITIONAL NOTES -->

			<div class="vrtk-additem-notes-box">
				<div class="vrtk-additem-notes-title vr-disable-selection">
					<?php echo JText::translate('VRTKADDREQUEST'); ?>
				</div>

				<div class="vrtk-additem-notes-field" style="<?php echo $this->item->notes ? '' : 'display: none;'; ?>">
					<div class="vrtk-additem-notes-info">
						<?php echo JText::translate('VRTKADDREQUESTSUBT'); ?>
					</div>

					<textarea name="notes" maxlength="256"><?php echo $this->item->notes; ?></textarea>
				</div>
			</div>
			
			<!-- TOTAL COST -->
			<div class="vrtk-additem-tcost-box">
				<?php echo $currency->format($this->item->price * $this->item->quantity); ?>
			</div>
		
		</div>
		
		<!-- TOPPINGS GROUPS CONTAINER -->
		
		<div class="vrtk-additem-groups-loading" style="display: none;text-align: center;">
			<img id="img-loading" src="<?php echo VREASSETS_URI . 'css/images/hor-loader.gif'; ?>" />
		</div>
		
		<div class="vrtk-additem-groups-container" style="visibility: hidden;">
			
			<?php foreach ($this->item->toppings as $group): ?>
				<div class="vrtk-additem-group-box" id="vrtkgroup<?php echo (int) $group->id; ?>" data-multiple="<?php echo (int) $group->multiple; ?>" data-min-toppings="<?php echo (int) $group->min_toppings; ?>" data-max-toppings="<?php echo (int) $group->max_toppings; ?>">

					<div class="vrtk-additem-group-title">
						<?php echo $group->description; ?>
					</div>
					
					<div class="vrtk-additem-group-fields">
						<?php foreach ($group->list as $topping): ?>
							<div class="vrtk-additem-group-topping vrtk-group-<?php echo ($group->multiple ? 'multiple' : 'single') . ($group->use_quantity ? ' use-quantity' : ''); ?>">
								
								<?php if ($group->multiple): ?>

									<span class="vrtk-additem-topping-field">
										<input
											type="checkbox"
											value="<?php echo (int) $topping->assoc_id; ?>"
											id="vrtk-cb<?php echo (int) $topping->assoc_id; ?>"
											name="topping[<?php echo (int) $group->id; ?>][]"
											class="vre-topping-checkbox"
											data-price="<?php echo (float) $topping->rate; ?>"
											data-group="<?php echo (int) $group->id; ?>"
											<?php echo ($topping->checked ? 'checked="checked"' : ''); ?>
										/>

										<label for="vrtk-cb<?php echo $topping->assoc_id; ?>">
											<?php echo $topping->name; ?>

											<?php if ($topping->description): ?>
												<i class="fas fa-info-circle topping-desc" title="<?php echo $this->escape($topping->description); ?>"></i>
											<?php endif; ?>
										</label>
									</span>

									<?php if ($group->use_quantity): ?>
										<span class="vrtk-additem-topping-units" data-units="<?php echo (int) $topping->units; ?>">
											<a href="javascript:void(0)" class="topping-del-unit no-underline">
												<i class="fas fa-minus-circle"></i>
											</a>

											<span class="topping-units"><?php echo $topping->units; ?></span>

											<a href="javascript:void(0)" class="topping-add-unit no-underline">
												<i class="fas fa-plus-circle"></i>
											</a>
										</span>

										<input type="hidden" name="topping_units[<?php echo (int) $group->id; ?>][<?php echo (int) $topping->assoc_id; ?>]" value="<?php echo (int) $topping->units; ?>" />
									<?php endif; ?>

									<?php if ($topping->rate != 0): ?>
										<span class="vrtk-additem-topping-price">
											<?php echo $currency->format($topping->rate); ?>
										</span>
									<?php endif; ?>
								
								<?php else: ?>

									<span class="vrtk-additem-topping-field">
										<input
											type="radio"
											value="<?php echo (int) $topping->assoc_id; ?>"
											id="vrtk-rb<?php echo (int) $topping->assoc_id; ?>"
											name="topping[<?php echo (int) $group->id; ?>][]" 
											class="vre-topping-radio"
											data-price="<?php echo (float) $topping->rate; ?>"
											data-group="<?php echo (int) $group->id; ?>"
											<?php echo ($topping->checked ? 'checked="checked"' : ''); ?>
										/>

										<label for="vrtk-rb<?php echo (int) $topping->assoc_id; ?>">
											<?php echo $topping->name; ?>

											<?php if ($topping->description): ?>
												<i class="fas fa-info-circle topping-desc" title="<?php echo $this->escape($topping->description); ?>"></i>
											<?php endif; ?>
										</label>
									</span>

									<?php if ($topping->rate != 0): ?>
										<span class="vrtk-additem-topping-price">
											<?php echo $currency->format($topping->rate); ?>
										</span>
									<?php endif; ?>
									
									<?php
									if ($topping->checked)
									{
										$toppings_map_costs[$group->id] = $topping->rate; 
									}
									?>
									
								<?php endif; ?>

							</div>
						<?php endforeach; ?>
					</div>

				</div>
			<?php endforeach; ?>
			
		</div>
		
		<div class="vrtk-additem-bottom dish-item-overlay-footer">
			
			<!-- CANCEL BUTTON -->

			<button type="button" id="vrtk-cartcancel-button" class="vre-btn secondary" data-role="close">
				<?php echo JText::translate("VRTKADDCANCELBUTTON"); ?>
			</button>

			<!-- ADD TO CART BUTTON -->

			<button type="button" id="vrtk-addtocart-button" class="vre-btn success" data-role="save">
				<?php echo JText::translate($this->item->cartIndex >= 0 ? 'VRSAVE' : 'VRTKADDOKBUTTON'); ?>
			</button>
			
		</div>
		
	</div>
	
	<input type="hidden" name="item_index" value="<?php echo (int) $this->item->cartIndex; ?>" />
	<input type="hidden" name="id_entry" value="<?php echo (int) $this->item->id; ?>" />
	<input type="hidden" name="id_option" value="<?php echo (int) $this->item->oid; ?>" />

</form>

<script>
	(function($) {
		'use strict';

		setTimeout(() => {
			if ($('.vrtk-additem-groups-container').is(':visible') == false) {
				$('.vrtk-additem-groups-loading').show();
			}
		}, 750);

		if ($.fn.tooltip) {
			// prevent errors in case tooltip is not defined
			$('.topping-desc').tooltip();
		}
		
		// ITEM QUANTITY

		$('#vrtk-additem-quantity input[name="quantity"]').on('change', function() {
			let q = vrGetAddItemQuantity();

			const box = $(this).closest('.quantity-actions');
			
			if (q > 1) {
				$(box).find('.vrtk-action-remove').removeClass('disabled');
			} else {
				$(box).find('.vrtk-action-remove').addClass('disabled');
			}

			vrIncreaseEntryPrice();
		});

		$('#vrtk-additem-quantity').find('.vrtk-action-remove, .vrtk-action-add').on('click', function() {
			const box   = $(this).closest('.quantity-actions');
			const input = $(box).find('input[name="quantity"]');

			let units = 1;

			if ($(this).hasClass('vrtk-action-remove')) {
				units = -1;
			}

			let q = vrGetAddItemQuantity();
			
			if (q + units > 0) {
				input.val(q + units);
			}
		
			input.trigger('change');
		});

		const vrGetAddItemQuantity = () => {
			let quantity = parseInt($('#vrtk-additem-quantity input[name="quantity"]').val());

			if (isNaN(quantity) || quantity <= 0) {
				quantity = 1;
			}

			return quantity;
		}
		
		// ADDITIONAL NOTES
		
		$('.vrtk-additem-notes-title').on('click', () => {
			if (!$('.vrtk-additem-notes-field').is(':visible')) {
				$('.vrtk-additem-notes-field').slideDown();
			} else {
				$('.vrtk-additem-notes-field').slideUp();
			}
		});
		
		// GROUPS 
		
		$(function() {
			const cont = $('.vrtk-additem-groups-container');
			let bound  = cont.offset().left + cont.width() / 2;
			
			let _float, _pos;

			// recalculate position of the blocks in order to be properly displayed
			$('.vrtk-additem-group-box').each(function() {
				let _float = $(this).css('float');
				let _pos   = $(this).offset().left + $(this).width();
				
				if (_pos < bound && _float == 'right') {
					$(this).css('float', 'left');
				} else if (_pos >= bound && _float == 'left') {
					$(this).css('float', 'right');   
				}
			});
			
			// remove loading box
			$('.vrtk-additem-groups-loading').remove();
			// DO NOT use display:none because the position wouldn't
			// be properly calculated
			cont.css('visibility', 'visible');

			// register events
			$('.topping-del-unit').on('click', function() {
				vrAddToppingUnits(this, -1);
			});

			$('.topping-add-unit').on('click', function() {
				vrAddToppingUnits(this, 1);
			});
		});
		
		// TOPPINGS
		
		let ENTRY_TOTAL_COST   = <?php echo ($this->item->price); ?>;
		let TOPPINGS_MAP_COSTS = <?php echo json_encode($toppings_map_costs); ?>;

		const vrCheckGroupToppingsStatus = (group) => {
			// calculate number of picked toppings
			let checked = vrCountCheckedToppings(group);
			// fetch maximum number of selectable toppings
			let max = parseInt($(group).attr('data-max-toppings'));
			
			if (checked == max) {
				$(group).find('input[name^="topping["]:not(:checked)').prop('disabled', true);

				// disable add units button (if supported)
				$(group).find('.topping-add-unit').addClass('disabled');

				return true;
			}

			$(group).find('input[name^="topping["]:not(:checked)').prop('disabled', false);

			// enable add units button (if supported)
			$(group).find('.topping-add-unit').removeClass('disabled');

			return false;
		}

		const vrCountCheckedToppings = (group) => {
			let count = 0;

			$(group).find('input[name^="topping["]:checked').each(function() {
				count += vrGetToppingUnits(this);
			});

			return count;
		}

		const vrGetToppingUnits = (topping) => {
			const units = $(topping).closest('.vrtk-additem-topping-field')
				.siblings('[data-units]');

			if (units.length == 0) {
				return 1;
			}

			return parseInt(units.attr('data-units'));
		}

		const vrAddToppingUnits = (btn, q) => {
			if ($(btn).length == 0 || $(btn).hasClass('disabled')) {
				// do not go ahead in case of disabled button
				return false;
			}

			// get units holder
			const holder = $(btn).closest('[data-units]');

			// get selected units plus the specified ones
			let units = parseInt(holder.attr('data-units')) + q;

			if (units < 0) {
				// cannot decrease further units
				return true;
			}

			// update units
			holder.attr('data-units', units);
			holder.siblings('input[name^="topping_units"]').val(units);
			holder.find('.topping-units').text(units);

			// get related topping checkbox
			const checkbox = holder.siblings('.vrtk-additem-topping-field').find('input');

			if (units == 0) {
				if (checkbox.is(':checked')) {
					// uncheck checkbox
					checkbox.prop('checked', false);
				}

				// do not disable delete button in order to avoid
				// strange behaviors with other events
			}

			// get topping price
			let p = parseFloat($(checkbox).attr('data-price'));

			// increase product price
			vrIncreaseEntryPrice(p * q);

			// get topping group
			const group = holder.closest('.vrtk-additem-group-box');

			// check whether all the toppings have been selected
			vrCheckGroupToppingsStatus(group);

			return true;
		}
		
		$('.vrtk-additem-group-box').each(function() {
			if ($(this).attr('data-multiple') == 1) {
				// toggle status of checkboxes
				vrCheckGroupToppingsStatus(this);
			}
		});

		$('.vre-topping-checkbox').on('change', function() {
			let p = parseFloat($(this).attr('data-price'));

			// get topping container
			const toppingParent = $(this).closest('.vrtk-additem-group-topping');
			
			if ($(this).is(':checked')) {
				// set units to 1
				let added = vrAddToppingUnits(toppingParent.find('.topping-add-unit'), 1);

				if (!added) {
					vrIncreaseEntryPrice(p);
				}
			} else {
				// multiply topping units per -1 to decrease them
				let units = vrGetToppingUnits(this) * -1;
			
				// decrease by all the picked units
				let deleted = vrAddToppingUnits(toppingParent.find('.topping-del-unit'), units);

				if (!deleted) {
					vrIncreaseEntryPrice(p * units);
				}
			}

			const group = $('#vrtkgroup'+$(this).attr('data-group'));

			// toggle status of checkboxes
			vrCheckGroupToppingsStatus(group);
		});
		
		$('.vre-topping-radio').on('change', function(e) {
			let id_group = $(this).attr('data-group');

			let price = parseFloat($(this).attr('data-price'));
			let total = price;

			if (TOPPINGS_MAP_COSTS.hasOwnProperty(id_group)) {
				// decrease by the previously selected topping
				total -= parseFloat(TOPPINGS_MAP_COSTS[id_group]);
			}

			// register selected topping price
			TOPPINGS_MAP_COSTS[id_group] = price;
			
			vrIncreaseEntryPrice(total);
		});
		
		const vrIncreaseEntryPrice = (p) => {
			if (p) {
				ENTRY_TOTAL_COST += p;
			}

			let total = ENTRY_TOTAL_COST * vrGetAddItemQuantity();

			$('.vrtk-additem-tcost-box').text(Currency.getInstance().format(total));
		}
		
		const vrAllGroupsChecked = () => {
			let min_toppings, max_toppings, sel_toppings;
			let ok = true;

			$('.vrtk-additem-group-box').each(function() {
				min_toppings = parseInt($(this).attr('data-min-toppings'));
				max_toppings = parseInt($(this).attr('data-max-toppings'));
				sel_toppings = vrCountCheckedToppings(this);

				if ((min_toppings > 0 && sel_toppings < min_toppings)
					|| sel_toppings > max_toppings) {
					ok = false;
					$(this).addClass('vrrequiredfield');
				} else {
					$(this).removeClass('vrrequiredfield');
				}
			});
			
			return ok;
		}
		
		// MODAL BUTTONS
		
		$('#vrtk-addtocart-button').on('click', () => {
			if (!vrAllGroupsChecked()) {
				return false;
			}
			
			vrPostTakeAwayItem();
		});
		
		$('#vrtk-cartcancel-button').on('click', () => {
			vrCloseOverlay('vrnewitemoverlay');
		});
		
		const vrPostTakeAwayItem = () => {
			// serialize form
			let data = $('#vrtk-additem-form').serialize();

			// make request
			vrMakeAddCartRequest(data).then((response) => {
				// auto-close overlay on success
				vrCloseOverlay('vrnewitemoverlay');
			}).catch((error) => {
				// do nothing here
			});
		}
	})(jQuery);
</script>