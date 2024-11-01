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
 * @var  object  $product  The product details.
 * @var  object  $item     The selected cart item data.
 */
extract($displayData);

/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
$formFactory = VREFactory::getPlatform()->getFormFactory();

?>

<!-- DETAILS -->

<div class="inspector-fieldset">

	<h3><?php echo JText::translate('JDETAILS'); ?></h3>

	<!-- NAME - Text -->

	<?php
	$field = $formFactory->createField()
		->type('text')
		->id('item_product_name')
		->value($product->name)
		->readonly($product->id)
		->required(!$product->id)
		->label(JText::translate('VRMANAGEMENUSPRODUCT2'));

	if ($product->stock !== null)
	{
		$field->description(JText::translate($product->options ? 'VRMANAGETKCARTSTOCK_VAR_HELP' : 'VRMANAGETKCARTSTOCK_HELP'));

		// use product stock by default
		$stock = $product->stock;

		foreach ($product->options as $option)
		{
			if ($option->id == $item->id_product_option)
			{
				// overwrite with variation stock
				$stock = $option->stock;
			}
		}

		// display stocks next to the input
		$suffix = '<i class="fas fa-archive"></i> <span id="item_stock">' . $stock . '</span>';
	}

	echo $field->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer($suffix ?? null));
	?>

	<!-- VARIATION - Select -->

	<?php
	$optionsLookup = [];
	$selectedOptionPrice = 0;

	if ($product->options)
	{
		foreach ($product->options as $opt)
		{
			$optionsLookup[$opt->id] = [
				'price' => (float) $opt->inc_price,
				'stock' => $opt->stock,
			];

			if ($opt->id == $item->id_product_option)
			{
				$selectedOptionPrice = (float) $opt->inc_price;
			}
		}

		echo $formFactory->createField()
			->type('select')
			->id('item_id_product_option')
			->value($item->id_product_option)
			->required(true)
			->label(JText::translate('VRTKCARTOPTION5'))
			->options(array_map(function($opt)
			{
				return JHtml::fetch('select.option', $opt->id, $opt->name);
			}, $product->options));
	}
	?>

	<!-- UNITS - Number -->

	<?php
	echo $formFactory->createField()
		->type('number')
		->id('item_quantity')
		->value($item->quantity)
		->label(JText::translate('VRMANAGETKRES20'))
		->min(1)
		->step(1)
		->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRE_PIECES_SHORT')))
	?>

	<!-- PRICE - Number -->

	<?php
	echo $formFactory->createField()
		->type('number')
		->id('item_price')
		->value($item->price)
		->label(JText::translate('VRMANAGEMENUSPRODUCT4'))
		->min(0)
		->step('any')
		->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
			'before' => VREFactory::getCurrency()->getSymbol(),
		]));
	?>

	<input type="hidden" id="item_discount" value="<?php echo (float) $item->discount; ?>" />

	<input type="hidden" id="item_id" value="<?php echo (int) $item->id; ?>" />
	<input type="hidden" id="item_id_product" value="<?php echo (int) $item->id_product; ?>" />

</div>

<!-- TOPPINGS -->

<?php
$allToppingsPricesLookup = [];
$selectedToppingsGroupPricesLookup = [];

if ($product->toppings): ?>
	<div class="inspector-fieldset">

		<h3><?php echo JText::translate('VRMENUTAKEAWAYTOPPINGS'); ?></h3>

		<?php
		// iterate topping groups
		foreach ($product->toppings as $group)
		{
			$suitable = in_array($group->id_variation, [0, $item->id_product_option]);

			$badges = '';

			if ($group->multiple)
			{
				if ($group->max_toppings)
				{
					$badges .= '<span class="badge badge-important pull-right" style="margin-left:4px;">' . JText::sprintf('VRE_MAX_N', $group->max_toppings) . '</span>';
				}

				if ($group->min_toppings)
				{
					$badges .= '<span class="badge badge-warning pull-right" style="margin-left:4px;">' . JText::sprintf('VRE_MIN_N', $group->min_toppings) . '</span>';
				}
			}

			// prepare field control
			$field = $formFactory->createField()
				->label((strip_tags($group->description) ?: $group->title) . $badges)
				->required($group->min_toppings > 0)
				->control([
					'class'          => 'toppings-control',
					'data-id'        => $group->id,
					'data-variation' => $group->id_variation,
					'style'          => $suitable ? '' : 'display: none;',
				]);

			if (!$group->multiple)
			{
				// fetch selected topping
				$selected = !empty($item->toppings[$group->id]) ? key($item->toppings[$group->id]) : null;

				foreach ($group->toppings as $topping)
				{
					$allToppingsPricesLookup[(int) $topping->id_topping] = (float) $topping->rate;

					// increase map price with selected topping only if suitable
					if ($suitable && isset($item->toppings[$group->id][$topping->id_topping]))
					{
						$selectedToppingsGroupPricesLookup[(int) $group->id] = (float) $topping->rate;
					}
				}

				// single selection
				$field->type('select');
				$field->value($selected);
				$field->class('entry_group_toppings');
				$field->options(array_map(function($topping) {
					$toppingName = $topping->name;

					if ($topping->rate != 0)
					{
						$toppingName .= ' : ' . VREFactory::getCurrency()->format($topping->rate);
					}

					return JHtml::fetch('select.option', $topping->id_topping, $toppingName);
				}, $group->toppings));

				echo $field->render();
			}
			else
			{
				// multiple selection
				echo $field->render(function($data, $input) use ($group, $item) {
					?>
					<div class="toppings-group<?php echo $group->use_quantity ? ' use-quantity' : ''; ?>">
						<?php
						foreach ($group->toppings as $topping)
						{
							$checked = '';

							// check if the topping has been checked
							if (isset($item->toppings[$group->id][$topping->id_topping]))
							{
								$checked = 'checked="checked"';

								// use specified number of units
								$units = $item->toppings[$group->id][$topping->id_topping];
							}
							else
							{
								$units = 0;
							}

							?>
							<span>
								<input type="checkbox" value="<?php echo (int) $topping->id_topping; ?>" data-rate="<?php echo (float) $topping->rate; ?>" id="topping-<?php echo (int) $group->id; ?>-<?php echo (int) $topping->id_topping; ?>" <?php echo $checked; ?> />
								<label for="topping-<?php echo (int) $group->id; ?>-<?php echo (int) $topping->id_topping; ?>"><?php echo $topping->name; ?></label>
								
								<?php if ($group->use_quantity): ?>
									<span class="topping-quantity pull-right" data-units="<?php echo (int) $units; ?>">
										<a href="javascript: void(0);" class="topping-del-unit<?php echo (int) $units > 1 ? '' : ' disabled'; ?>">
											<i class="fas fa-minus-circle medium-big"></i>
										</a>

										<span class="topping-units"><?php echo $units; ?></span>

										<a href="javascript: void(0);" class="topping-add-unit<?php echo $units > 0 ? '' : ' disabled'; ?>">
											<i class="fas fa-plus-circle medium-big"></i>
										</a>
									</span>
								<?php endif; ?>

								<?php if ($topping->rate != 0): ?>
									<span class="badge badge-info pull-right"><?php echo VREFactory::getCurrency()->format($topping->rate); ?></span>
								<?php endif; ?>
							</span>
							<?php
						}
						?>
					</div>
					<?php
				});
			}
		}
		?>

	</div>
<?php endif; ?>

<!-- NOTES -->

<div class="inspector-fieldset">

	<h3><?php echo JText::translate('VRMANAGETKRESTITLE4'); ?></h3>

	<!-- NOTES - Textarea -->

	<?php
	echo $formFactory->createField()
		->type('textarea')
		->id('item_notes')
		->value($item->notes)
		->height(100)
		->style('resize: vertical;')
		->hiddenLabel(true);
	?>

</div>

<script>
	(function($, w) {
		'use strict';

		const optionsLookup = <?php echo json_encode($optionsLookup); ?>;
		let selectedOptionPrice = <?php echo (float) $selectedOptionPrice; ?>;

		// keep relation of current toppings costs (single-selection only)
		let selectedToppingsGroupPricesLookup = <?php echo json_encode($selectedToppingsGroupPricesLookup); ?>;
		const allToppingsPricesLookup = <?php echo json_encode($allToppingsPricesLookup); ?>;

		const updateItemPrice = (add) => {
			// get current price
			let price = parseFloat($('#item_price').val());

			// increase price
			price += add;

			// round price to avoid sum/diff errors
			price = price.roundTo(2);

			// update price and make sure the cost is not lower than 0
			$('#item_price').val(Math.max(0, price));
		}

		const getToppingUnits = (topping) => {
			const unitsBox = $(topping).siblings('.topping-quantity');

			if (unitsBox.length) {
				return parseInt(unitsBox.attr('data-units'));	
			}
			
			return 1;
		}

		const addToppingUnits = (topping, units) => {
			// find units box
			const unitsBox = $(topping).siblings('.topping-quantity');

			if (unitsBox.length == 0) {
				// the topping doesn't support the units selection
				return false;
			}

			// increase/decrease units by the specified amount
			units = getToppingUnits(topping) + units;

			// update picked units
			unitsBox.attr('data-units', units);
			unitsBox.find('.topping-units').text(units);

			if (units <= 1) {
				unitsBox.find('.topping-del-unit').addClass('disabled');
			} else {
				unitsBox.find('.topping-del-unit').removeClass('disabled');
			}

			if (units <= 0) {
				unitsBox.find('.topping-add-unit').addClass('disabled');
			} else {
				unitsBox.find('.topping-add-unit').removeClass('disabled');
			}

			return true;
		}

		w.fetchToppingsGroups = (data) => {
			data.groups = [];

			// iterate controls
			$('.toppings-control').each(function() {
				// consider only the groups with matching variation ID
				const groupOptionId = parseInt($(this).data('variation'));

				if (groupOptionId == 0 || groupOptionId == data.id_product_option) {
					// fetch group
					const group = {
						id: parseInt($(this).data('id')),
						toppings: [],
						units: {},
					};

					if ($(this).find('select').length) {
						// get topping from select
						let id_topping = parseInt($(this).find('select').select2('val'));

						// add only if not empty
						if (!isNaN(id_topping) && id_topping > 0) {
							group.toppings.push(id_topping);
						}
					} else {
						// get toppings from checked inputs
						$(this).find('input:checked').each(function() {
							// get topping ID
							let id_topping = parseInt($(this).val());

							// register topping
							group.toppings.push(id_topping);

							// look for the topping units
							let units = getToppingUnits(this);

							// register units
							group.units[id_topping] = units;
						});
					}

					if (group.toppings.length) {
						data.groups.push(group);
					}
				}
			});

			if ($('#item_stock').length) {
				// get stock
				data.stock = parseInt($('#item_stock').text());

				if (data.id > 0) {
					// increase stock by the original quantity in case of update
					data.stock += <?php echo $item->quantity; ?>;
				}
			}
		}

		w.assignToppingsGroups = (data) => {
			if (!data.groups) {
				return;
			}

			// IMPORTANT: triggering the "change" event will result in a wrong price calculation.
			// We should rather adjust the lookup containing all the selected toppings.

			// reset selected toppings lookup
			selectedToppingsGroupPricesLookup = {};

			// unselect all the toppings
			$('.toppings-control input:checked').prop('checked', false);

			data.groups.forEach((group) => {
				const control = $('.toppings-control[data-id="' + group.id + '"]');

				if ($(control).find('select').length) {
					// select the first topping in the list
					$(control).find('select').select2('val', group.toppings[0]);

					if (allToppingsPricesLookup.hasOwnProperty(group.toppings[0])) {
						// update lookup
						selectedToppingsGroupPricesLookup[group.id] = allToppingsPricesLookup[group.toppings[0]];
					}
				} else {
					group.toppings.forEach((toppingId) => {
						const checkbox = $(control).find('input[value="' + toppingId + '"]');
						checkbox.prop('checked', true);

						if (group.units.hasOwnProperty(toppingId)) {
							addToppingUnits(checkbox, group.units[toppingId] - 1);
						}
					});
				}
			});
		}

		$('select#item_id_product_option, select.entry_group_toppings').select2({
			allowClear: false,
			width: '100%',
		});

		$('select#item_id_product_option').on('change', function() {
			let optionId = parseInt($(this).val());

			// iterate toppings groups and toggle them according
			// to the variation that has been selected
			$('.toppings-control').each(function() {
				// get group variation ID
				const groupOptionId = parseInt($(this).data('variation'));

				// show toppings group only if available for all variations or if
				// the selected variation matches the one specified for the item
				if (groupOptionId == 0 || groupOptionId == optionId) {
					$(this).show();
				} else {
					$(this).hide();

					// when a group of checkboxes is no more suitable for the selected variation,
					// turn off all the checked toppings and trigger the "change" event to unset
					// their cost from the product price
					$(this).find('.toppings-group input[type="checkbox"]:checked').prop('checked', false).trigger('change');
				}

				// trigger toppings change to refresh price in case the group is
				// no more suitable for the selected variation and vice-versa
				$(this).find('select.entry_group_toppings').trigger('change');
			});


			// subtract the cost of the previously selected option
			let sub = selectedOptionPrice;
			let add = 0;

			if (optionsLookup.hasOwnProperty(optionId)) {
				// track the new option cost 
				selectedOptionPrice = optionsLookup[optionId].price;

				// increase price by the cost of the newly selected option
				add = selectedOptionPrice;

				// update stocks, if supported
				if (optionsLookup[optionId].stock !== null) {
					$('#item_stock').text(optionsLookup[optionId].stock);
				}
			}

			// commit changes
			updateItemPrice(add - sub);
		});

		$('.entry_group_toppings').on('change', function() {
			let rate = 0;
			let prev = 0;

			const toppingId = parseInt($(this).select2('val'));
			const groupId   = parseInt($(this).closest('.toppings-control').data('id'));

			if (allToppingsPricesLookup.hasOwnProperty(toppingId)) {
				rate = allToppingsPricesLookup[toppingId];
			}

			// get toppings group ID variation
			const groupOptionId = parseInt($(this).closest('*[data-variation]').data('variation'));
			// get selected variation
			const selectedOptionId = parseInt($('#item_id_product_option').select2('val'));

			// unset topping rate in case the group is not suitable for the selected variation
			if (groupOptionId != 0 && groupOptionId != selectedOptionId) {
				rate = 0;
			}

			// get previous topping rate
			if (selectedToppingsGroupPricesLookup.hasOwnProperty(groupId)) {
				// decrease rate by the cost of the previously selected topping
				prev = parseFloat(selectedToppingsGroupPricesLookup[groupId]);
			}

			// update map with new rate
			selectedToppingsGroupPricesLookup[groupId] = rate;

			// increase price by the rate of the selected topping
			// and decrease by the rate of the previous one
			updateItemPrice(rate - prev);
		});

		$('.toppings-group input[type="checkbox"]').on('change', function() {
			// get topping price
			let rate = parseFloat($(this).data('rate'));
			// set units to specify
			let units = 1

			if ($(this).is(':checked') == false) {
				// find current units
				units = getToppingUnits(this);

				// topping unchecked, decrease rate instead
				rate *= units * -1;

				// unset units
				units *= -1;
			}

			// update picked units
			addToppingUnits(this, units);

			// increase/decrease price by the rate of the checked/unchecked topping
			updateItemPrice(rate);
		});

		$('.topping-del-unit, .topping-add-unit').on('click', function() {
			if ($(this).hasClass('disabled')) {
				return false;
			}

			// choose whether we need to increase or decrease the item price by the topping cost
			const factor = $(this).hasClass('topping-del-unit') ? -1 : 1;

			// find topping
			const topping = $(this).closest('.topping-quantity').siblings('input[type="checkbox"]');

			// increase/decrease units by one
			addToppingUnits(topping, factor);

			// get topping price
			let rate = parseFloat($(topping).data('rate'));

			// increase/decrease price by the rate of the topping
			updateItemPrice(rate * factor);
		});
	})(jQuery, window);
</script>