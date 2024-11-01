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
 * @var  int                 $index        The current key of the item in cart.
 * @var  Item                $item         The item instance to insert/update.
 * @var  VREOrderRestaurant  $reservation  The reservation instance.
 */
extract($displayData);

$currency = VREFactory::getCurrency();

?>

<form id="dish-overlay-item-form">

	<!-- ITEM DETAILS -->
	
	<div class="dish-overlay-item-details">

		<!-- ITEM IMAGE -->

		<?php if ($item->image): ?>
			<div class="dish-overlay-item-image" style="background-image: url(<?php echo VREMEDIA_URI . $item->image; ?>);">
				
			</div>
		<?php endif; ?>

		<!-- ITEM TEXTS -->

		<div class="dish-overlay-item-text">

			<!-- ITEM NAME -->
			
			<div class="dish-overlay-item-name">
				<?php echo $item->getName(); ?>
			</div>

			<!-- ITEM DESCRIPTION -->

			<?php if ($desc = $item->getDescription()): ?>
				<div class="dish-overlay-item-description">
					<?php echo $desc; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

	<!-- ADDITIONAL NOTES -->

	<div class="vrtk-additem-notes-box">

		<div class="vrtk-additem-notes-title vr-disable-selection">
			<?php echo JText::translate('VRTKADDREQUEST'); ?>
		</div>

		<div class="vrtk-additem-notes-field" style="<?php echo $item->getAdditionalNotes() ? '' : 'display: none;'; ?>">
			<div class="vrtk-additem-notes-info">
				<?php echo JText::translate('VRTKADDREQUESTSUBT'); ?>
			</div>

			<textarea name="notes" maxlength="256"><?php echo $item->getAdditionalNotes(); ?></textarea>
		</div>

	</div>

	<!-- ITEM VARIATIONS -->

	<?php
	// get a list of variations
	$variations = $item->getVariations();

	if ($variations)
	{
		// get selected variation
		$selected = $item->getVariation();
		?>
		<div class="vrtk-additem-variations-box">

			<p><?php echo JText::translate('VRTKCHOOSEVAR'); ?></p>

			<ul>
				<?php foreach ($variations as $var): ?>
					<li>
						<input
							type="radio"
							name="id_product_option"
							value="<?php echo (int) $var->id; ?>"
							id="vre-item-var-<?php echo (int) $var->id; ?>"
							data-cost="<?php echo (float) $var->price; ?>"
							<?php echo $selected && $var->id == $selected->id ? 'checked="checked"' : ''; ?>
						/>

						<label for="vre-item-var-<?php echo (int) $var->id; ?>">
							<?php echo $var->name; ?>
						</label>

						<?php if ($var->price): ?>
							<span class="var-charge">
								+&nbsp;<?php echo $currency->format($var->price); ?>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>
		<?php
	}
	?>

	<?php if (VREFactory::getConfig()->getBool('servingnumber')): ?>
		<div class="vrtk-additem-variations-box">

			<p><?php echo JText::translate('VRE_ORDERDISH_SERVING_NUMBER_LABEL'); ?></p>

			<ul>
				<?php foreach ([0, 1, 2] as $number): ?>
					<li>
						<input
							type="radio"
							name="serving_number"
							value="<?php echo (int) $number; ?>"
							id="vre-item-serving-number-<?php echo (int) $number; ?>"
							<?php echo $item->getServingNumber() == $number ? 'checked="checked"' : ''; ?>
						/>

						<label for="vre-item-serving-number-<?php echo (int) $number; ?>">
							<?php echo JText::translate('VRE_ORDERDISH_SERVING_NUMBER_' . $number); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>

		</div>
	<?php endif; ?>

	<!-- ITEM QUANTITY -->

	<div class="vrtk-additem-quantity-box">

		<div class="vrtk-additem-quantity-box-inner">

			<span class="quantity-actions">
				<a href="javascript:void(0)" data-role="unit.remove" class="vrtk-action-remove <?php echo ($item->getQuantity() <= 1 ? 'disabled' : ''); ?>">
					<i class="fas fa-minus"></i>
				</a>

				<input type="text" name="quantity" value="<?php echo (int) $item->getQuantity(); ?>" size="4" id="vrtk-quantity-input" onkeypress="return (event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode == 13;" />

				<a href="javascript:void(0)" data-role="unit.add" class="vrtk-action-add">
					<i class="fas fa-plus"></i>
				</a>
			</span>

		</div>

	</div>

	<!-- ACTIONS BAR -->

	<div class="dish-item-overlay-footer">

		<button type="button" class="vre-btn secondary" data-role="close">
			<?php echo JText::translate('VRTKADDCANCELBUTTON'); ?>
		</button>

		<button type="button" class="vre-btn success" data-role="save">
			<?php echo JText::sprintf('VRTKADDTOTALBUTTON', $currency->format($item->getTotalCost())); ?>
		</button>

	</div>

	<input type="hidden" name="id" value="<?php echo (int) $item->id_assoc; ?>" />
	<input type="hidden" name="index" value="<?php echo (int) $index; ?>" />
	<input type="hidden" name="ordnum" value="<?php echo (int) $reservation->id; ?>" />
	<input type="hidden" name="ordkey" value="<?php echo $this->escape($reservation->sid); ?>" />

</form>

<script>
	(function($) {
		'use strict';

		// Item notes

		$('.vrtk-additem-notes-title').on('click', () => {
			if (!$('.vrtk-additem-notes-field').is(':visible')) {
				$('.vrtk-additem-notes-field').slideDown();
			} else {
				$('.vrtk-additem-notes-field').slideUp();
			}
		});

		// Item variations

		$('#dish-overlay-item-form input[name="id_product_option"]').on('change', () => {
			// trigger quantity change to update total
			$('#vrtk-quantity-input').trigger('change');
		});

		// Item quantity

		$('#vrtk-quantity-input').on('change', function() {
			// get quantity
			let q = parseInt($(this).val());
			
			if (q > 1) {
				// allow (-) button again
				$('#dish-overlay-item-form .vrtk-action-remove').removeClass('disabled');
			} else {
				// disable (-) button
				$('#dish-overlay-item-form .vrtk-action-remove').addClass('disabled');
			}

			// get total cost per unit
			let total = <?php echo $item->getPrice(false); ?>;

			// get selected variation
			const opt = $('#dish-overlay-item-form input[name="id_product_option"]:checked');

			if (opt.length) {
				// increase base cost by the variation charge
				total += parseFloat(opt.data('cost'));
			}

			// multiply by the number of selected units
			total *= q;

			// fetch total text
			let text = Joomla.JText._('VRTKADDTOTALBUTTON').replace(/%s/, Currency.getInstance().format(total));
			// update button text
			$('#dish-overlay-item-form .dish-item-overlay-footer button[data-role="save"]').text(text);
		});

		$('.quantity-actions *[data-role]').on('click', function() {
			// get quantity input
			const input = $('#vrtk-quantity-input');

			// get current quantity
			let q = parseInt(input.val());

			// fetch units to add/decrease
			let units = $(this).data('role') == 'unit.add' ? 1 : -1;
			
			if (q + units > 0) {
				// update only in case the quantity is higher than 0
				input.val(q + units);

				// update quantity
				input.trigger('change');
			}
		});

		// Actions

		$('#dish-overlay-item-form .dish-item-overlay-footer button[data-role="close"]').on('click', () => {
			vrCloseDishOverlay();
		});

		$('#dish-overlay-item-form .dish-item-overlay-footer button[data-role="save"]').on('click', () => {
			// serialize form data
			let data = $('#dish-overlay-item-form').serialize();

			const btn = $(this);

			// disable button during the request
			btn.prop('disabled', true);

			// save dish into the cart
			vrAddDishToCart(data).then((resp) => {
				// close overlay on success
				vrCloseDishOverlay();
			}).catch((error) => {
				// enable button again
				btn.prop('disabled', false);
			});
		});
	})(jQuery);
</script>