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

$optLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-cards-container cards-tax-rules" id="cards-tax-rules">

	<?php
	foreach ($this->tax->rules as $i => $rule)
	{
		?>
		<div class="vre-card-fieldset up-to-1" id="tax-rule-fieldset-<?php echo (int) $i; ?>">

			<?php
			$displayData = array();

			// reduce card size
			$displayData['class'] = 'compress';

			// fetch primary text
			$displayData['primary'] = $rule->name;

			// fetch edit button
			$displayData['edit'] = 'vreOpenTaxRuleCard(\'' . $i . '\');';

			// render layout
			echo $optLayout->render($displayData);
			?>
			
			<input type="hidden" name="rule_json[]" value="<?php echo $this->escape(json_encode($rule)); ?>" />

		</div>
		<?php
	}

	/**
	 * Preserve the deleted rules in case of failure.
	 * 
	 * @since 1.9
	 */
	if (!empty($this->tax->deleted_rules))
	{
		foreach ($this->tax->deleted_rules as $deleted)
		{
			?><input type="hidden" name="rule_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}
	?>

	<!-- ADD PLACEHOLDER -->

	<div class="vre-card-fieldset up-to-1 add add-tax-rule">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="tax-rule-struct">
			
	<?php
	// create structure for records
	$displayData = array();
	$displayData['class']   = 'compress';
	$displayData['primary'] = '';
	$displayData['edit']    = true;

	echo $optLayout->render($displayData);
	?>

</div>

<script>
	(function($) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo count($this->tax->rules); ?>;
		let SELECTED_OPTION = null;

		$(function() {
			// open inspector for new rules
			$('.vre-card-fieldset.add-tax-rule').on('click', () => {
				vreOpenTaxRuleCard();
			});

			$('#cards-tax-rules').sortable({
				// exclude "add" boxes
				items: '.vre-card-fieldset:not(.add)',
				// hide "add" box when sorting starts
				start: () => {
					$('.vre-card-fieldset.add-tax-rule').hide();
				},
				// show "add" box again when sorting stops
				stop: () => {
					$('.vre-card-fieldset.add-tax-rule').show();
				},
			});

			// fill the form before showing the inspector
			$('#tax-rule-inspector').on('inspector.show', () => {
				let json = [];

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					json = fieldset.find('input[name="rule_json[]"]').val();

					try {
						json = JSON.parse(json);
					} catch (err) {
						json = {};
					}
				}

				if (json.id === undefined) {
					// creating new record, hide delete button
					$('#tax-rule-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#tax-rule-inspector [data-role="delete"]').show();
				}

				fillTaxRuleForm(json);
			});

			// apply the changes
			$('#tax-rule-inspector').on('inspector.save', function() {
				// validate form
				if (!ruleValidator.validate()) {
					return false;
				}

				// get saved record
				const data = getTaxRuleData();

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddTaxRuleCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="rule_json[]"]').val(JSON.stringify(data));

				// refresh card details
				vreRefreshTaxRuleCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			// delete the record
			$('#tax-rule-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="rule_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="rule_deleted[]" value="' + json.id + '" />');
				}

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});
		});

		window['vreOpenTaxRuleCard'] = (index) => {
			if (typeof index !== 'undefined') {
				SELECTED_OPTION = 'tax-rule-fieldset-' + index;
			} else {
				SELECTED_OPTION = null;
			}

			// open inspector
			vreOpenInspector('tax-rule-inspector');
		}

		const vreAddTaxRuleCard = (data) => {
			let index = OPTIONS_COUNT++;

			SELECTED_OPTION = 'tax-rule-fieldset-' + index;

			var html = $('#tax-rule-struct').clone().html();

			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-1" id="tax-rule-fieldset-' + index + '">' + html + '</div>'
			).insertBefore($('.vre-card-fieldset.add-tax-rule').last());

			// get created fieldset
			let fieldset = $('#' + SELECTED_OPTION);

			fieldset.vrecard('edit', 'vreOpenTaxRuleCard(' + index + ')');

			// create input to hold JSON data
			let input = $('<input type="hidden" name="rule_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshTaxRuleCard = (elem, data) => {
			// update primary text
			elem.vrecard('primary', data.name);
		}
	})(jQuery);
</script>
