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

$varLayout = new JLayoutFile('blocks.card');

?>
			
<div class="vre-cards-container cards-product-variations" id="cards-product-variations">

	<?php
	foreach ($this->product->options as $i => $option)
	{
		?>
		<div class="vre-card-fieldset up-to-1" id="product-var-fieldset-<?php echo (int) $i; ?>">

			<?php
			$displayData = array();

			// fetch card ID
			$displayData['id'] = 'product-var-card-' . $i;

			// fetch primary text
			$displayData['primary']  = $option->name;

			// fetch secondary text
			$displayData['secondary'] = '<span class="badge badge-info option-cost">' . $currency->format($option->inc_price) . '</span>';

			// fetch edit button
			$displayData['edit'] = 'vreOpenProductVariationCard(' . $i . ');';

			// render layout
			echo $varLayout->render($displayData);
			?>
			
			<input type="hidden" name="option_json[]" value="<?php echo $this->escape(json_encode($option)); ?>" />

		</div>
		<?php
	}

	/**
	 * Preserve the deleted options in case of failure.
	 * 
	 * @since 1.9
	 */
	if (!empty($this->product->deleted_options))
	{
		foreach ($this->product->deleted_options as $deleted)
		{
			?><input type="hidden" name="option_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}
	?>

	<!-- ADD PLACEHOLDER -->

	<div class="vre-card-fieldset up-to-1 add add-product-var">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="product-var-struct">
	
	<?php
	// create product option structure for new items
	$displayData = array();
	$displayData['id']        = 'product-var-card-{id}';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';
	$displayData['edit']      = true;

	echo $varLayout->render($displayData);
	?>

</div>

<?php
JText::script('VRE_ADD_VARIATION');
JText::script('VRE_EDIT_VARIATION');
?>

<script>
	(function($, w) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo count($this->product->options); ?>;
		let SELECTED_OPTION = null;

		$(function() {
			// open inspector for new variations
			$('.vre-card-fieldset.add-product-var').on('click', () => {
				vreOpenProductVariationCard();
			});

			$('#cards-product-variations').sortable({
				// exclude "add" boxs
				items: '.vre-card-fieldset:not(.add)',
				// hide "add" box when sorting starts
				start: function() {
					$('.vre-card-fieldset.add-product-var').hide();
				},
				// show "add" box again when sorting stops
				stop: function() {
					$('.vre-card-fieldset.add-product-var').show();
				},
			});

			// fill the form before showing the inspector
			$('#product-option-inspector').on('inspector.show', function() {
				let data = [];

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					data = fieldset.find('input[name="option_json[]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				if (data.id === undefined) {
					// creating new record, hide delete button
					$('#product-option-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#product-option-inspector [data-role="delete"]').show();
				}

				// fill the form with the retrieved data
				fillProductOptionForm(data);
			});

			$('#product-option-inspector').on('inspector.save', function() {
				// validate form
				if (!optionValidator.validate()) {
					return false;
				}

				// get updated product option data
				const data = getProductOptionData();

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddProductVariationCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="option_json[]"]').val(JSON.stringify(data));

				// refresh details shown in card
				vreRefreshProductVariationCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			$('#product-option-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="option_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="option_deleted[]" value="' + json.id + '" />');
				}

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});
		});

		w['vreOpenProductVariationCard'] = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRE_ADD_VARIATION');
				SELECTED_OPTION = null;
			} else {
				title = Joomla.JText._('VRE_EDIT_VARIATION');
				SELECTED_OPTION = 'product-var-fieldset-' + index;
			}
			
			// open inspector
			vreOpenInspector('product-option-inspector', {title: title});
		}

		const vreAddProductVariationCard = (data) => {
			let index = OPTIONS_COUNT++;

			SELECTED_OPTION = 'product-var-fieldset-' + index;

			let html = $('#product-var-struct').clone().html();
			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-1" id="' + SELECTED_OPTION + '">' + html + '</div>'
			).insertBefore('.vre-card-fieldset.add-product-var');

			// get created fieldset
			const fieldset = $('#' + SELECTED_OPTION);

			fieldset.vrecard('edit', 'vreOpenProductVariationCard(' + index + ')');

			// create input to hold JSON data
			const input = $('<input type="hidden" name="option_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshProductVariationCard = (elem, data) => {
			// update primary text
			elem.vrecard('primary', data.name);

			// update secondary text
			const secondary = '<span class="badge badge-info option-cost">' + Currency.getInstance().format(data.inc_price) + '</span>';
			elem.vrecard('secondary', secondary);
		}
	})(jQuery, window);
</script>
