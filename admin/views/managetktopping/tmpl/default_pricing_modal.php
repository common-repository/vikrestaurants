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

$vik = VREApplication::getInstance();

$rates = [];

// detect all the supported rates
foreach ($this->products as $menu)
{
	foreach ($menu->products as $product)
	{
		$rates[] = (float) $product->topping->rate;
	}
}

?>

<div class="inspector-form" id="inspector-group-form">
	
	<div class="inspector-fieldset">

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->id('price_alter_type')
			->value('percentage')
			->label('<span id="price_alter_type_label"></span>')
			->options([
				JHtml::fetch('select.option', 'percentage', ''),
				JHtml::fetch('select.option',      'fixed', ''),
			])->control([
				'style' => count(array_unique($rates)) === 1 ? 'display: none;' : '',
			]);
		?>

		<div class="control-group">
			<div class="vrtk-toppings-list">
				<?php
				foreach ($this->products as $menu)
				{
					?>
					<table class="inspector-selection-table">

						<thead>
							<tr>
								<th width="8%" style="text-align: left;">
									<input type="checkbox" class="toppings-group-checkbox" id="menu-topping-checkbox-<?php echo (int) $menu->id; ?>" />
								</th>
								
								<th width="52%" style="text-align: left;">
									<label for="menu-topping-checkbox-<?php echo (int) $menu->id; ?>">
										<strong><?php echo $menu->title; ?></strong>
									</label>
								</th>
								
								<th width="15%" style="text-align: left;">
									<?php echo JText::translate('VRMANAGETKTOPPING2'); ?>
								</th>

								<th width="25%" style="text-align: left;">
									<?php echo JText::translate('VRNEWPRICE'); ?>
								</th>
							</tr>
						</thead>

						<tbody>
							<?php
							foreach ($menu->products as $product)
							{
								?>
								<tr>
									<td>
										<input type="checkbox" value="<?php echo (int) $product->topping->id; ?>" class="topping-checkbox" id="topping-check-<?php echo (int) $product->topping->id; ?>" />
									</td>

									<td>
										<label for="topping-check-<?php echo (int) $product->topping->id; ?>">
											<strong><?php echo $product->name; ?></strong>

											<?php if ($product->option): ?>
												&nbsp;<span class="badge badge-info"><?php echo $product->option->name; ?></span>
											<?php endif; ?>
										</label>
									</td>

									<td>
										<span><?php echo $currency->format($product->topping->rate); ?></span>
									</td>

									<td>
										<?php
										echo $this->formFactory->createField()
											->type('number')
											->value($product->topping->rate)
											->setData('original-value', (float) $product->topping->rate)
											->class('topping-rate')
											->readonly()
											->step('any')
											->hidden(true)
											->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
												'before' => $currency->getSymbol(),
											]));
										?>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>

					</table>
					<?php
				}
				?>
			</div>
		</div>

	</div>

</div>

<?php
JText::script('VRE_INCREASE_BY');
JText::script('VRE_DECREASE_BY');
JText::script('VRE_PERCENTAGE_N');
JText::script('VRE_FIXED_N');
?>

<script>
	(function($, w) {
		'use strict';

		w.setSelectedToppings = (data) => {
			// recalculate first the fixed and percentage difference value
			let percentage = calculatePriceDifference('percentage', 2) * 100;
			let fixed      = calculatePriceDifference('fixed', 2);

			$('#price_alter_type_label').text(Joomla.JText._(fixed < 0 ? 'VRE_DECREASE_BY' : 'VRE_INCREASE_BY'));

			const priceAlterSelect = $('#price_alter_type');

			// refresh label for percentage option
			priceAlterSelect.find('option[value="percentage"]')
				.html(Joomla.JText._('VRE_PERCENTAGE_N').replace(/%s/, Math.abs(percentage) + '%'));

			// refresh label for fixed option
			priceAlterSelect.find('option[value="fixed"]')
				.html(Joomla.JText._('VRE_FIXED_N').replace(/%s/, Currency.getInstance().format(Math.abs(fixed))));

			// refresh selection, since the label may be updated
			priceAlterSelect.select2('val', priceAlterSelect.select2('val'));

			// toggle checkboxes
			$('.topping-checkbox').each(function() {
				// get parent row
				const row = $(this).closest('tr');
				// extract assoc ID
				let id_assoc = parseInt($(this).val());

				if (data.hasOwnProperty(id_assoc)) {
					$(this).prop('checked', true).trigger('change');
					row.find('.topping-rate').val(data[id_assoc]);
				} else {
					$(this).prop('checked', false).trigger('change');
				}
			});
		}

		w.getSelectedToppings = () => {
			let toppings = {};

			$('.topping-checkbox:checked').each(function() {
				// get parent row
				const row = $(this).closest('tr');
				// extract assoc ID
				let id_assoc = parseInt($(this).val());

				// fetch original price and new price
				const priceInput = row.find('.topping-rate');
				let oldPrice = parseFloat(priceInput.data('original-value'));
				let newPrice = parseFloat(priceInput.val());

				if (!isNaN(newPrice) && newPrice != oldPrice) {
					// register selected price within the lookup only in
					// case we have a valid value different then the 
					// previous one
					toppings[id_assoc] = parseFloat(row.find('.topping-rate').val());
				}
			});

			return toppings;
		}

		const calculatePriceDifference = (type, round) => {
			let price = parseFloat($('#adminForm input[name="price"]').val());
			price = isNaN(price) ? 0 : price;

			// calculate the difference between the new price and the previous one
			let diff = price - <?php echo (float) $this->topping->price; ?>;

			if (type === 'percentage') {
				// calculate rog
				diff = diff / <?php echo (float) $this->topping->price ?: 1; ?>;
			}

			if (typeof round !== 'undefined') {
				return Math.round((diff + Number.EPSILON) * Math.pow(10, round)) / Math.pow(10, round);
			}
			
			return diff;
		}

		const getNewPrice = (original, round) => {
			const type = $('#price_alter_type').select2('val');

			const diff = calculatePriceDifference(type);

			// always apply a fixed discount in case the original price was equals to 0
			if (type === 'percentage' && original != 0) {
				original = original + original * diff;
			} else {
				original += diff;
			}

			if (typeof round !== 'undefined') {
				original = Math.round((original + Number.EPSILON) * Math.pow(10, round)) / Math.pow(10, round);
			}

			return original;
		}

		$(function() {
			$('.toppings-group-checkbox').on('change', function() {
				// fetch new status
				const checked = $(this).is(':checked');

				const table = $(this).closest('.inspector-selection-table');

				// toggle all table checkboxes
				table.find('.topping-checkbox')
					.prop('checked', checked);

				// toggle readonly for inputs
				table.find('tbody input[type="number"]').each(function() {
					$(this).prop('readonly', checked ? false : true);

					if (checked) {
						$(this).val(getNewPrice($(this).data('original-value'), 2));
					} else {
						$(this).val($(this).data('original-value'));
					}
				});
			});

			$('.topping-checkbox').on('change', function() {
				// fetch new status
				const checked = $(this).is(':checked');

				// toggle readonly for inputs
				const priceInput = $(this).closest('tr')
					.find('input[type="number"]')
						.prop('readonly', checked ? false : true);

				if (checked) {
					priceInput.val(getNewPrice(priceInput.data('original-value'), 2));
				} else {
					priceInput.val(priceInput.data('original-value'));
				}

				const table = $(this).closest('.inspector-selection-table');

				// check if there are any unchecked fields
				const has_unchecked = table.find('.topping-checkbox:not(:checked)').length ? true : false;

				// toggle group checkbox status
				table.find('.toppings-group-checkbox')
					.prop('checked', has_unchecked ? false : true);
			});

			$('#price_alter_type').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '100%',
			});

			$('#price_alter_type').on('change', () => {
				$('.topping-checkbox:checked').trigger('change');
			});
		});
	})(jQuery, window);
</script>