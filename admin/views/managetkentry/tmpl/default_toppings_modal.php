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

$vik = VREApplication::getInstance();

$currency = VREFactory::getCurrency();

?>

<div class="inspector-form" id="inspector-group-form">

	<?php echo $vik->bootStartTabSet('tkgroup', ['active' => 'tkgroup_details']); ?>

		<!-- GROUP DETAILS -->

		<?php echo $vik->bootAddTab('tkgroup', 'tkgroup_details', JText::translate('JDETAILS')); ?>

			<div class="inspector-fieldset">
			
				<!-- GROUP TITLE - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('group_title')
					->required(true)
					->label(JText::translate('VRTKMANAGEENTRYGROUP1'))
					->description(JText::translate('VRTKMANAGEENTRYGROUPTITLE_HELP'));
				?>

				<!-- GROUP DESCRIPTION - Textarea -->

				<?php
				echo $this->formFactory->createField()
					->type('editor')
					->name('group_description')
					->label(JText::translate('VRMANAGETKMENU2'))
					->description(JText::translate('VRTKMANAGEENTRYGROUPDESC_HELP'))
					->render(function($data, $input) {
						// wrap editor within a form in order to avoid TinyMCE errors
						return '<form>' . $input . '</form>';
					});
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- GROUP PROPERTIES -->

		<?php echo $vik->bootAddTab('tkgroup', 'tkgroup_properties', JText::translate('VRMAPPROPERTIESBUTTON')); ?>

			<div class="inspector-fieldset">

				<!-- GROUP VARIATION - Select -->

				<?php
				echo $this->formFactory->createField()
					->type('select')
					->id('group_variation')
					->label(JText::translate('VRTKMANAGEENTRYGROUP5'))
					->options([JHtml::fetch('select.option', '', '')])
					->control(['class' => 'variation-control']);
				?>

				<!-- GROUP MULTIPLE - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('group_multiple')
					->label(JText::translate('VRTKMANAGEENTRYGROUP2'));
				?>

				<!-- GROUP MINIMUM - Number -->

				<?php
				echo $this->formFactory->createField()
					->type('number')
					->id('group_min_toppings')
					->label(JText::translate('VRTKMANAGEENTRYGROUP3'))
					->min(0)
					->step(1);
				?>

				<!-- GROUP MAXIMUM - Number -->

				<?php
				echo $this->formFactory->createField()
					->type('number')
					->id('group_max_toppings')
					->label(JText::translate('VRTKMANAGEENTRYGROUP4'))
					->min(1)
					->step(1);
				?>

				<!-- GROUP USE QUANTITY - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('group_use_quantity')
					->label(JText::translate('VRTKMANAGEENTRYGROUP6'))
					->description(JText::translate('VRTKMANAGEENTRYGROUP6_DESC'))
					->control(['class' => 'show-with-multiple']);
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- GROUP TOPPINGS -->

		<?php echo $vik->bootAddTab('tkgroup', 'tkgroup_toppings', JText::translate('VRMENUTAKEAWAYTOPPINGS')); ?>

			<div class="inspector-fieldset">

				<!-- TOPPINGS SELECTION - Select -->
				<div class="control-group">
					<div class="vrtk-toppings-list">
						<?php
						foreach (JHtml::fetch('vrehtml.admin.tktoppings') as $separator)
						{
							JHtml::fetch('vrehtml.scripts.sortablelist', 'separatorList' . $separator->id, 'adminForm');

							?>
							<table class="inspector-selection-table" id="separatorList<?php echo $separator->id; ?>">

								<thead>
									<tr>
										<th width="8%" style="text-align: left;">
											<input type="checkbox" class="toppings-group-checkbox" id="separator-topping-checkbox-<?php echo (int) $separator->id; ?>" />
										</th>
										
										<th width="52%" style="text-align: left;">
											<label for="separator-topping-checkbox-<?php echo (int) $separator->id; ?>">
												<strong><?php echo $separator->title; ?></strong>
											</label>
										</th>
										
										<th width="40%" style="text-align: left;">
											<?php echo JText::translate('VRMANAGETKTOPPING2'); ?>
										</th>
									</tr>
								</thead>

								<tbody>
									<?php
									foreach ($separator->toppings as $topping)
									{
										?>
										<tr data-id="<?php echo (int) $topping->id; ?>" data-ordering="<?php echo (int) $topping->ordering; ?>">
											<td>
												<input type="checkbox" value="<?php echo (int) $topping->id; ?>" class="topping-checkbox" id="topping-check-<?php echo (int) $topping->id; ?>" />
												<input type="hidden" value="0" class="topping-assoc" />
											</td>

											<td>
												<label for="topping-check-<?php echo (int) $topping->id; ?>">
													<strong><?php echo $topping->name; ?></strong>
												</label>
											</td>

											<td>
												<?php
												echo $this->formFactory->createField()
													->type('number')
													->class('topping-rate')
													->readonly(true)
													->step('any')
													->hidden(true)
													->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
														'before' => $currency->getSymbol(),
													]))
												?>

												<input type="hidden" value="<?php echo (float) $topping->price; ?>" class="topping-default-rate" />
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

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>

	<input type="hidden" id="group_id" class="field" value="" />

</div>

<?php
JText::script('VRTKGROUPVARPLACEHOLDER');
JText::script('VRTKTOPPINGSPLACEHOLDER');
?>

<script>
	(function($, w) {
		'use strict';

		w.fillTkentryGroupForm = (data) => {
			// update title
			if (data.title === undefined) {
				data.title = '';
			}

			$('#group_title').val(data.title);

			groupValidator.unsetInvalid($('#group_title'));

			// update description
			Joomla.editors.instances.group_description.setValue(data.description ? data.description : '');

			// update variation
			const variationSelect = $('#group_variation');

			if (!data.id_variation) {
				data.id_variation = '';
			}

			// rebuild variations dropdown because the user might have updated them
			variationSelect.html('<option></option>');

			const variations = getSupportedTkentryVariations();

			if (variations.length) {
				variations.forEach((variation) => {
					let opt = $('<option></option>').val(variation.id).text(variation.name);

					if (variation.id == 0) {
						// disable variation because we don't know yet the real ID that will be
						// saved into the database, needed for the group-variation assignment
						opt.prop('disabled', true);
					}

					variationSelect.append(opt);
				});

				variationSelect.select2('val', data.id_variation);
				$('.variation-control').show();
			} else {
				// variations not specified for this product, hide field
				$('.variation-control').hide();
				variationSelect.select2('val', null);
			}

			// update multiple
			if (data.multiple === undefined) {
				data.multiple = true;
			} else if (typeof data.multiple === 'string') {
				data.multiple = parseInt(data.multiple);
			}

			$('#group_multiple').prop('checked', data.multiple);

			// update minimum toppings
			if (data.min_toppings === undefined) {
				data.min_toppings = 0;
			}

			$('#group_min_toppings')
				.val(data.multiple ? data.min_toppings : 1)
				.prop('readonly', !data.multiple);

			// update maximum toppings
			if (data.max_toppings === undefined) {
				data.max_toppings = 1;
			}

			$('#group_max_toppings')
				.val(data.multiple ? data.max_toppings : 1)
				.prop('readonly', !data.multiple);

			// update use quantity
			if (data.use_quantity === undefined) {
				data.use_quantity = 0;
			} else if (typeof data.use_quantity === 'string') {
				data.use_quantity = parseInt(data.use_quantity);
			}

			$('#group_use_quantity').prop('checked', data.use_quantity);

			if (data.multiple) {
				$('.show-with-multiple').show();
			} else {
				$('.show-with-multiple').hide();
			}

			// update ID
			if (data.id === undefined) {
				data.id = 0;
			}

			$('#group_id').val(data.id);

			// uncheck all toppings
			$('.toppings-group-checkbox').prop('checked', false).trigger('change');
			// unset all associations
			$('.topping-assoc').val(0);
			// restore all rates
			$('.topping-default-rate').each(function() {
				$(this).closest('tr').find('.topping-rate').val($(this).val());
			});

			// update toppings
			if (data.toppings === undefined) {
				data.toppings = [];
			}

			for (let i = 0; i < data.toppings.length; i++) {
				let topping = data.toppings[i];

				// find topping row
				let tr = $('tr[data-id="' + topping.id_topping + '"]');

				// set association ID
				tr.find('.topping-assoc').val(topping.id);

				// check topping
				tr.find('.topping-checkbox').prop('checked', true).trigger('change');

				// update rate
				tr.find('.topping-rate').val(topping.rate);
			}

			restoreToppingsOrdering(data.toppings);

			if (!data.id) {
				// always fallback to default details tab
				$('a[href="#tkgroup_details"]').trigger('click');
				// for J4 compatibility
				$('body.com_vikrestaurants joomla-tab button[aria-controls="tkgroup_details"]').trigger('click');
			}
		}

		w.getTkentryGroupData = () => {
			let data = {};

			// set ID
			data.id = $('#group_id').val();

			// set title
			data.title = $('#group_title').val();

			// set description
			data.description = Joomla.editors.instances.group_description.getValue();

			// set variation
			data.id_variation = $('#group_variation').val();

			if (!data.id_variation) {
				data.id_variation = 0;
			}

			// set multiple
			data.multiple = $('#group_multiple').is(':checked') ? 1 : 0;

			if (data.multiple) {
				// set minimum toppings
				data.min_toppings = parseInt($('#group_min_toppings').val());

				// set maximum toppings
				data.max_toppings = parseInt($('#group_max_toppings').val());

				// set use quantity
				data.use_quantity = $('#group_use_quantity').is(':checked') ? 1 : 0;
			} else {
				data.min_toppings = 1;
				data.max_toppings = 1;
				data.use_quantity = 0;
			}

			// set toppings
			data.toppings = [];

			let usedToppings = [];

			$('.topping-checkbox:checked').each(function() {
				// get parent row
				const row = $(this).closest('tr');

				let id_top = parseInt($(this).val());

				// Do not take topping if it was already added.
				// Duplicate toppings might occur when clicking the
				// save button quickly after rearranging the records.
				if (usedToppings.indexOf(id_top) == -1) {
					// register topping
					data.toppings.push({
						id_topping: id_top,
						id:        row.find('.topping-assoc').val(),
						rate:      row.find('.topping-rate').val(),
					});

					// mark topping as already used
					usedToppings.push(id_top);
				}
			});

			return data;
		}

		const restoreToppingsOrdering = (toppings) => {
			let lookup = {};

			// create toppings ordering lookup
			for (let i = 0; i < toppings.length; i++) {
				lookup[toppings[i].id_topping] = i + 1;
			}

			$('#inspector-group-form').find('.inspector-selection-table').each(function() {
				const tableBody = $(this).find('tbody');

				$(tableBody).children().detach().sort((a, b) => {
					// get IDs
					let aID = $(a).data('id');
					let bID = $(b).data('id');

					let x, y;

					// get values to compare
					if (lookup.hasOwnProperty(aID) && lookup.hasOwnProperty(bID)) {
						// compare assoc ordering
						x = lookup[aID];
						y = lookup[bID];
					} else if (!lookup.hasOwnProperty(aID) && !lookup.hasOwnProperty(bID)) {
						// compare toppings ordering
						x = parseInt($(a).data('ordering'));
						y = parseInt($(b).data('ordering'));
					} else {
						// push unchecked toppings down (higher value)
						x = lookup.hasOwnProperty(aID) ? 0 : 1;
						y = lookup.hasOwnProperty(bID) ? 0 : 1;
					}

					if (x < y) {
						// A is lower than B
						return -1;
					} else if (x > y) {
						// A is higher than B
						return 1;
					}

					return 0;
				}).appendTo(tableBody);
			});
		}

		$(function() {
			w.groupValidator = new VikFormValidator('#inspector-group-form');

			$('#group_variation').select2({
				placeholder: Joomla.JText._('VRTKGROUPVARPLACEHOLDER'),
				allowClear: true,
				width: '100%',
			});

			$('#adminForm').on('submit', () => {
				const editor = Joomla.editors.instances.group_description;

				if (editor.onSave) {
					editor.onSave();
				}
			});

			$('#group_multiple').on('change', function() {
				if ($(this).is(':checked')) {
					$('#group_min_toppings, #group_max_toppings')
						.prop('readonly', false);

					$('.show-with-multiple').show();
				} else {
					$('#group_min_toppings, #group_max_toppings')
						.val(1)
						.prop('readonly', true);

					$('.show-with-multiple').hide();
				}
			});

			$('.toppings-group-checkbox').on('change', function() {
				// fetch new status
				const checked = $(this).is(':checked');

				const table = $(this).closest('.inspector-selection-table');

				// toggle all table checkboxes
				table.find('.topping-checkbox')
					.prop('checked', checked);

				// toggle readonly for inputs
				table.find('tbody input[type="number"]')
					.prop('readonly', checked ? false : true);
			});

			$('.topping-checkbox').on('change', function() {
				// fetch new status
				const checked = $(this).is(':checked');

				// toggle readonly for inputs
				$(this).closest('tr')
					.find('input[type="number"]')
						.prop('readonly', checked ? false : true);

				const table = $(this).closest('.inspector-selection-table');

				// check if there are any unchecked fields
				const has_unchecked = table.find('.topping-checkbox:not(:checked)').length ? true : false;

				// toggle group checkbox status
				table.find('.toppings-group-checkbox')
					.prop('checked', has_unchecked ? false : true);
			});

			// validate min/max toppings
			groupValidator.addCallback(function() {
				// get inputs
				const minInput = $('#group_min_toppings');
				const maxInput = $('#group_max_toppings');

				groupValidator.unsetInvalid(minInput);
				groupValidator.unsetInvalid(maxInput);

				// get minimum
				let min = parseInt(minInput.val());

				// make sure the amount is a number
				if (isNaN(min) || min < 0) {
					groupValidator.setInvalid(minInput);

					return false;
				}

				// get maximum
				let max = parseInt(maxInput.val());

				// make sure the amount is a number
				if (isNaN(max) || max < 1) {
					groupValidator.setInvalid(maxInput);

					return false;
				}

				// make sure the minimum is not higher than the maximum
				if (min > max) {
					groupValidator.setInvalid(minInput);
					groupValidator.setInvalid(maxInput);

					return false;
				}

				return true;
			});
		});
	})(jQuery, window);
</script>
